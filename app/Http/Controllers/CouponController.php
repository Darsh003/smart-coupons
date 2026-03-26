<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CouponService;
use App\Services\PricingService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        protected CouponService  $couponService,
        protected CartService    $cartService,
        protected PricingService $pricingService
    ) {}

    public function apply(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string|max:50']);

        $cartItems = $this->cartService->getItems();

        if (empty($cartItems)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your cart is empty. Add items before applying a coupon.',
            ], 422);
        }

        $subtotal = collect($cartItems)->sum('line_total');
        $result   = $this->couponService->validate($request->coupon_code, $subtotal);

        if ($result['valid']) {
            session(['applied_coupon' => strtoupper(trim($request->coupon_code))]);

            $breakdown = $this->pricingService->calculate(
                $cartItems,
                strtoupper(trim($request->coupon_code)),
                auth()->user()?->is_premium ?? false
            );

            return response()->json([
                'status'    => 'success',
                'message'   => $result['message'],
                'breakdown' => $breakdown,
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => $result['message'],
        ], 422);
    }

    public function remove()
    {
        session()->forget('applied_coupon');

        $cartItems = $this->cartService->getItems();
        $breakdown = $this->pricingService->calculate(
            $cartItems,
            null,
            auth()->user()?->is_premium ?? false
        );

        return response()->json([
            'status'    => 'success',
            'message'   => 'Coupon removed.',
            'breakdown' => $breakdown,
        ]);
    }
}
