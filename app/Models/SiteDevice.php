<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteDevice extends Model
{
// Nama tabel didefinisikan secara eksplisit
    protected $table = 'site_devices';

    // Karena kita menggunakan ID (Primary Key) di tabel pivot
    public $incrementing = true;

    protected $fillable = [
        'site_id',
        'device_id',
        'started_at',
        'ended_at'
    ];

    // kolom started_at dan ended_at otomatis dianggap sebagai tanggal
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
}
