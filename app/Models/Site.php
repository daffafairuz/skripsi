<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
protected $fillable = [
        'user_id',
        'name',
        'location',
        'description',
        'mac_address'
    ];

    /**
     * Relasi: Site dimiliki oleh seorang User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Site memiliki banyak perangkat melalui tabel pivot site_devices.
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'site_devices')
                    ->withPivot('started_at', 'ended_at')
                    ->withTimestamps();
    }

    /**
     * Relasi: Site memiliki banyak notifikasi.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Relasi: Site memiliki banyak jadwal pemberian pakan.
     */
    public function feedSchedules(): HasMany
    {
        return $this->hasMany(FeedSchedule::class);
    }
    public function growLightSchedules(): HasMany
    {
        return $this->hasMany(GrowLightSchedule::class);
    }
}
