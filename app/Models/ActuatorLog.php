<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActuatorLog extends Model
{
    protected $fillable = [
        'actuator_id',
        'action',
        'triggered_by'
    ];
    /**
     * Relasi: Log ini merujuk pada satu Actuator tertentu.
     */
    public function actuator(): BelongsTo
    {
        return $this->belongsTo(Actuator::class);
    }
}
