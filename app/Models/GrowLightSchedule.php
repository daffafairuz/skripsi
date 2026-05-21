<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrowLightSchedule extends Model
{
    protected $fillable = [
        'actuator_id',
        'start_time',
        'end_time',
    ];

    public function actuator()
    {
        return $this->belongsTo(Actuator::class);
    }
}
