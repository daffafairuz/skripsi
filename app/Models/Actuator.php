<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actuator extends Model
{
protected $fillable = [
        'device_id',
        'name',
        'type',
        'default_state'
    ];

    /**
     * Relasi: Actuator dimiliki oleh satu Device.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Relasi: Actuator memiliki banyak riwayat aktivitas (logs).
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ActuatorLog::class);
    }
}
