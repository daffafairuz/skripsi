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
use App\Http\Controllers\SiteDeviceController;
use App\Http\Controllers\ActuatorController;
use App\Http\Controllers\SensorController;



Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->middleware('auth');

    Route::get('/chart-data', [DashboardController::class, 'chartData'])
        ->middleware('auth');

    Route::middleware(['auth'])->group(function () {

        Route::resource('devices', DeviceController::class);

    });
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


    Route::middleware(['auth'])
    ->group(function(){

    Route::resource(
        'sites',
        SiteController::class
    );

    });

});

Route::get('/', fn() => redirect('/login'));


Route::middleware(['auth'])
->group(function(){

Route::get(
'/sites/{site}/devices/create',
[SiteDeviceController::class,'create']
)
->name('sites.devices.create');


Route::post(
'/sites/{site}/devices',
[SiteDeviceController::class,'store']
)
->name('sites.devices.store');

});

Route::get(
'/sites/{site}',
[SiteController::class,'show']
)->name('sites.show');

Route::post(
'/actuator/{actuator}/toggle',
[ActuatorController::class,'toggle']
)
->name('actuator.toggle');


Route::get(
    '/sensors/device/{device}',
    [SensorController::class,'device']
)->name('sensors.device');

Route::get(
'/sensor/{sensor}/chart',
[SensorController::class,'chart']
)->name('sensor.chart');