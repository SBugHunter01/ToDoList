<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class TaskController extends Controller
{
    // Constants for magic numbers
    private const MAX_BULK_OPERATIONS = 50;
    private const MAX_BULK_DELETE_OPERATIONS = 20;
    private const DEFAULT_ITEMS_PER_PAGE = 20;
    private const MAX_ITEMS_PER_PAGE = 100;
    private const RATE_LIMIT_BULK_UPDATE = 5;
    private const RATE_LIMIT_BULK_DELETE = 3;
    private const RATE_LIMIT_TASK_CREATE = 10;
    private const RATE_LIMIT_WINDOW = 60; // seconds

    /**
     * Helper method for consistent error responses
     */
    private function errorResponse(string $message, int $code = 500, array $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Get all tasks for the authenticated user.
     * Returns tasks with category relationships for dashboard display.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = auth()->user()->tasks()
                ->with(['category:id,name,color']);

            // Enhanced filtering and searching
            $this->applyFilters($query, $request);

            // Enhanced sorting with priority and status ordering
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');

            // Validate sort parameters to prevent SQL injection
            $allowedSortColumns = ['id', 'title', 'description', 'completed', 'status', 'priority', 'created_at', 'updated_at'];
            $allowedSortDirections = ['asc', 'desc'];

            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'created_at';
            }
            if (!in_array($sortDirection, $allowedSortDirections)) {
                $sortDirection = 'desc';
            }

            // Handle complex sorting
            if ($sortBy === 'priority') {
                // Sort by priority level first, then by created_at
                // Using CASE statement for SQLite compatibility (FIELD is MySQL-only)
                $query->orderByRaw("CASE priority 
                    WHEN 'urgent' THEN 1 
                    WHEN 'high' THEN 2 
                    WHEN 'medium' THEN 3 
                    WHEN 'low' THEN 4 
                    END {$sortDirection}")
                      ->orderBy('created_at', 'desc');
            } elseif ($sortBy === 'status') {
                // Sort by status workflow order first, then by created_at
                // Using CASE statement for SQLite compatibility (FIELD is MySQL-only)
                $query->orderByRaw("CASE status 
                    WHEN 'pending' THEN 1 
                    WHEN 'in_progress' THEN 2 
                    WHEN 'completed' THEN 3 
                    WHEN 'cancelled' THEN 4 
                    END {$sortDirection}")
                      ->orderBy('created_at', 'desc');
            } elseif ($sortBy === 'category') {
                // Sort by category name, then by created_at
                // Use LEFT JOIN to include tasks without category
                $query->leftJoin('categories', 'tasks.category_id', '=', 'categories.id')
                      ->orderBy('categories.name', $sortDirection)
                      ->orderBy('tasks.created_at', 'desc')
                      ->select('tasks.*');
            } else {
                // Default sorting
                $query->orderBy($sortBy, $sortDirection);
            }

            // Add pagination for better performance
            $perPage = min($request->get('per_page', self::DEFAULT_ITEMS_PER_PAGE), self::MAX_ITEMS_PER_PAGE);
            $tasks = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $tasks->items(),
                'meta' => [
                    'total' => $tasks->total(),
                    'per_page' => $tasks->perPage(),
                    'current_page' => $tasks->currentPage(),
                    'last_page' => $tasks->lastPage(),
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection,
                    'filters' => $this->getAppliedFilters($request)
                ],
                'links' => [
                    'first' => $tasks->url(1),
                    'last' => $tasks->url($tasks->lastPage()),
                    'prev' => $tasks->previousPageUrl(),
                    'next' => $tasks->nextPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching tasks: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new task for the authenticated user.
     */
    public function store(TaskStoreRequest $request): JsonResponse
    {
        // Rate limiting: 10 tasks per minute per user
        $key = 'task-create:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, self::RATE_LIMIT_TASK_CREATE)) {
            return $this->errorResponse('Too many task creation attempts. Please wait before creating more tasks.', 429);
        }
        RateLimiter::hit($key, self::RATE_LIMIT_WINDOW);

        try {
            $task = auth()->user()->tasks()->create([
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'status' => $request->status ?? 'pending',
                'priority' => $request->priority ?? 'medium',
                'completed' => false
            ]);

            return response()->json([
                'success' => true,
                'data' => $task->load('category:id,name,color'),
                'message' => 'Task created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating task: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified task with validation.
     */
    public function show(string $id): JsonResponse
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid task ID format'
                ], 400);
            }
            
            $task = auth()->user()->tasks()
                ->with(['category:id,name,color'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }
    }

    /**
     * Update the specified task in storage dengan parameter validation.
     */
    public function update(TaskUpdateRequest $request, string $id): JsonResponse
    {
        try {
            // Validate route parameter
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid task ID format'
                ], 400);
            }
            
            $task = auth()->user()->tasks()->findOrFail($id);
            
            // Validation sudah dihandle oleh TaskUpdateRequest

            $task->update($request->only(['title', 'description', 'completed', 'category_id', 'status', 'priority']));
            $task->load('category');

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'Task updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating task: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified task from storage dengan atomic transaction.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            // Validate route parameter
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid task ID format'
                ], 400);
            }
            
            // Use transaction for atomicity
            \DB::transaction(function () use ($id) {
                $task = auth()->user()->tasks()->lockForUpdate()->findOrFail($id);
                
                // Delete task record
                $task->delete();
            });
            
            // Clear cache after successful deletion
            \Cache::forget('admin.stats');

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting task: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle task completion status with proper validation.
     */
    public function toggle(string $id): JsonResponse
    {
        try {
            // Validate route parameter
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid task ID format'
                ], 400);
            }
            
            // Use transaction for atomicity
            $task = \DB::transaction(function () use ($id) {
                $task = auth()->user()->tasks()->lockForUpdate()->findOrFail($id);
                
                // FIXED: Enforce policy authorization for consistency
                $this->authorize('toggle', $task);
                
                // Use toggleCompleted method for better consistency
                $task->toggleCompleted();
                $task->load('category');
                
                return $task;
            });
            
            // Clear cache after successful toggle
            \Cache::forget('admin.stats');

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'Task completion status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating task completion status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Advance task status to next logical status dengan parameter validation.
     */
    public function advanceStatus(string $id): JsonResponse
    {
        try {
            // Validate route parameter
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid task ID format'
                ], 400);
            }
            
            // Use transaction for atomicity
            $task = \DB::transaction(function () use ($id) {
                $task = auth()->user()->tasks()->lockForUpdate()->findOrFail($id);
                
                // Check if task can be advanced
                if (!$task->canAdvanceStatus()) {
                    throw new \Exception('Task status cannot be advanced further');
                }

                // Use advanceStatus method for workflow progression
                $task->advanceStatus();
                $task->load('category:id,name,color');
                
                return $task;
            });
            
            // Clear cache after successful advance
            \Cache::forget('admin.stats');

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'Task status advanced successfully'
            ]);
        } catch (\Exception $e) {
            $statusCode = str_contains($e->getMessage(), 'cannot be advanced') ? 422 : 500;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    /**
     * Update task status directly dengan parameter validation.
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        try {
            // Validate route parameter
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid task ID format'
                ], 400);
            }

            // Validate request data
            $request->validate([
                'status' => 'required|in:pending,in_progress,completed,cancelled',
            ]);
            
            // Use transaction for atomicity
            $task = \DB::transaction(function () use ($id, $request) {
                $task = auth()->user()->tasks()->lockForUpdate()->findOrFail($id);
                
                // Use setStatus method for direct status update
                $task->setStatus($request->status);
                $task->load('category:id,name,color');
                
                return $task;
            });
            
            // Clear cache after successful update
            \Cache::forget('admin.stats');

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'Task status updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating task status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update status for multiple tasks.
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        // Rate limiting: 5 bulk operations per minute per user
        $key = 'bulk-update:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, self::RATE_LIMIT_BULK_UPDATE)) {
            return $this->errorResponse('Too many bulk operations. Please wait before performing more bulk actions.', 429);
        }
        RateLimiter::hit($key, self::RATE_LIMIT_WINDOW);

        try {
            $request->validate([
                'task_ids' => 'required|array|min:1|max:' . self::MAX_BULK_OPERATIONS,
                'task_ids.*' => 'required|integer|min:1|max:999999999', // Ensure valid integer range
                'status' => 'required|in:pending,in_progress,completed,cancelled',
            ]);

            $taskIds = array_unique($request->task_ids); // Remove duplicates
            $newStatus = $request->status;

            // lockForUpdate only works within a transaction context
            $result = \DB::transaction(function () use ($taskIds, $newStatus) {
                // Validate that all tasks exist and belong to the authenticated user
                // Use pessimistic locking to prevent concurrent modifications
                $existingTasks = auth()->user()->tasks()
                    ->whereIn('id', $taskIds)
                    ->lockForUpdate() // Lock held until transaction commits
                    ->get()
                    ->keyBy('id');

                if ($existingTasks->count() !== count($taskIds)) {
                    $missingIds = array_diff($taskIds, $existingTasks->keys()->toArray());
                    throw new \Exception('Some tasks do not exist or do not belong to you: ' . implode(', ', $missingIds));
                }

                // Update tasks using model methods to preserve boot() logic and ensure data integrity
                // This approach ensures:
                // 1. Model events are fired (boot, saving, saved)
                // 2. Data sanitization and validation occur
                // 3. Status-completed field consistency is maintained
                // 4. All updates are atomic within transaction
                
                $updatedCount = 0;
                foreach ($existingTasks as $task) {
                    try {
                        // Use model method to maintain data integrity
                        $task->setStatus($newStatus);
                        $updatedCount++;
                    } catch (\Exception $e) {
                        // Log individual task update failures
                        \Log::warning("Failed to update task {$task->id}", [
                            'task_id' => $task->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                return ['updated_count' => $updatedCount, 'task_ids' => $taskIds];
            });

            // Get updated tasks (refresh from database after transaction)
            $tasks = auth()->user()->tasks()
                ->with(['category:id,name,color'])
                ->whereIn('id', $result['task_ids'])
                ->get();
            
            // Clear any relevant caches
            \Cache::forget('admin.stats');

            return response()->json([
                'success' => true,
                'data' => $tasks,
                'message' => "{$result['updated_count']} task(s) status updated successfully",
                'meta' => [
                    'updated_count' => $result['updated_count'],
                    'new_status' => $newStatus
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Error bulk updating task status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Bulk delete multiple tasks.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        // Rate limiting: 3 bulk delete operations per minute per user
        $key = 'bulk-delete:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, self::RATE_LIMIT_BULK_DELETE)) {
            return $this->errorResponse('Too many bulk delete operations. Please wait before performing more bulk actions.', 429);
        }
        RateLimiter::hit($key, self::RATE_LIMIT_WINDOW);

        try {
            $request->validate([
                'task_ids' => 'required|array|min:1|max:' . self::MAX_BULK_DELETE_OPERATIONS, // Limit for safety
                'task_ids.*' => 'required|integer|min:1',
            ]);

            $taskIds = array_unique($request->task_ids); // Remove duplicates
            
            // This prevents race condition where tasks could be deleted between validation and delete
            $deletedCount = \DB::transaction(function () use ($taskIds) {
                // Lock tasks and validate ownership
                $ownedTasks = auth()->user()->tasks()
                    ->whereIn('id', $taskIds)
                    ->lockForUpdate()
                    ->pluck('id')
                    ->toArray();

                if (count($ownedTasks) !== count($taskIds)) {
                    $missingIds = array_diff($taskIds, $ownedTasks);
                    throw new \Exception('Some tasks do not belong to you or do not exist: ' . implode(', ', $missingIds));
                }

                // Delete tasks atomically
                return auth()->user()->tasks()
                    ->whereIn('id', $taskIds)
                    ->delete();
            });
            
            // Clear cache after successful deletion
            \Cache::forget('admin.stats');

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} task(s) deleted successfully"
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error bulk deleting tasks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply filters and search to the query.
     */
    private function applyFilters($query, Request $request)
    {
        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $statusValue = $request->status;
            if (is_array($statusValue)) {
                $query->whereIn('status', $statusValue);
            } else {
                // Handle single status value from frontend filter
                $query->where('status', $statusValue);
            }
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $priorityValue = $request->priority;
            if (is_array($priorityValue)) {
                $query->whereIn('priority', $priorityValue);
            } else {
                // Handle single priority value from frontend filter
                $query->where('priority', $priorityValue);
            }
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by completion status
        if ($request->filled('completed')) {
            $query->where('completed', $request->boolean('completed'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    }

    /**
     * Get applied filters for response metadata.
     */
    private function getAppliedFilters(Request $request): array
    {
        $filters = [];

        if ($request->filled('search')) {
            $filters['search'] = $request->search;
        }

        if ($request->filled('status')) {
            $filters['status'] = $request->status;
        }

        if ($request->filled('priority')) {
            $filters['priority'] = $request->priority;
        }

        if ($request->filled('category_id')) {
            $filters['category_id'] = $request->category_id;
        }

        if ($request->filled('completed')) {
            $filters['completed'] = $request->boolean('completed');
        }

        if ($request->filled('date_from')) {
            $filters['date_from'] = $request->date_from;
        }

        if ($request->filled('date_to')) {
            $filters['date_to'] = $request->date_to;
        }

        return $filters;
    }

    /**
     * Duplicate a task with optional modifications.
     */
    public function duplicate(Request $request, string $id): JsonResponse
    {
        try {
            // Validate route parameter
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid task ID format'
                ], 400);
            }
            
            $originalTask = auth()->user()->tasks()->findOrFail($id);

            // Validate request data
            $request->validate([
                'title' => 'nullable|string|max:255|min:3',
                'description' => 'nullable|string|max:1000',
                'status' => 'nullable|in:pending,in_progress,completed,cancelled',
                'priority' => 'nullable|in:low,medium,high,urgent',
                'category_id' => 'nullable|exists:categories,id'
            ]);

            // Create duplicate with modifications
            $duplicateData = [
                'title' => $request->title ?? $originalTask->title . ' (Copy)',
                'description' => $request->description ?? $originalTask->description,
                'status' => $request->status ?? 'pending', // Always start as pending
                'priority' => $request->priority ?? $originalTask->priority,
                'category_id' => $request->category_id ?? $originalTask->category_id,
                'completed' => false // Always start as incomplete
            ];

            $duplicateTask = auth()->user()->tasks()->create($duplicateData);
            $duplicateTask->load('category:id,name,color');

            return response()->json([
                'success' => true,
                'data' => $duplicateTask,
                'message' => 'Task duplicated successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error duplicating task: ' . $e->getMessage()
            ], 500);
        }
    }

}
