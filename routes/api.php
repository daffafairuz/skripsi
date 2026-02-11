<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TemperatureController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// post temperature
Route::post('/temperature', [TemperatureController::class, 'store']);
// get temperature
Route::get('/temperature', [TemperatureController::class, 'index']);