<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Services\CartService;
use App\Services\PricingService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService    $cart,
        protected PricingService $pricing
    ) {}

    public function index()
    {
        $cartItems = $this->cart->getItems();
        $breakdown = $this->pricing->calculate(
            $cartItems,
            session('applied_coupon'),
            auth()->user()?->is_premium ?? false
        );

        return view('cart.index', compact('cartItems', 'breakdown'));
    }

    public function add(AddToCartRequest $request)
    {
        $result = $this->cart->addItem(
            (int) $request->product_id,
            (int) $request->quantity
        );

        if ($request->wantsJson()) {
            return response()->json($result, $result['status'] === 'success' ? 200 : 422);
        }

        return back()->with($result['status'], $result['message']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:100',
        ]);

        $result = $this->cart->updateItem(
            (int) $request->product_id,
            (int) $request->quantity
        );

        return response()->json($result);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);

        $this->cart->removeItem((int) $request->product_id);

        return response()->json([
            'status'     => 'success',
            'message'    => 'Item removed.',
            'cart_count' => $this->cart->count(),
        ]);
    }

    public function clear()
    {
        $this->cart->clearCart();
        return response()->json(['status' => 'success']);
    }

    /**
     * AJAX: returns live pricing breakdown for cart page
     */
    public function summary()
    {
        $cartItems = $this->cart->getItems();
        $breakdown = $this->pricing->calculate(
            $cartItems,
            session('applied_coupon'),
            auth()->user()?->is_premium ?? false
        );

        return response()->json([
            'cartCount' => $this->cart->count(),
            'breakdown' => $breakdown,
        ]);
    }
}
