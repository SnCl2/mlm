<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinaryWallet extends Model
{
    protected $fillable = ['user_id',  'matching_amount'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
