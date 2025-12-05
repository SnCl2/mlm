<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivationKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'status',
        'assigned_to',
        'assigned_by',
        'used_at',
        'used_for',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(Management::class, 'assigned_by'); // Fixed class name
    }

    public function usedFor()
    {
        return $this->belongsTo(User::class, 'used_for');
    }
    
    public function transfers()
    {
        return $this->hasMany(ActivationKeyTransfer::class);
    }

}
