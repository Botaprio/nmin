<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AiCreditController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\YoutubeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Google OAuth routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::post('/auth/google/disconnect', [GoogleAuthController::class, 'disconnect'])->middleware('auth')->name('google.disconnect');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Boards
    Route::resource('boards', BoardController::class);
    
    // Columns
    Route::resource('columns', ColumnController::class)->except(['index', 'show']);
    
    // Cards
    Route::resource('cards', CardController::class)->except(['index', 'show']);
    Route::post('/cards/{card}/move', [CardController::class, 'move'])->name('cards.move');
    Route::post('/cards/{card}/assign', [CardController::class, 'assignUser'])->name('cards.assign');
    
    // Comments
    Route::post('/cards/{card}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    
    // AI Credits
    Route::get('/ai-credits', [AiCreditController::class, 'index'])->name('ai-credits.index');
    Route::post('/ai-credits', [AiCreditController::class, 'store'])->name('ai-credits.store');
    Route::patch('/ai-credits/{aiCredit}', [AiCreditController::class, 'update'])->name('ai-credits.update');
    
    // Google Drive
    Route::post('/cards/{card}/drive/upload', [GoogleDriveController::class, 'upload'])->name('drive.upload');
    Route::get('/drive/files/{file}', [GoogleDriveController::class, 'show'])->name('drive.show');
    Route::delete('/drive/files/{file}', [GoogleDriveController::class, 'destroy'])->name('drive.destroy');
    
    // YouTube
    Route::get('/youtube/analytics', [YoutubeController::class, 'analytics'])->name('youtube.analytics');
    Route::post('/cards/{card}/youtube/link', [YoutubeController::class, 'linkVideo'])->name('youtube.link');
    Route::post('/youtube/{youtubeVideo}/refresh', [YoutubeController::class, 'refreshAnalytics'])->name('youtube.refresh');
    
    // Activity Log
    Route::get('/boards/{board}/activity', [ActivityLogController::class, 'index'])->name('activity.index');
});

require __DIR__.'/auth.php';
