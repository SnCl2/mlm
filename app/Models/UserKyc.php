<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserKyc extends Model
{
    protected $fillable = [
        'user_id',
        'profile_image',
        'pan_card_image',
        'aadhar_card_image',
        'alternate_phone',
        'bank_account_number',
        'ifsc_code',
        'upi_id',
        'status',

        // Newly added fields
        'aadhar_number',
        'pan_card',
        'bank_name',
        'country',
        'state',
        'city',
        'pincode',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
