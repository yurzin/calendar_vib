<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\PartnerController;
use App\Http\Controllers\Api\Admin\ProfileController;
use App\Http\Controllers\Api\View\MainController;
use App\Http\Controllers\Auth\AuthController;
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
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/dashboard', [AdminController::class, 'index']);
        Route::get('/profiles/search', [ProfileController::class, 'search']);
        Route::post('/profiles', [ProfileController::class, 'store']);
        // restore должен быть ДО apiResource
        Route::post('/partners/{id}/restore', [PartnerController::class, 'restore']);
        Route::apiResource('partners', PartnerController::class);
    });
});

