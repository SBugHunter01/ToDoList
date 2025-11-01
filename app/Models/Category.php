<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Field yang boleh diisi melalui mass assignment
    protected $fillable = [
        'name',        // Nama kategori (contoh: Work, Personal, Study)
        'color',       // Warna kategori dalam format hex (contoh: #3B82F6)
        'description'  // Deskripsi kategori (opsional)
    ];
    
    // Cast tipe data otomatis
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Security: fields that should be sanitized
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($category) {
            // Sanitize name and description
            if ($category->name) {
                $category->name = strip_tags(trim($category->name));
            }
            if ($category->description) {
                $category->description = strip_tags(trim($category->description));
            }
            // Validate color format
            if ($category->color && !preg_match('/^#[a-fA-F0-9]{6}$/', $category->color)) {
                $category->color = '#3B82F6'; // Default blue
            }
        });
    }

    /**
     * Relasi: Category memiliki banyak Task
     * Satu kategori bisa digunakan oleh banyak tugas
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
