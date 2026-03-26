<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected PricingService $pricing,
        protected CouponService $couponService
    ) {
    }

    public function place(Request $request)
    {
        $cartItems = $this->cart->getItems();

        if (empty($cartItems)) {
            return redirect()->route('products.index')
                ->with('error', 'Your cart is empty. Add some products first.');
        }

        $couponCode = session('applied_coupon');
        $isPremium = auth()->user()?->is_premium ?? false;
        $breakdown = $this->pricing->calculate($cartItems, $couponCode, $isPremium);

        try {
            DB::beginTransaction();

            // 1. Create the order
            $order = Order::create([
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'coupon_code' => $couponCode,
                'subtotal' => $breakdown['subtotal'],
                'item_discount' => $breakdown['item_discount'],
                'cart_discount' => $breakdown['cart_discount'],
                'coupon_discount' => $breakdown['coupon_discount'],
                'premium_discount' => $breakdown['premium_discount'],
                'tax_amount' => $breakdown['tax_amount'],
                'delivery_charge' => $breakdown['delivery_charge'],
                'total' => $breakdown['grand_total'],
                'is_weekend_order' => in_array(now()->dayOfWeek, [0, 6]),
                'status' => 'confirmed',
            ]);

            // 2. Create order items & decrement inventory
            foreach ($breakdown['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'item_discount' => $item['item_discount'],
                    'line_total' => $item['net_line'],
                ]);

                // Atomic decrement — prevents negative stock
                $affected = DB::table('inventory')
                    ->where('product_id', $item['product_id'])
                    ->where('quantity', '>=', $item['quantity'])
                    ->decrement('quantity', $item['quantity']);

                if ($affected === 0) {
                    DB::rollBack();
                    return redirect()->route('cart.index')
                        ->with('error', "Sorry, {$item['name']} went out of stock. Please update your cart.");
                }
            }

            // 3. Mark coupon as used
            if ($couponCode) {
                $this->couponService->markUsed($couponCode);
            }

            // 4. Clear the cart
            $this->cart->clearCart();

            DB::commit();

            return redirect()->route('order.confirmation', $order)
                ->with('success', 'Your order has been placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')
                ->with('error', 'Something went wrong placing your order. Please try again.');
        }
    }

    public function confirmation(Order $order)
    {
        // Security: only allow the order owner or guest who placed it this session
        if ($order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items');
        return view('orders.confirmation', compact('order'));
    }
}
