<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralIncome extends Model
{
    protected $fillable = ['user_id', 'new_user_id', 'amount', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newUser()
    {
        return $this->belongsTo(User::class, 'new_user_id');
    }
}
