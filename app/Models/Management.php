<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Management extends Authenticatable
{
    use Notifiable;

    protected $guard = 'management';

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
