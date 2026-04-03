<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MainController;
use Illuminate\Support\Facades\Route;

// Только авторизация, без проверки роли
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Отдельно — с проверкой роли
Route::middleware(['auth:sanctum', 'role:admin|editor'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

// Публичные
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/main', [MainController::class, 'index']);

Route::domain(env('ADMIN_DOMAIN', 'admin.calendar.local'))->group(function () {
    // Админские маршруты с проверкой
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/dashboard', [AdminController::class, 'index']);
    });
});

