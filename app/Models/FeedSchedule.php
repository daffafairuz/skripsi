<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedSchedule extends Model
{
    protected $fillable = [
        'site_id',
        'time',
        'duration',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
