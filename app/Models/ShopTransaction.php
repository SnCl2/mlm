<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopTransaction extends Model
{
    protected $fillable = [
        'user_id', 'shop_id', 'purchase_amount', 'commission_amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
  