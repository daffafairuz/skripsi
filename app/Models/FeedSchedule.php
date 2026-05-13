<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedSchedule extends Model
{
    protected $fillable = [
        'site_id',
        'time',
        'last_time_active',
        'amount'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
