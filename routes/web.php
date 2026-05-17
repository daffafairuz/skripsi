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
use App\Http\Controllers\GrowLightScheduleController;
use App\Http\Controllers\FeederScheduleController;

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

    // Grow Light Schedule
    Route::prefix('growlight')->group(function () {
        Route::get('/schedule', [GrowLightScheduleController::class, 'index'])->name('growlight.schedule');
        Route::get('/create', [GrowLightScheduleController::class, 'create'])->name('growlight.create');
        Route::post('/store', [GrowLightScheduleController::class, 'store'])->name('growlight.store');
        Route::get('/edit/{id}', [GrowLightScheduleController::class, 'edit'])->name('growlight.edit');
        Route::put('/update/{id}', [GrowLightScheduleController::class, 'update'])->name('growlight.update');
        Route::delete('/destroy/{id}', [GrowLightScheduleController::class, 'destroy'])->name('growlight.destroy');
    });
    
    // Data Sensor
    Route::get('/data-sensor', [DataSensorController::class, 'index'])->name('data-sensor');

    // Grow Light Schedule
    Route::get('/jadwal-grow-light', [GrowLightScheduleController::class, 'index'])->name('grow-light-schedule');

    // Feeder Schedule
    Route::get('/jadwal-feeder', [FeederScheduleController::class, 'index'])->name('feeder-schedule');

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
