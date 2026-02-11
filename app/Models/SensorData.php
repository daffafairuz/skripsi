<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    // Nama tabel (opsional, tapi bagus untuk ketegasan)
    protected $table = 'sensor_data';

    // Kolom yang boleh diisi oleh ESP32
    protected $fillable = [
        'temperature',
        'ph',
       
    ];
}