<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Esp32DataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route untuk halaman utama (root) - ARAHKAN KE DASHBOARD ESP32
Route::get('/', [Esp32DataController::class, 'index'])->name('home');

// Route tambahan (opsional)
Route::get('/dashboard', [Esp32DataController::class, 'index'])->name('dashboard');
Route::get('/monitoring', [Esp32DataController::class, 'index'])->name('monitoring');

// Route untuk API (kalau butuh)
Route::get('/api/latest-data', function() {
    return App\Models\Esp32Data::latest()->first();
});
