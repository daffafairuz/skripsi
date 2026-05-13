<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
protected $fillable = [
        'site_id',
        'message',
        'is_read',
        'type'
    ];


    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
