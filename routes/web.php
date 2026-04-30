<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/chart-data', [DashboardController::class, 'chartData']);

    Route::get('/device', [DeviceController::class, 'index']);

    // Riwayat Data
    Route::prefix('history')->group(function () {
        Route::get('/suhu', [HistoryController::class, 'suhu']);
        Route::get('/kelembapan', [HistoryController::class, 'kelembapan']);
        Route::get('/ph', [HistoryController::class, 'ph']);
        Route::get('/debit', [HistoryController::class, 'debit']);
    });

    // Account
    Route::get('/account', [AccountController::class, 'index']);

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index']);

    // Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/', fn() => redirect('/login'));