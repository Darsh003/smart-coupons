<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'coupon_id', 'coupon_code',
        'subtotal', 'item_discount', 'cart_discount', 'coupon_discount',
        'premium_discount', 'weekend_surcharge', 'tax_amount',
        'delivery_charge', 'total', 'is_weekend_order', 'status',
    ];

    protected $casts = [
        'subtotal'          => 'decimal:2',
        'item_discount'     => 'decimal:2',
        'cart_discount'     => 'decimal:2',
        'coupon_discount'   => 'decimal:2',
        'premium_discount'  => 'decimal:2',
        'weekend_surcharge' => 'decimal:2',
        'tax_amount'        => 'decimal:2',
        'delivery_charge'   => 'decimal:2',
        'total'             => 'decimal:2',
        'is_weekend_order'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
