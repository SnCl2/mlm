<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'total_commission',
    ];

    /**
     * Relationship: ShopCommission belongs to Shop
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
