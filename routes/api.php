<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PlatformController;
use App\Http\Controllers\Api\AnalyticsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ActivityLogController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::apiResource('posts', PostController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::put('posts/{post}/schedule', [PostController::class, 'schedule']);
    Route::put('posts/{post}/publish', [PostController::class, 'publish']);
    Route::put('posts/{post}/draft', [PostController::class, 'draft']);
    Route::get('platforms', [PlatformController::class, 'index']);
    Route::get('user/platforms', [PlatformController::class, 'userPlatforms']);
    Route::put('user/platforms', [PlatformController::class, 'updateUserPlatforms']);
    Route::get('analytics', [AnalyticsController::class, 'index']);
    Route::get('activity-logs', [ActivityLogController::class, 'index']);
});