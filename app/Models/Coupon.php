<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'max_discount',
        'min_order_value', 'is_combinable', 'is_active',
        'usage_limit', 'used_count', 'valid_from', 'valid_until',
    ];

    protected $casts = [
        'value'           => 'decimal:2',
        'max_discount'    => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'is_combinable'   => 'boolean',
        'is_active'       => 'boolean',
        'valid_from'      => 'datetime',
        'valid_until'     => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Checks all validity conditions.
     * Returns ['valid' => bool, 'message' => string]
     */
    public function isValid(float $cartSubtotal): array
    {
        if (! $this->is_active) {
            return ['valid' => false, 'message' => 'This coupon is not active.'];
        }

        if ($this->valid_from && now()->lt($this->valid_from)) {
            return ['valid' => false, 'message' => 'This coupon is not yet valid.'];
        }

        if ($this->valid_until && now()->gt($this->valid_until)) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'This coupon has reached its usage limit.'];
        }

        if ($cartSubtotal < (float) $this->min_order_value) {
            return [
                'valid'   => false,
                'message' => 'Minimum order value of ₹' . number_format($this->min_order_value, 2) . ' required.',
            ];
        }

        return ['valid' => true, 'message' => 'Coupon applied successfully!'];
    }

    /**
     * Calculate actual discount for a given subtotal.
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'flat') {
            return min((float) $this->value, $amount);
        }

        $discount = $amount * ((float) $this->value / 100);

        if ($this->max_discount !== null) {
            $discount = min($discount, (float) $this->max_discount);
        }

        return round($discount, 2);
    }
}
