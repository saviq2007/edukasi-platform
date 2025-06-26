<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');
    
    // Categories Management
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('admin.categories.destroy');
    
    // Materials Management
    Route::get('/materials', [AdminController::class, 'materials'])->name('admin.materials');
    Route::post('/materials', [AdminController::class, 'storeMaterial'])->name('admin.materials.store');
    Route::put('/materials/{id}', [AdminController::class, 'updateMaterial'])->name('admin.materials.update');
    Route::delete('/materials/{id}', [AdminController::class, 'deleteMaterial'])->name('admin.materials.destroy');
    
    // Quizzes Management
    Route::get('/quizzes', [AdminController::class, 'quizzes'])->name('admin.quizzes');
    Route::post('/quizzes', [AdminController::class, 'storeQuiz'])->name('admin.quizzes.store');
    Route::put('/quizzes/{id}', [AdminController::class, 'updateQuiz'])->name('admin.quizzes.update');
    Route::delete('/quizzes/{id}', [AdminController::class, 'deleteQuiz'])->name('admin.quizzes.destroy');
    Route::get('/quizzes/{id}/questions', [AdminController::class, 'quizQuestions'])->name('admin.quizzes.questions');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/export', [AdminController::class, 'exportReport'])->name('admin.reports.export');
});

// User Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    
    // Materials
    Route::get('/materials', [UserController::class, 'materials'])->name('materials');
    Route::get('/materials/{id}', [UserController::class, 'showMaterial'])->name('materials.show');
    Route::post('/materials/{id}/complete', [UserController::class, 'markAsCompleted'])->name('materials.complete');
    
    // Quizzes
    Route::get('/quizzes', [UserController::class, 'quizzes'])->name('quizzes');
    Route::get('/quizzes/{id}/take', [UserController::class, 'takeQuiz'])->name('quizzes.take');
    Route::post('/quizzes/{id}/submit', [UserController::class, 'submitQuiz'])->name('quizzes.submit');
    
    // Progress
    Route::get('/progress', [UserController::class, 'progress'])->name('progress');
    
    // Profile
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/change-password', [UserController::class, 'changePassword'])->name('profile.change-password');
});
