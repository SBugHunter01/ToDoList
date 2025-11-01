<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Composite index untuk user + status (sangat sering digunakan untuk filtering)
            $table->index(['user_id', 'status'], 'tasks_user_status_index');

            // Composite index untuk user + priority (untuk sorting dan filtering)
            $table->index(['user_id', 'priority'], 'tasks_user_priority_index');

            // Composite index untuk user + category (untuk filtering berdasarkan kategori)
            $table->index(['user_id', 'category_id'], 'tasks_user_category_index');

            // Composite index untuk user + status + priority (untuk complex queries)
            $table->index(['user_id', 'status', 'priority'], 'tasks_user_status_priority_index');

            // Index untuk updated_at (untuk sorting terbaru)
            $table->index('updated_at', 'tasks_updated_at_index');

            // Composite index untuk user + created_at (untuk pagination dan sorting)
            $table->index(['user_id', 'created_at'], 'tasks_user_created_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            // Composite index untuk role + created_at (untuk admin queries)
            $table->index(['role', 'created_at'], 'users_role_created_at_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            // Index untuk name (untuk sorting dan filtering)
            // Note: user_id column tidak ada di tabel categories
            // $table->index(['user_id', 'name'], 'categories_user_name_index');

            // Index untuk color (jika sering difilter berdasarkan warna)
            $table->index('color', 'categories_color_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_user_status_index');
            $table->dropIndex('tasks_user_priority_index');
            $table->dropIndex('tasks_user_category_index');
            $table->dropIndex('tasks_user_status_priority_index');
            $table->dropIndex('tasks_updated_at_index');
            $table->dropIndex('tasks_user_created_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_created_at_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            // $table->dropIndex('categories_user_name_index'); // Index tidak pernah dibuat
            $table->dropIndex('categories_color_index');
        });
    }
};
