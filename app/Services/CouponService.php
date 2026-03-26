<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    /**
     * Validate a coupon code against cart subtotal.
     */
    public function validate(string $code, float $subtotal): array
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (! $coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code.'];
        }

        return $coupon->isValid($subtotal);
    }

    /**
     * Calculate and return discount amount for a given amount.
     */
    public function applyDiscount(string $code, float $amount): array
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (! $coupon) {
            return ['discount' => 0.0, 'message' => 'Coupon not found.'];
        }

        $discount = $coupon->calculateDiscount($amount);

        return [
            'discount' => $discount,
            'message'  => "Coupon '{$coupon->code}' applied — ₹{$discount} off!",
        ];
    }

    /**
     * Increment used_count after a successful order.
     */
    public function markUsed(string $code): void
    {
        Coupon::where('code', strtoupper(trim($code)))->increment('used_count');
    }
}
