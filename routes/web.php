<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Home/Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
require __DIR__.'/auth.php';

// Protected Routes (Authentication Required)
Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Task Routes (User Dashboard)
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/{id}', [TaskController::class, 'show'])->name('tasks.show');
        Route::put('/{id}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
        Route::patch('/{id}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
        Route::patch('/{id}/advance-status', [TaskController::class, 'advanceStatus'])->name('tasks.advance-status');
        Route::patch('/{id}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
        Route::post('/bulk-update-status', [TaskController::class, 'bulkUpdateStatus'])->name('tasks.bulk-update-status');
        Route::delete('/bulk-delete', [TaskController::class, 'bulkDelete'])->name('tasks.bulk-delete');
        Route::post('/{id}/duplicate', [TaskController::class, 'duplicate'])->name('tasks.duplicate');
    });

    // Category Routes
    Route::resource('categories', CategoryController::class);

    // User Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// API Routes for AJAX calls (with JSON responses)
Route::prefix('api')->middleware('auth')->group(function () {
    // Task API Routes
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    Route::patch('/tasks/{id}/toggle', [TaskController::class, 'toggle']);
    Route::patch('/tasks/{id}/advance-status', [TaskController::class, 'advanceStatus']);
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::post('/tasks/bulk-update-status', [TaskController::class, 'bulkUpdateStatus']);
    Route::delete('/tasks/bulk-delete', [TaskController::class, 'bulkDelete']);
    Route::post('/tasks/{id}/duplicate', [TaskController::class, 'duplicate']);
    
    // Category API Routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});

// Admin Routes (Admin Only)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/stats', [AdminController::class, 'getStats'])->name('stats');
    Route::get('/users', [AdminController::class, 'getUsers'])->name('users');
    Route::patch('/users/{userId}/role', [AdminController::class, 'updateUserRole'])->name('users.update-role');
    Route::delete('/users/{userId}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    Route::get('/system-overview', [AdminController::class, 'getSystemOverview'])->name('system-overview');
});
