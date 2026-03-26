<?php

namespace App\Services;

class PricingService
{
    private const TAX_RATE = 0.05;    // 5%
    private const FREE_DELIVERY_ABOVE = 500.00;
    private const DELIVERY_CHARGE = 50.00;
    private const ITEM_DISCOUNT_QTY = 3;       // qty >= 3 => 10% off item
    private const ITEM_DISCOUNT_PCT = 0.10;
    private const CART_DISCOUNT_ABOVE = 1000.00; // subtotal >= 1000 => 100 off
    private const CART_DISCOUNT_FLAT = 100.00;
    private const PREMIUM_DISCOUNT = 0.05;    // extra 5%
    private const WEEKEND_SURGE_PCT = 0.10;   // +10% on Fri, Sat, Sun
    private const MAX_DISCOUNT_CAP = 0.30;    // total discount never > 30% of subtotal

    /**
     * Master calculation method — 9-step pricing pipeline.
     */
    public function calculate(array $cartItems, ?string $couponCode, bool $isPremium): array
    {
        if (empty($cartItems)) {
            return $this->emptyBreakdown();
        }

        // ── Step 1: Subtotal + item-level discounts ─────────────
        $subtotal = 0.0;
        $itemDiscount = 0.0;
        $items = [];

        foreach ($cartItems as $item) {
            $lineTotal = $item['unit_price'] * $item['quantity'];
            $itemDisc = 0.0;

            if ($item['quantity'] >= self::ITEM_DISCOUNT_QTY) {
                $itemDisc = round($lineTotal * self::ITEM_DISCOUNT_PCT, 2);
            }

            $subtotal += $lineTotal;
            $itemDiscount += $itemDisc;

            $items[] = array_merge($item, [
                'item_discount' => $itemDisc,
                'net_line' => round($lineTotal - $itemDisc, 2),
            ]);
        }

        $subtotal = round($subtotal, 2);
        $itemDiscount = round($itemDiscount, 2);

        // ── Step 1.5: Weekend Surge (+10%) ───────────────────────
        $isWeekend = in_array(now()->dayOfWeek, [0, 6]);
        $weekendSurge = $isWeekend ? round($subtotal * self::WEEKEND_SURGE_PCT, 2) : 0.0;

        // ── Step 2: Cart-level discount ──────────────────────────
        $afterItemDiscount = $subtotal - $itemDiscount;
        $cartDiscount = ($subtotal >= self::CART_DISCOUNT_ABOVE)
            ? self::CART_DISCOUNT_FLAT
            : 0.0;

        // ── Step 3: Coupon discount ──────────────────────────────
        $couponDiscount = 0.0;
        $couponMessage = null;

        if ($couponCode) {
            $couponService = app(CouponService::class);
            $amountForCoupon = $afterItemDiscount - $cartDiscount;
            $couponResult = $couponService->applyDiscount($couponCode, max(0, $amountForCoupon));
            $couponDiscount = $couponResult['discount'];
            $couponMessage = $couponResult['message'];
        }

        // ── Step 4: Premium discount (5% on remaining) ───────────
        $premiumDiscount = 0.0;
        $afterCoupons = $afterItemDiscount - $cartDiscount - $couponDiscount;

        if ($isPremium && $afterCoupons > 0) {
            $premiumDiscount = round($afterCoupons * self::PREMIUM_DISCOUNT, 2);
        }

        // ── Step 5: Total discount cap (40%) ─────────────────────
        $totalDiscount = $itemDiscount + $cartDiscount + $couponDiscount + $premiumDiscount;
        $maxAllowed = round($subtotal * self::MAX_DISCOUNT_CAP, 2);

        if ($totalDiscount > $maxAllowed) {
            $ratio = $maxAllowed / $totalDiscount;
            $itemDiscount = round($itemDiscount * $ratio, 2);
            $cartDiscount = round($cartDiscount * $ratio, 2);
            $couponDiscount = round($couponDiscount * $ratio, 2);
            $premiumDiscount = round($premiumDiscount * $ratio, 2);
            $totalDiscount = $maxAllowed;
        }

        // ── Step 6: Pre-tax total ────────────────────────────────
        $preTaxTotal = max(0, $subtotal + $weekendSurge - $totalDiscount);

        // ── Step 7: Tax (5%) ─────────────────────────────────────
        $taxAmount = round($preTaxTotal * self::TAX_RATE, 2);

        // ── Step 8: Delivery ─────────────────────────────────────
        $deliveryCharge = ($preTaxTotal >= self::FREE_DELIVERY_ABOVE) ? 0.0 : self::DELIVERY_CHARGE;

        // ── Step 9: Grand total ──────────────────────────────────
        $grandTotal = max(0, round($preTaxTotal + $taxAmount + $deliveryCharge, 2));

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'item_discount' => $itemDiscount,
            'cart_discount' => $cartDiscount,
            'coupon_discount' => $couponDiscount,
            'premium_discount' => $premiumDiscount,
            'total_discount' => $totalDiscount,
            'pre_tax_total' => $preTaxTotal,
            'tax_rate' => self::TAX_RATE * 100,
            'tax_amount' => $taxAmount,
            'delivery_charge' => $deliveryCharge,
            'grand_total' => $grandTotal,
            'is_weekend' => $isWeekend,
            'weekend_surge' => $weekendSurge,
            'is_free_delivery' => $deliveryCharge === 0.0,
            'coupon_message' => $couponMessage,
            'coupon_applied' => !empty($couponCode),
            'applied_coupon' => !empty($couponCode) ? $couponCode : null,
            'is_premium' => $isPremium,
        ];
    }

    private function emptyBreakdown(): array
    {
        return [
            'items' => [],
            'subtotal' => 0,
            'item_discount' => 0,
            'cart_discount' => 0,
            'coupon_discount' => 0,
            'premium_discount' => 0,
            'total_discount' => 0,
            'pre_tax_total' => 0,
            'tax_rate' => self::TAX_RATE * 100,
            'tax_amount' => 0,
            'delivery_charge' => 0,
            'grand_total' => 0,
            'is_weekend' => false,
            'weekend_surge' => 0,
            'is_free_delivery' => false,
            'coupon_message' => null,
            'coupon_applied' => false,
            'is_premium' => false,
        ];
    }
}
