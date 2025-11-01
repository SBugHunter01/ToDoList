<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Personal',
                'color' => '#3B82F6',
                'description' => 'Tugas-tugas pribadi dan kehidupan sehari-hari'
            ],
            [
                'name' => 'Pekerjaan',
                'color' => '#EF4444',
                'description' => 'Tugas-tugas yang berkaitan dengan pekerjaan'
            ],
            [
                'name' => 'Pendidikan',
                'color' => '#10B981',
                'description' => 'Tugas sekolah, kuliah, dan pembelajaran'
            ],
            [
                'name' => 'Kesehatan',
                'color' => '#F59E0B',
                'description' => 'Aktivitas kesehatan dan olahraga'
            ],
            [
                'name' => 'Belanja',
                'color' => '#8B5CF6',
                'description' => 'Daftar belanja dan kebutuhan'
            ],
            [
                'name' => 'Hobi',
                'color' => '#EC4899',
                'description' => 'Aktivitas hobi dan hiburan'
            ]
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']], // Find by name
                $category // Update or create with all data
            );
        }
    }
}
