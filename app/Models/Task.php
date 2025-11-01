<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // Field yang boleh diisi melalui mass assignment
    protected $fillable = [
        'title',          // Judul tugas
        'description',    // Deskripsi tugas (opsional)
        'completed',      // Status selesai (true/false)
        'status',         // Status tugas: pending, in_progress, completed, cancelled
        'priority',       // Prioritas tugas: low, medium, high, urgent
        'user_id',        // ID pemilik tugas
        'category_id',    // ID kategori tugas (opsional)
    ];

    // Cast tipe data otomatis
    protected $casts = [
        'completed' => 'boolean', // Konversi ke boolean untuk field completed
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Security: fields that should be sanitized
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($task) {
            // Sanitize title and description
            if ($task->title) {
                $task->title = strip_tags(trim($task->title));
            }
            if ($task->description) {
                $task->description = strip_tags(trim($task->description), '<br><p><strong><em>');
            }

            // Validate and set default values for status
            $validStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];
            if (!$task->status || !in_array($task->status, $validStatuses)) {
                $task->status = 'pending';
            }

            // Validate and set default values for priority
            $validPriorities = ['low', 'medium', 'high', 'urgent'];
            if (!$task->priority || !in_array($task->priority, $validPriorities)) {
                $task->priority = 'medium';
            }

            // Ensure completed is consistent with status
            // This is crucial for data integrity between status and completed fields
            if ($task->status === 'completed') {
                $task->completed = true;
            } elseif (in_array($task->status, ['pending', 'in_progress', 'cancelled'])) {
                $task->completed = false;
            } else {
                // Fallback for any unexpected status values
                $task->completed = false;
                $task->status = 'pending';
            }
        });
    }

    /**
     * Relasi: Task milik satu User
     * Setiap tugas hanya bisa dimiliki oleh satu user
     */
    public function user()
    {
        return $this->belongsTo(User::class)->select(['id', 'name', 'email']);
    }

    /**
     * Relasi: Task bisa memiliki satu Category (opsional)
     * Tugas bisa dikategorikan untuk organisasi yang lebih baik
     */
    public function category()
    {
        return $this->belongsTo(Category::class)->select(['id', 'name', 'color']);
    }

    /**
     * Get status information with comprehensive workflow data
     */
    public function getStatusInfo()
    {
        $statusMap = [
            'pending' => [
                'label' => 'Menunggu',
                'icon' => 'fas fa-clock',
                'color' => 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300',
                'border_color' => 'border-gray-300 dark:border-gray-500',
                'next_status' => 'in_progress',
                'can_toggle' => true,
                'can_advance' => true,
                'is_final' => false,
                'order' => 1
            ],
            'in_progress' => [
                'label' => 'Dalam Proses',
                'icon' => 'fas fa-play-circle',
                'color' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                'border_color' => 'border-blue-300 dark:border-blue-600',
                'next_status' => 'completed',
                'can_toggle' => true,
                'can_advance' => true,
                'is_final' => false,
                'order' => 2
            ],
            'completed' => [
                'label' => 'Selesai',
                'icon' => 'fas fa-check-circle',
                'color' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                'border_color' => 'border-green-300 dark:border-green-600',
                'next_status' => null,
                'can_toggle' => true,
                'can_advance' => false,
                'is_final' => true,
                'order' => 3
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'icon' => 'fas fa-times-circle',
                'color' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                'border_color' => 'border-red-300 dark:border-red-600',
                'next_status' => null,
                'can_toggle' => false,
                'can_advance' => false,
                'is_final' => true,
                'order' => 4
            ]
        ];

        return $statusMap[$this->status] ?? $statusMap['pending'];
    }

    /**
     * Get priority information
     */
    public function getPriorityInfo()
    {
        $priorityMap = [
            'low' => [
                'label' => 'Rendah',
                'icon' => 'fas fa-arrow-down',
                'color' => 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300',
                'level' => 1
            ],
            'medium' => [
                'label' => 'Sedang',
                'icon' => 'fas fa-minus',
                'color' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                'level' => 2
            ],
            'high' => [
                'label' => 'Tinggi',
                'icon' => 'fas fa-arrow-up',
                'color' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                'level' => 3
            ],
            'urgent' => [
                'label' => 'Mendesak',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                'level' => 4
            ]
        ];

        return $priorityMap[$this->priority] ?? $priorityMap['medium'];
    }

    /**
     * Update task status to next logical status
     * Ensures consistency between status and completed fields
     */
    public function advanceStatus()
    {
        $currentStatus = $this->getStatusInfo();

        if ($currentStatus['next_status']) {
            $this->status = $currentStatus['next_status'];

            // Always synchronize completed field with status
            $this->completed = ($currentStatus['next_status'] === 'completed');

            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Set task status directly with proper validation
     * Ensures completed field stays consistent with status
     */
    public function setStatus(string $status)
    {
        $validStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}. Valid statuses are: " . implode(', ', $validStatuses));
        }

        $this->status = $status;

        // Always synchronize completed field with status
        $this->completed = ($status === 'completed');

        $this->save();

        return $this;
    }

    /**
     * Toggle completed status and update status accordingly
     * Preserves workflow logic while allowing flexible completion
     * This maintains consistency: completed task can be reopened to in_progress
     */
    public function toggleCompleted()
    {
        // Store original values BEFORE any changes
        $originalStatus = $this->status;
        $wasCompleted = $this->completed;
        
        // Toggle completed flag
        $this->completed = !$this->completed;

        // Update status based on completed state
        if ($this->completed) {
            // When completing a task, always set status to completed
            $this->status = 'completed';
        } elseif ($originalStatus === 'completed') {
            // This is consistent with getValidTransitions() which allows completed -> in_progress
            // User can then work on it or move it back to pending if needed
            $this->status = 'in_progress';
        }
        // If task is not completed and status is not 'completed', keep current status

        $this->save();

        return $this;
    }

    /**
     * Check if task can be advanced to next status
     */
    public function canAdvanceStatus(): bool
    {
        $statusInfo = $this->getStatusInfo();
        return $statusInfo['can_advance'] ?? false;
    }

    /**
     * Check if task can be toggled (completed/incomplete)
     */
    public function canToggle(): bool
    {
        $statusInfo = $this->getStatusInfo();
        return $statusInfo['can_toggle'] ?? false;
    }

    /**
     * Check if task is in final state
     */
    public function isFinalState(): bool
    {
        $statusInfo = $this->getStatusInfo();
        return $statusInfo['is_final'] ?? false;
    }

    /**
     * Get status workflow order
     */
    public function getStatusOrder(): int
    {
        $statusInfo = $this->getStatusInfo();
        return $statusInfo['order'] ?? 0;
    }

    /**
     * Get valid status transitions for current status
     * Enforces proper workflow: pending -> in_progress -> completed
     */
    public function getValidTransitions(): array
    {
        $workflowTransitions = [
            'pending' => ['in_progress', 'cancelled'], // Can't jump directly to completed
            'in_progress' => ['completed', 'cancelled'], // Can complete or cancel from in_progress
            'completed' => ['in_progress'], // Can re-open completed tasks
            'cancelled' => ['pending', 'in_progress'], // Can restart cancelled tasks
        ];

        return $workflowTransitions[$this->status] ?? [];
    }

    /**
     * Set task priority directly with proper validation
     */
    public function setPriority(string $priority)
    {
        $validPriorities = ['low', 'medium', 'high', 'urgent'];

        if (!in_array($priority, $validPriorities)) {
            throw new \InvalidArgumentException("Invalid priority: {$priority}. Valid priorities are: " . implode(', ', $validPriorities));
        }

        $this->priority = $priority;
        $this->save();

        return $this;
    }
}
