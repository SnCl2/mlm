<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralWallet extends Model
{
    protected $fillable = ['user_id', 'new_user_id', 'parent_id', 'amount'];

    public function user()
    {
        return $this->belongsTo(User::class); // Who earned
    }

    public function newUser()
    {
        return $this->belongsTo(User::class, 'new_user_id');
    }

    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}
