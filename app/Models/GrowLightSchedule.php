<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrowLightSchedule extends Model
{
    protected $fillable = [
        'site_id',
        'start_time',
        'end_time',
    ];
}
