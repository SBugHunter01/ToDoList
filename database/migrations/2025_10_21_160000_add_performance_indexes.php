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
            // Index untuk query berdasarkan user dan status completion
            $table->index(['user_id', 'completed'], 'tasks_user_completed_index');
            
            // Index untuk sorting berdasarkan created_at (sering digunakan di admin dashboard)
            $table->index('created_at', 'tasks_created_at_index');
            
            // Index untuk query berdasarkan category (untuk filtering)
            $table->index('category_id', 'tasks_category_index');
            
            // Index untuk tasks yang punya attachment (untuk statistics)
            $table->index('attachment_path', 'tasks_attachment_index');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index untuk admin queries berdasarkan role
            $table->index('role', 'users_role_index');
            
            // Index untuk sorting users by created_at
            $table->index('created_at', 'users_created_at_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            // Index untuk sorting categories by name
            $table->index('name', 'categories_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_user_completed_index');
            $table->dropIndex('tasks_created_at_index');
            $table->dropIndex('tasks_category_index');
            $table->dropIndex('tasks_attachment_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
            $table->dropIndex('users_created_at_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_name_index');
        });
    }
};
