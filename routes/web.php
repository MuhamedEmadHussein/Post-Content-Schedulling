<?php

use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Protected routes (require authentication)
Route::middleware(['web', 'auth:sanctum', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Posts
    Route::get('/posts/create', function () {
        return view('posts.create');
    })->name('posts.create');

    Route::get('/posts/{post}/edit', function () {
        return view('posts.edit');
    })->name('posts.edit');

    // Analytics
    Route::get('/analytics', function () {
        return view('analytics');
    })->name('analytics');

    // Settings
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');

    // Activity Logs
    Route::get('/activity-logs', function () {
        return view('activity-logs');
    })->name('activity-logs');

    // Profile
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
});

// Catch-all route for SPA-like behavior
Route::fallback(function () {
    return redirect()->route('dashboard');
});