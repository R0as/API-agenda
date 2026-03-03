<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\EventController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/email', [PasswordResetController::class, 'sendCode']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Events specific to the authenticated user
    Route::apiResource('events', EventController::class);

    // Admin routes
    Route::middleware('is_admin')->prefix('admin')->group(function() {
        Route::apiResource('users', AdminController::class);
    });
});
