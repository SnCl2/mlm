<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionLevel extends Model
{
    // Mass assignable fields
    protected $fillable = [
        'level',
        'percentage',
    ];
}
