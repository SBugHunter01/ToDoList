<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Get admin statistics.
     */
    public function getStats(): JsonResponse
    {
        try {
            // FIXED: Cache recent activities separately with shorter TTL (1 minute)
            // This allows frequently changing data to update more often
            // while keeping main stats cached for longer
            $recentActivities = Cache::remember('admin.recent_activities', 60, function () {
                return $this->getRecentActivities();
            });
            
            $stats = Cache::remember('admin.stats', 300, function () use ($recentActivities) { // Cache for 5 minutes
                return [
                    'users' => [
                        'total' => User::count(),
                        'admins' => User::where('role', 'admin')->count(),
                        'regular_users' => User::where('role', 'user')->count(),
                        'recent' => User::whereDate('created_at', '>=', now()->subDays(7))->count()
                    ],
                    'tasks' => [
                        'total' => Task::count(),
                        'completed' => Task::where('status', 'completed')->count(),
                        'pending' => Task::where('status', 'pending')->count(),
                        'in_progress' => Task::where('status', 'in_progress')->count(),
                        'cancelled' => Task::where('status', 'cancelled')->count(),
                        'recent' => Task::whereDate('created_at', '>=', now()->subDays(7))->count()
                    ],
                    'categories' => [
                        'total' => Category::count(),
                        'most_used' => Category::withCount('tasks')
                            ->orderBy('tasks_count', 'desc')
                            ->limit(5)
                            ->get()
                            ->map(function ($category) {
                                return [
                                    'name' => $category->name,
                                    'count' => $category->tasks_count,
                                    'color' => $category->color
                                ];
                            })
                    ],
                    'recent_activities' => $recentActivities
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users for admin management.
     */
    public function getUsers(): JsonResponse
    {
        try {
            $users = User::withCount('tasks')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'tasks_count' => $user->tasks_count,
                        'created_at' => $user->created_at->format('d M Y H:i'),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user role dengan parameter validation.
     */
    public function updateUserRole(Request $request, $userId): JsonResponse
    {
        try {
            // Validate route parameter
            if (!is_numeric($userId) || $userId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user ID format'
                ], 400);
            }
            
            $request->validate([
                'role' => 'required|in:user,admin'
            ]);

            $user = User::findOrFail($userId);
            
            // Prevent demoting the last admin
            if ($user->role === 'admin' && $request->role === 'user') {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot demote the last admin user'
                    ], 400);
                }
            }

            $user->update(['role' => $request->role]);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User role updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user (admin only) dengan transaction dan file cleanup.
     */
    public function deleteUser($userId): JsonResponse
    {
        try {
            // Validate route parameter
            if (!is_numeric($userId) || $userId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user ID format'
                ], 400);
            }
            
            $user = User::findOrFail($userId);

            // Prevent deleting the last admin
            if ($user->role === 'admin') {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete the last admin user'
                    ], 400);
                }
            }

            // Prevent self-deletion
            if ($user->id === auth()->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ], 400);
            }

            // Use transaction untuk atomic operation
            DB::transaction(function () use ($user) {
                // Delete user (akan cascade delete tasks via foreign key)
                // Attachment cleanup sudah tidak diperlukan karena fitur upload sudah dihapus
                $user->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system overview for admin.
     */
    public function getSystemOverview(): JsonResponse
    {
        try {
            // Task completion rate over time (last 30 days)
            $taskTrends = DB::table('tasks')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed'))
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // User registration trends (last 30 days)
            $userTrends = DB::table('users')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Categories usage
            $categoryUsage = Category::withCount('tasks')
                ->get()
                ->map(function ($category) {
                    return [
                        'name' => $category->name,
                        'tasks_count' => $category->tasks_count,
                        'color' => $category->color
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'task_trends' => $taskTrends,
                    'user_trends' => $userTrends,
                    'category_usage' => $categoryUsage
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching system overview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent activities for dashboard.
     */
    private function getRecentActivities()
    {
        $recentTasks = Task::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($task) {
                return [
                    'type' => 'task_created',
                    'message' => "{$task->user->name} membuat tugas \"{$task->title}\"",
                    'time' => $task->created_at->diffForHumans(),
                    'category' => $task->category ? [
                        'name' => $task->category->name,
                        'color' => $task->category->color
                    ] : null,
                    'created_at' => $task->created_at // For proper sorting
                ];
            });

        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user_registered',
                    'message' => e("User baru {$user->name} bergabung"),
                    'time' => $user->created_at->diffForHumans(),
                    'category' => null,
                    'created_at' => $user->created_at // For proper sorting
                ];
            });

        return $recentTasks->concat($recentUsers)
            ->sortByDesc('created_at') // Sort by actual timestamp
            ->take(10)
            ->values();
    }
}
