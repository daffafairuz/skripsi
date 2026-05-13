<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
protected $fillable = [
        'mac_address',
        'name',
        'description',
        'status'
    ];

    /**
     * Relasi: Satu Device memiliki banyak Sensor.
     */
    public function sensors(): HasMany
    {
        return $this->hasMany(Sensor::class);
    }

    /**
     * Relasi: Satu Device memiliki banyak Aktuator.
     */
    public function actuators(): HasMany
    {
        return $this->hasMany(Actuator::class);
    }

    /**
     * Relasi: Satu Device bisa terpasang di banyak Site (Pivot).
     * Sesuai diagram, kita ambil data started_at dan ended_at dari tabel pivot.
     */
    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'site_devices')
                    ->withPivot('started_at', 'ended_at')
                    ->withTimestamps();
    }
}
