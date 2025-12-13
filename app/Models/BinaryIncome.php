<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinaryIncome extends Model
{
    protected $fillable = ['user_id', 'amount', 'matches', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
