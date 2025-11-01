<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user by email
        $user = User::where('email', 'admin@todolist.com')->first();

        if (!$user) {
            $this->command->error('Admin user not found. Please run DatabaseSeeder first.');
            return;
        }

        // Get categories and create map for easy lookup
        $categories = Category::all()->keyBy('name');

        // Create default category if it doesn't exist
        $defaultCategory = Category::firstOrCreate(
            ['name' => 'Personal'],
            [
                'color' => '#3B82F6',
                'description' => 'Tugas-tugas pribadi dan umum'
            ]
        );

        // Helper function to safely get category ID with fallback
        $getCategoryId = function ($categoryName) use ($categories, $defaultCategory) {
            return $categories->get($categoryName)?->id ?? $defaultCategory->id;
        };

        $sampleTasks = [
            [
                'title' => 'Belajar Laravel untuk PKL',
                'description' => 'Menyelesaikan tutorial Laravel dan membuat project ToDo List',
                'completed' => false,
                'status' => 'in_progress',
                'priority' => 'high',
                'category_id' => $getCategoryId('Pendidikan')
            ],
            [
                'title' => 'Setup environment development',
                'description' => 'Install XAMPP, Composer, dan tools development lainnya',
                'completed' => true,
                'status' => 'completed',
                'priority' => 'medium',
                'category_id' => $getCategoryId('Pekerjaan')
            ],
            [
                'title' => 'Olahraga pagi',
                'description' => 'Jogging selama 30 menit di sekitar komplek',
                'completed' => false,
                'status' => 'pending',
                'priority' => 'low',
                'category_id' => $getCategoryId('Kesehatan')
            ],
            [
                'title' => 'Belanja groceries',
                'description' => 'Beli beras, minyak goreng, dan sayuran untuk seminggu',
                'completed' => false,
                'status' => 'pending',
                'priority' => 'medium',
                'category_id' => $getCategoryId('Belanja')
            ],
            [
                'title' => 'Baca buku programming',
                'description' => 'Lanjutkan membaca "Clean Code" chapter 3-4',
                'completed' => false,
                'status' => 'in_progress',
                'priority' => 'medium',
                'category_id' => $getCategoryId('Hobi')
            ],
            [
                'title' => 'Meeting dengan pembimbing PKL',
                'description' => 'Review progress project dan diskusi next steps',
                'completed' => true,
                'status' => 'completed',
                'priority' => 'urgent',
                'category_id' => $getCategoryId('Pekerjaan')
            ],
            [
                'title' => 'Backup data penting',
                'description' => 'Backup project files dan dokumen penting ke cloud storage',
                'completed' => false,
                'status' => 'pending',
                'priority' => 'high',
                'category_id' => $getCategoryId('Personal')
            ]
        ];

        // Only create tasks that don't exist yet to avoid duplicates
        foreach ($sampleTasks as $taskData) {
            $existingTask = $user->tasks()->where('title', $taskData['title'])->first();
            if (!$existingTask) {
                try {
                    $user->tasks()->create($taskData);
                    $this->command->info("Created task: {$taskData['title']}");
                } catch (\Exception $e) {
                    $this->command->error("Failed to create task '{$taskData['title']}': " . $e->getMessage());
                }
            } else {
                $this->command->info("Task already exists: {$taskData['title']}");
            }
        }
    }
}
