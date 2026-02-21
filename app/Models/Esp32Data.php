<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Esp32Data extends Model
{
    use HasFactory;

    protected $table = 'esp32_data';

    protected $fillable = [
        'temperature',
        'ph'
    ];

    // Accessor untuk format temperature
    public function getFormattedTemperatureAttribute()
    {
        return $this->temperature ? $this->temperature . ' °C' : '-';
    }

    // Accessor untuk format pH
    public function getFormattedPhAttribute()
    {
        return $this->ph ? number_format($this->ph, 2) . ' pH' : '-';
    }
}
