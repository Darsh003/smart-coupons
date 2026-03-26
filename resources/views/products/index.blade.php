@extends('layouts.app')
@section('title', 'Products')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">All Products</h1>
        <p class="page-subtitle">{{ $products->total() }} products available</p>
    </div>
    @auth
        @if(auth()->user()->is_premium)
            <div class="premium-notice">
                <span>★</span>
                <span>Premium member — extra 5% discount on every order</span>
            </div>
        @endif
    @endauth
</div>

{{-- ── Weekend Surge Banner ─────────────────────────────────── --}}
@if(now()->isWeekend())
    <div class="surge-banner">
        <span class="surge-icon">⚡</span>
        <div>
            <strong>Weekend Surge Pricing Active</strong>
            <span class="surge-sub"> — All prices include a +10% weekend surcharge</span>
        </div>
    </div>
@endif

{{-- ── Product Grid ─────────────────────────────────────────── --}}
<div class="product-grid">
    @forelse($products as $product)
        @php
            $effectivePrice = $product->getEffectivePrice();
            $inStock        = $product->isInStock();
            $stock          = $product->getStockCount();
            $isWeekend      = $product->isWeekendSurgeActive();
            $isLowStock     = $inStock && $stock <= 5;
        @endphp

        <div class="product-card {{ !$inStock ? 'product-card--oos' : '' }}" id="product-{{ $product->id }}">

            {{-- Product Image — strict 1:1 ratio container --}}
            <div class="product-img-wrap">
                @if($product->image)
                    <img
                        src="{{ asset('images/' . $product->image) }}"
                        alt="{{ $product->name }}"
                        class="product-img"
                        loading="lazy"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="product-img-fallback" style="display:none">
                        🛍️
                    </div>
                @else
                    <div class="product-img-fallback">🛍️</div>
                @endif

                {{-- Stock overlay badge --}}
                @if(!$inStock)
                    <div class="img-overlay-badge img-overlay-badge--oos">Out of Stock</div>
                @elseif($isLowStock)
                    <div class="img-overlay-badge img-overlay-badge--low">Only {{ $stock }} left</div>
                @endif

                {{-- Weekend price badge on image --}}
                @if($isWeekend && $inStock)
                    <div class="img-overlay-badge img-overlay-badge--surge">⚡ +10%</div>
                @endif
            </div>

            {{-- Card Body --}}
            <div class="product-body">

                {{-- Category --}}
                <span class="product-category">{{ $product->category?->name ?? 'General' }}</span>

                {{-- Name --}}
                <h3 class="product-name">{{ $product->name }}</h3>

                {{-- Description --}}
                <p class="product-desc">{{ Str::limit($product->description, 70) }}</p>

                {{-- Pricing --}}
                <div class="product-pricing">
                    <span class="product-price">₹{{ number_format($effectivePrice, 2) }}</span>
                    @if($isWeekend)
                        <span class="product-price-original">₹{{ number_format($product->base_price, 2) }}</span>
                    @endif
                </div>

                {{-- Add to Cart --}}
                <div class="product-footer">
                    @if($inStock)
                        <form class="add-to-cart-form" data-product-id="{{ $product->id }}" data-name="{{ $product->name }}">
                            @csrf
                            <div class="qty-ctrl">
                                <button type="button" class="qty-btn qty-dec">−</button>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $stock }}"
                                       class="qty-input" aria-label="Quantity">
                                <button type="button" class="qty-btn qty-inc">+</button>
                            </div>
                            <button type="submit" id="add-btn-{{ $product->id }}" class="btn-add-cart">
                                Add to Cart
                            </button>
                        </form>
                    @else
                        <button disabled class="btn-oos">Out of Stock</button>
                    @endif
                </div>

            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="text-4xl mb-3">📦</div>
            <p class="font-semibold text-slate-700">No products found.</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="mt-10">
    {{ $products->links() }}
</div>

@endsection

@push('scripts')
<script>
// ── Quantity +/- controls ───────────────────────────────────
document.querySelectorAll('.qty-dec').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = btn.closest('.qty-ctrl').querySelector('.qty-input');
        if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
    });
});
document.querySelectorAll('.qty-inc').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = btn.closest('.qty-ctrl').querySelector('.qty-input');
        const max   = parseInt(input.max) || 100;
        if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
    });
});

// ── Add to Cart (AJAX) ──────────────────────────────────────
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const productId = form.dataset.productId;
        const productName = form.dataset.name;
        const btn       = document.getElementById('add-btn-' + productId);
        const qty       = form.querySelector('[name="quantity"]').value;
        const csrf      = document.querySelector('meta[name="csrf-token"]').content;

        const orig = btn.textContent.trim();
        btn.textContent = 'Adding…';
        btn.disabled    = true;
        btn.classList.add('btn-add-cart--loading');

        try {
            const res  = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: parseInt(productId), quantity: parseInt(qty) }),
            });
            const data = await res.json();

            if (data.status === 'success') {
                btn.textContent = '✓ Added!';
                btn.classList.remove('btn-add-cart--loading');
                btn.classList.add('btn-add-cart--success');
                updateCartCount(data.cart_count);
                showToast('✓ ' + productName + ' added to cart', 'success');
                setTimeout(() => {
                    btn.textContent = orig;
                    btn.classList.remove('btn-add-cart--success');
                    btn.disabled    = false;
                }, 2000);
            } else {
                showToast(data.message || 'Could not add to cart', 'error');
                btn.textContent = orig;
                btn.classList.remove('btn-add-cart--loading');
                btn.disabled    = false;
            }
        } catch {
            showToast('Network error. Please try again.', 'error');
            btn.textContent = orig;
            btn.classList.remove('btn-add-cart--loading');
            btn.disabled    = false;
        }
    });
});

function showToast(message, type = 'info') {
    const existingToasts = document.querySelectorAll('.toast-msg');
    existingToasts.forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = 'toast-msg ' + (type === 'success' ? 'toast-success' : 'toast-error');
    toast.textContent = message;
    document.body.appendChild(toast);
    // Force reflow for animation
    toast.offsetHeight;
    toast.classList.add('toast-visible');
    setTimeout(() => {
        toast.classList.remove('toast-visible');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endpush
