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
use App\Http\Controllers\FeedScheduleController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\ActuatorController;
use App\Http\Controllers\ActuatorControlController;
use App\Http\Controllers\SiteDeviceController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->middleware('auth');

    Route::get('/chart-data', [DashboardController::class, 'chartData'])
        ->middleware('auth');

    // =========================
    // DEVICES (resource)
    // =========================
    Route::resource('devices', DeviceController::class);

    // Riwayat Data
    Route::prefix('history')->group(function () {
        Route::get('/suhu', [HistoryController::class, 'suhu']);
        Route::get('/kelembapan', [HistoryController::class, 'kelembapan']);
        Route::get('/ph', [HistoryController::class, 'ph']);
        Route::get('/debit', [HistoryController::class, 'debit']);
    });

    // Account Setting
    Route::get('/account', [AccountController::class, 'index'])->name('account-setting');
    Route::get('/account/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account/update', [AccountController::class, 'update'])->name('account.update');
    Route::delete('/account/destroy', [AccountController::class, 'destroy'])->name('account.destroy');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

    // Actuator Log (Data Monitoring)
    Route::get('/actuator-log', [ActuatorLogController::class, 'index'])->name('actuator-log');
    Route::get('/actuator-log/export-csv', [ActuatorLogController::class, 'exportCsv'])->name('actuator-log.export-csv');

    // Grow Light Schedule
    Route::prefix('growlight')->group(function () {
        Route::get('/schedule', [GrowLightScheduleController::class, 'index'])->name('growlight.schedule');
        Route::get('/create', [GrowLightScheduleController::class, 'create'])->name('growlight.create');
        Route::post('/store', [GrowLightScheduleController::class, 'store'])->name('growlight.store');
        Route::get('/edit/{id}', [GrowLightScheduleController::class, 'edit'])->name('growlight.edit');
        Route::put('/update/{id}', [GrowLightScheduleController::class, 'update'])->name('growlight.update');
        Route::delete('/destroy/{id}', [GrowLightScheduleController::class, 'destroy'])->name('growlight.destroy');
    });

    // Data Sensor (Data Monitoring)
    Route::get('/data-sensor', [DataSensorController::class, 'index'])->name('data-sensor');
    Route::get('/data-sensor/export-csv', [DataSensorController::class, 'exportCsv'])->name('data-sensor.export-csv');

    // Grow Light Schedule (alias)
    Route::get('/jadwal-grow-light', [GrowLightScheduleController::class, 'index'])->name('grow-light-schedule');

    // Feeder Schedule
    Route::get('/jadwal-pakan', [FeedScheduleController::class, 'index'])->name('jadwal-pakan.index');
    Route::get('/jadwal-pakan/create', [FeedScheduleController::class, 'create'])->name('jadwal-pakan.create');
    Route::post('/jadwal-pakan', [FeedScheduleController::class, 'store'])->name('jadwal-pakan.store');
    Route::get('/jadwal-pakan/{id}/edit', [FeedScheduleController::class, 'edit'])->name('jadwal-pakan.edit');
    Route::put('/jadwal-pakan/{id}', [FeedScheduleController::class, 'update'])->name('jadwal-pakan.update');
    Route::delete('/jadwal-pakan/{id}', [FeedScheduleController::class, 'destroy'])->name('jadwal-pakan.destroy');

    // =========================
    // ACTUATOR CONTROL (User)
    // =========================
    Route::get('/actuator-control', [ActuatorControlController::class, 'index'])->name('actuator-control');
    Route::post('/actuator-control/{id}/toggle', [ActuatorControlController::class, 'toggle'])->name('actuator-control.toggle');

    // =========================
    // ADMIN ONLY ROUTES
    // =========================
    Route::middleware(['auth', 'role:admin'])->group(function () {

        // Users CRUD
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        // Sensors CRUD
        Route::get('/sensors', [SensorController::class, 'index']);
        Route::post('/sensors', [SensorController::class, 'store']);
        Route::put('/sensors/{sensor}', [SensorController::class, 'update']);
        Route::delete('/sensors/{sensor}', [SensorController::class, 'destroy']);

        // Actuators CRUD
        Route::get('/actuators', [ActuatorController::class, 'index']);
        Route::post('/actuators', [ActuatorController::class, 'store']);
        Route::put('/actuators/{actuator}', [ActuatorController::class, 'update']);
        Route::delete('/actuators/{actuator}', [ActuatorController::class, 'destroy']);
    });

    //=========================
    Route::post('/logout', [AuthController::class, 'logout']);

    // Sites (resource)
    Route::resource('sites', SiteController::class);

    // Site Devices
    Route::get('/sites/{site}/devices/create', [SiteDeviceController::class, 'create'])->name('sites.devices.create');
    Route::post('/sites/{site}/devices', [SiteDeviceController::class, 'store'])->name('sites.devices.store');
    Route::post('/site-devices', [SiteDeviceController::class, 'attach'])->name('site-devices.store');
    Route::delete('/sites/{site}/devices/{device}', [SiteDeviceController::class, 'destroy'])->name('sites.devices.destroy');

    // Actuator Toggle (from bima_view)
    Route::post('/actuator/{actuator}/toggle', [ActuatorController::class, 'toggle'])->name('actuator.toggle');

    // Sensor by Device
    Route::get('/sensors/device/{device}', [SensorController::class, 'device'])->name('sensors.device');

    // Sensor Chart
    Route::get('/sensor/{sensor}/chart', [SensorController::class, 'chart'])->name('sensor.chart');

});

Route::get('/', fn() => redirect('/login'));

use App\Models\Notification;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {

    $notification = new Notification([
        'message' => 'Ini email percobaan Smart Aquaponic',
        'type' => 'info',
        'is_read' => false
    ]);

    Mail::to('bima4453@gmail.com')
        ->send(new NotificationMail($notification));

    return 'Email berhasil dikirim';
});
