<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiteController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->middleware('auth');

    Route::get('/chart-data', [DashboardController::class, 'chartData'])
        ->middleware('auth');

    Route::get('/devices', [DeviceController::class, 'index']);

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

    // Admin only nanti dibikin middlewarenya, 
    Route::middleware(['auth', 'role:admin'])->group(function () {

        Route::get('/users', [UserController::class, 'index']);

        Route::post('/users', [UserController::class, 'store']);

        Route::put('/users/{user}', [UserController::class, 'update']);

        Route::delete('/users/{user}', [UserController::class, 'destroy']);

    });
    //=========================
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::get('/sites', [SiteController::class, 'index'])
        ->name('sites.index');

});

Route::get('/', fn() => redirect('/login'));