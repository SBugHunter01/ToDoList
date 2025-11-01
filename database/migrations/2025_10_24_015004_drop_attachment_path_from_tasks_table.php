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
            // Drop index first before dropping column
            $table->dropIndex('tasks_attachment_index');
            $table->dropColumn('attachment_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('category_id');
            $table->index('attachment_path', 'tasks_attachment_index');
        });
    }
};
