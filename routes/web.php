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
use App\Http\Controllers\ActuatorLogController;
use App\Http\Controllers\DataSensorController;

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

    // Account Setting
    Route::middleware(['auth'])->group(function () {
        Route::get('/account', [AccountController::class, 'index'])->name('account-setting');
        Route::get('/account/edit', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('/account/update', [AccountController::class, 'update'])->name('account.update');
        Route::delete('/account/destroy', [AccountController::class, 'destroy'])->name('account.destroy');
    });

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

    // Actuator Log
    Route::get('/actuator-log', [ActuatorLogController::class, 'index'])->name('actuator-log');

    // Data Sensor
    Route::get('/data-sensor', [DataSensorController::class, 'index'])->name('data-sensor');

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