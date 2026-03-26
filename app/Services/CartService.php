<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;

class CartService
{
    // ── Helpers ────────────────────────────────────────────────

    /**
     * Returns the base query scoped to the current user or session.
     */
    private function query()
    {
        return CartItem::forCurrentUser()->with(['product.inventory', 'product.category']);
    }

    /**
     * Merge cart when guest logs in: re-assign session items to user_id.
     */
    public function mergeGuestCart(): void
    {
        if (! auth()->check()) return;

        $sessionId = session()->getId();

        CartItem::where('session_id', $sessionId)->each(function (CartItem $guestItem) {
            // Check if user already has this product in cart
            $existing = CartItem::where('user_id', auth()->id())
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($existing) {
                // Merge quantities, cap at stock
                $stock   = $guestItem->product?->getStockCount() ?? 0;
                $merged  = min($existing->quantity + $guestItem->quantity, $stock);
                $existing->update(['quantity' => $merged]);
                $guestItem->delete();
            } else {
                $guestItem->update([
                    'user_id'    => auth()->id(),
                    'session_id' => null,
                ]);
            }
        });
    }

    // ── Public API ─────────────────────────────────────────────

    /**
     * Return all cart items as an array compatible with PricingService.
     */
    public function getItems(): array
    {
        return $this->query()->get()->map(function (CartItem $item) {
            $stock = $item->product?->getStockCount() ?? 0;
            return [
                'product_id'  => $item->product_id,
                'name'        => $item->product?->name ?? 'Unknown',
                'image'       => $item->product?->image ?? null,
                'base_price'  => (float) $item->base_price,
                'unit_price'  => (float) $item->unit_price,
                'quantity'    => $item->quantity,
                'line_total'  => $item->line_total,
                'is_weekend'  => $item->is_weekend_price,
                'stock'       => $stock,
            ];
        })->keyBy('product_id')->toArray();
    }

    /**
     * Add or merge item. Returns status array.
     */
    public function addItem(int $productId, int $quantity): array
    {
        if ($quantity <= 0) {
            return ['status' => 'error', 'message' => 'Quantity must be at least 1.'];
        }

        $product = Product::with('inventory')->find($productId);

        if (! $product || ! $product->is_active) {
            return ['status' => 'error', 'message' => 'Product not found or unavailable.'];
        }

        $stock = $product->getStockCount();

        if ($stock === 0) {
            return ['status' => 'error', 'message' => "{$product->name} is out of stock."];
        }

        // Check existing cart row
        $existing    = $this->query()->where('product_id', $productId)->first();
        $existingQty = $existing ? $existing->quantity : 0;
        $totalQty    = $existingQty + $quantity;

        if ($totalQty > $stock) {
            return ['status' => 'error', 'message' => "Only {$stock} unit(s) available for {$product->name}."];
        }

        $effectivePrice = $product->getEffectivePrice();
        $isWeekend      = $product->isWeekendSurgeActive();

        $cartData = [
            'quantity'        => $totalQty,
            'unit_price'      => $effectivePrice,
            'base_price'      => (float) $product->base_price,
            'is_weekend_price'=> $isWeekend,
        ];

        if ($existing) {
            $existing->update($cartData);
        } else {
            $identifier = auth()->check()
                ? ['user_id' => auth()->id(), 'product_id' => $productId]
                : ['session_id' => session()->getId(), 'product_id' => $productId];

            CartItem::create(array_merge($identifier, $cartData));
        }

        return [
            'status'     => 'success',
            'message'    => "{$product->name} added to cart.",
            'cart_count' => $this->count(),
        ];
    }

    /**
     * Update quantity of a specific product in cart.
     */
    public function updateItem(int $productId, int $quantity): array
    {
        $item = $this->query()->where('product_id', $productId)->first();

        if (! $item) {
            return ['status' => 'error', 'message' => 'Item not found in cart.'];
        }

        $product = Product::with('inventory')->find($productId);
        $stock   = $product?->getStockCount() ?? 0;

        if ($quantity > $stock) {
            return ['status' => 'error', 'message' => "Only {$stock} unit(s) in stock."];
        }

        $item->update(['quantity' => $quantity]);

        return ['status' => 'success', 'message' => 'Cart updated.', 'cart_count' => $this->count()];
    }

    /**
     * Remove a product from cart.
     */
    public function removeItem(int $productId): void
    {
        $this->query()->where('product_id', $productId)->delete();
    }

    /**
     * Clear entire cart (and remove applied coupon from session).
     */
    public function clearCart(): void
    {
        $this->query()->delete();
        session()->forget('applied_coupon');
    }

    public function isEmpty(): bool
    {
        return $this->query()->doesntExist();
    }

    public function count(): int
    {
        return $this->query()->count();
    }
}
