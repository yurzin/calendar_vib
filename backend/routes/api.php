<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\CalendarExportController;
use App\Http\Controllers\Api\Admin\PartnerController;
use App\Http\Controllers\Api\Admin\PersonController;
use App\Http\Controllers\Api\Admin\ProfileController;
use App\Http\Controllers\Api\View\MainController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/user', [AuthController::class, 'user']);

// Только авторизация, без проверки роли
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// С проверкой роли
Route::middleware(['auth:sanctum', 'role:admin|editor'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

// Публичные
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/main', [MainController::class, 'index']);
Route::post('/members', [MainController::class, 'members']);

Route::domain(env('ADMIN_DOMAIN', 'admin.calendar.local'))->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    // auth:sanctum сначала проверяет токен, затем admin проверяет роль
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/dashboard', [AdminController::class, 'index']);
        Route::get('/profiles/search', [ProfileController::class, 'search']);
        Route::post('/profiles', [ProfileController::class, 'store']);
        Route::post('/partners/{id}/restore', [PartnerController::class, 'restore']);
        Route::apiResource('partners', PartnerController::class);
        Route::apiResource('persons', PersonController::class);
        Route::prefix('export/calendar')->group(function () {
            // Статистика по месяцам (для кнопок в UI)
            Route::get('stats',    [CalendarExportController::class, 'stats']);

            // JSON для InDesign (просмотр в браузере)
            Route::get('json',     [CalendarExportController::class, 'exportJson']);

            // Скачать JSON-файл
            Route::get('download', [CalendarExportController::class, 'downloadJson']);
        });
    });
});
