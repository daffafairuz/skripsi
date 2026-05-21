<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedSchedule extends Model
{
    protected $fillable = [
        'actuator_id',
        'time',
        'duration',
    ];

    public function actuator()
    {
        return $this->belongsTo(Actuator::class);
    }
}
