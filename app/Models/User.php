<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass Assignable
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'role',
        'status',
        'password',
    ];

    /**
     * Hidden Attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}