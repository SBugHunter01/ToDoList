<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_KEY_ALL = 'categories:all';

    /**
     * Get all categories with task count
     */
    public function getAllCategories(bool $withCache = true): Collection
    {
        if ($withCache) {
            return Cache::remember(self::CACHE_KEY_ALL, self::CACHE_TTL, function () {
                return Category::withCount('tasks')->orderBy('name')->get();
            });
        }

        return Category::withCount('tasks')->orderBy('name')->get();
    }

    /**
     * Get category by ID with tasks
     */
    public function getCategoryWithTasks(Category $category): Category
    {
        return $category->load(['tasks' => function($query) {
            $query->select(['id', 'title', 'status', 'priority', 'completed', 'category_id']);
        }]);
    }

    /**
     * Create new category
     */
    public function createCategory(array $data): Category
    {
        DB::beginTransaction();
        try {
            $category = Category::create([
                'name' => $data['name'],
                'color' => $data['color'],
                'description' => $data['description'] ?? null,
            ]);

            $this->clearCache();
            DB::commit();

            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update category
     */
    public function updateCategory(Category $category, array $data): Category
    {
        DB::beginTransaction();
        try {
            $category->update([
                'name' => $data['name'],
                'color' => $data['color'],
                'description' => $data['description'] ?? $category->description,
            ]);

            $this->clearCache();
            DB::commit();

            return $category->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category update failed', ['error' => $e->getMessage(), 'category_id' => $category->id]);
            throw $e;
        }
    }

    /**
     * Delete category if no tasks attached
     */
    public function deleteCategory(Category $category): bool
    {
        $taskCount = $category->tasks()->count();
        
        if ($taskCount > 0) {
            throw new \Exception("Cannot delete category with {$taskCount} existing task(s). Please reassign or delete the tasks first.");
        }

        DB::beginTransaction();
        try {
            $deleted = $category->delete();
            $this->clearCache();
            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category deletion failed', ['error' => $e->getMessage(), 'category_id' => $category->id]);
            throw $e;
        }
    }

    /**
     * Get category statistics
     */
    public function getCategoryStatistics(): array
    {
        $categories = $this->getAllCategories();

        return [
            'total' => $categories->count(),
            'most_used' => $categories->sortByDesc('tasks_count')->take(5)->values(),
            'unused' => $categories->where('tasks_count', 0)->count(),
        ];
    }

    /**
     * Clear category cache
     */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
    }
}
