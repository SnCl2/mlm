<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivationKeyTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'activation_key_id',
        'from_user_id',
        'to_user_id',
        'transferred_at',
    ];

    public $timestamps = false;

    // Relationships

    public function activationKey()
    {
        return $this->belongsTo(ActivationKey::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
