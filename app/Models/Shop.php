<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Shop extends Authenticatable
{

    protected $fillable = [
        'name',
        'category',
        'owner_name',
        'phone',
        'email',
        'address',
        'aadhar_number',
        'pan_number',
        'aadhar_image_path',
        'pan_image_path',
        'latitude',
        'longitude',
        'commission_rate',
        'is_active',
        'password', // ✅ Important if you allow password login
    ];
    
    protected $hidden = ['password', 'remember_token'];

    public function transactions()
    {
        return $this->hasMany(ShopTransaction::class);
    }
    
    // app/Models/Shop.php

    public function commission()
    {
        return $this->hasOne(ShopCommission::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'recipient_id')
                    ->where('recipient_type', 'shop');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

}
