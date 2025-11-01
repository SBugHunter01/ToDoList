<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine if user can view any tasks
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view their own tasks list
        return true;
    }

    /**
     * Determine if user can view specific task
     */
    public function view(User $user, Task $task): bool
    {
        // User can only view their own tasks
        // Admin can view all tasks
        return $user->id === $task->user_id || $user->role === 'admin';
    }

    /**
     * Determine if user can create tasks
     */
    public function create(User $user): bool
    {
        // All authenticated users can create tasks
        return true;
    }

    /**
     * Determine if user can update task
     */
    public function update(User $user, Task $task): bool
    {
        // User can only update their own tasks
        return $user->id === $task->user_id;
    }

    /**
     * Determine if user can delete task
     */
    public function delete(User $user, Task $task): bool
    {
        // User can only delete their own tasks
        return $user->id === $task->user_id;
    }

    /**
     * Determine if user can restore task (soft delete)
     */
    public function restore(User $user, Task $task): bool
    {
        // User can only restore their own tasks
        return $user->id === $task->user_id;
    }

    /**
     * Determine if user can permanently delete task
     */
    public function forceDelete(User $user, Task $task): bool
    {
        // Only admin can force delete
        return $user->role === 'admin';
    }

    /**
     * Determine if user can toggle task completion
     */
    public function toggle(User $user, Task $task): bool
    {
        // User can only toggle their own tasks
        return $user->id === $task->user_id;
    }

    /**
     * Determine if user can update task status
     */
    public function updateStatus(User $user, Task $task): bool
    {
        // User can only update status of their own tasks
        return $user->id === $task->user_id;
    }

    /**
     * Determine if user can duplicate task
     */
    public function duplicate(User $user, Task $task): bool
    {
        // User can duplicate their own tasks or any task they can view
        return $user->id === $task->user_id || $user->role === 'admin';
    }

    /**
     * Determine if user can perform bulk operations
     */
    public function bulkUpdate(User $user): bool
    {
        // All authenticated users can bulk update their own tasks
        return true;
    }

    /**
     * Determine if user can bulk delete
     */
    public function bulkDelete(User $user): bool
    {
        // All authenticated users can bulk delete their own tasks
        return true;
    }
}
