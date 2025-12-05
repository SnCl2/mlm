<?php

// app/Models/Withdrawal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id',
        'cashback_amount',
        'referral_amount',
        'binary_amount',
        'total_amount',
        'status',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
