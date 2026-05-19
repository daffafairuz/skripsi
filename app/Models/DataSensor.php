<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataSensor extends Model
{
// Mengatur nama tabel secara manual agar aman
    protected $table = 'data_sensors';

    protected $fillable = [
        'sensor_id',
        'value',
        'created_at_ts'
    ];
    /**
     * Relasi: DataSensor ini dimiliki oleh (belongsTo) satu Sensor.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }
}
