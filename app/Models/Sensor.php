<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
protected $fillable = [
        'device_id',
        'name',
        'type',
        'unit',
        'min_threshold',
        'max_threshold'
    ];

    /**
     * Relasi: Sensor ini dimiliki oleh satu Device.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Relasi: Sensor ini memiliki banyak data record (DataSensor).
     */
    public function dataSensors(): HasMany
    {
        return $this->hasMany(DataSensor::class, 'sensor_id');
    }
}
