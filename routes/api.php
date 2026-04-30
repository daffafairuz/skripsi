<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TemperatureController;
use App\Http\Controllers\Api\SensorDataController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// post temperature
Route::post('/temperature', [TemperatureController::class, 'store']);
// get temperature
Route::get('/temperature', [TemperatureController::class, 'index']);

Route::prefix('sensors')->group(function () {
    Route::post('/', [SensorDataController::class, 'store']);
    Route::get('/', [SensorDataController::class, 'index']);  
});