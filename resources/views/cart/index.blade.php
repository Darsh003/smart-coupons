@extends('layouts.app')
@section('title', 'Your Cart')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Your Cart</h1>
        @if(!empty($cartItems))
            <p class="page-subtitle">{{ count($cartItems) }} {{ Str::plural('item', count($cartItems)) }} in your selection</p>
        @endif
    </div>
</div>

@if(empty($cartItems))
    {{-- Empty Cart --}}
    <div class="empty-state">
        <div style="font-size: 3rem; margin-bottom: 1rem">🛍️</div>
        <h2 class="section-title" style="text-align: center">Your cart is empty</h2>
        <p class="page-subtitle" style="text-align: center; margin-bottom: 2rem">Add some products to get started</p>
        <a href="{{ route('products.index') }}" class="btn-primary">
            Continue Shopping
        </a>
    </div>

@else

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Cart Items Column ─────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-3" id="cart-items">

        @if($isPremium)
            <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2.5 text-amber-800 text-sm font-medium">
                <span>★</span>
                <span>You are a <strong>Premium Member</strong> — an extra 5% discount is applied to your order!</span>
            </div>
        @endif

        @foreach($cartItems as $item)
        <div class="card-sm flex gap-4 items-start" data-product-id="{{ $item['product_id'] }}">

            {{-- Product Image Thumbnail --}}
            <div class="cart-item-img-wrap shrink-0">
                @if(!empty($item['image']))
                    <img src="{{ asset('images/' . $item['image']) }}"
                         alt="{{ $item['name'] }}"
                         class="w-16 h-16 rounded-md object-cover border border-slate-100 shadow-sm"
                         loading="lazy"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="bg-slate-50 flex items-center justify-center rounded-md w-16 h-16 text-xl border border-slate-100" style="display:none">🛍️</div>
                @else
                    <div class="bg-slate-50 flex items-center justify-center rounded-md w-16 h-16 text-xl border border-slate-100">🛍️</div>
                @endif
            </div>

            {{-- Item Details --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm leading-tight truncate">{{ $item['name'] }}</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-slate-500 text-xs font-medium">₹{{ number_format($item['unit_price'], 2) }}</span>
                            @if($item['is_weekend'])
                                <span class="text-[9px] font-bold text-amber-600 bg-amber-50 px-1 rounded border border-amber-100/50">WEEKEND</span>
                            @endif
                        </div>
                        <p class="text-[10px] text-emerald-600 mt-2 font-bold item-discount-text {{ $item['quantity'] >= 3 ? '' : 'hidden' }}">✓ 10% item discount applied</p>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="font-bold text-slate-900 text-sm item-line-total">₹{{ number_format($item['line_total'], 2) }}</div>
                        @php
                            $itemDisc = $breakdown['items'][$loop->index]['item_discount'] ?? 0;
                        @endphp
                        <div class="text-[10px] font-bold text-emerald-600 mt-0.5 item-discount-amount {{ $itemDisc > 0 ? '' : 'hidden' }}">−₹{{ number_format($itemDisc, 2) }}</div>
                    </div>
                </div>

                {{-- Row Controls --}}
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-50">
                    <div class="flex items-center gap-3">
                        <div class="qty-ctrl !h-8 !border-slate-200">
                            <button type="button" class="qty-btn qty-dec w-8 disabled:opacity-30">−</button>
                            <input type="number"
                                   value="{{ $item['quantity'] }}"
                                   min="1"
                                   max="{{ $item['stock'] }}"
                                   class="qty-input !w-9 !text-xs border-0 pointer-events-none"
                                   data-product-id="{{ $item['product_id'] }}"
                                   readonly
                                   aria-label="Quantity">
                            <button type="button" class="qty-btn qty-inc w-8">+</button>
                        </div>
                        <a href="{{ route('products.index') }}" class="text-[10px] font-bold text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-wider">
                             Add more
                        </a>
                    </div>

                    <button class="remove-btn text-[10px] font-bold text-slate-400 hover:text-red-500 transition-colors flex items-center gap-1 uppercase tracking-wider"
                            data-product-id="{{ $item['product_id'] }}">
                        🗑 Remove
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        <div class="flex justify-end pt-2">
            <button id="clear-cart-btn" class="text-xs text-slate-400 hover:text-red-500 transition-colors">
                Clear entire cart
            </button>
        </div>
    </div>

    {{-- ── Summary Sidebar ───────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Coupon Box --}}
        <div class="card">
            <h2 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Apply Coupon</h2>

            <div id="coupon-message" class="hidden text-sm mb-4 px-3 py-2 rounded-lg animate-fadeIn border"></div>

            <div id="coupon-box-contents">
                @if(session('applied_coupon'))
                    <div class="flex items-center justify-between bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2">
                        <span class="text-emerald-800 font-bold text-xs">
                            <span class="font-mono bg-emerald-100/50 px-1 rounded border border-emerald-200">{{ session('applied_coupon') }}</span> active
                        </span>
                        <button id="remove-coupon" class="text-[10px] font-bold text-red-500 hover:underline">Remove</button>
                    </div>
                @else
                    <div class="flex gap-2">
                        <input type="text"
                               id="coupon-input"
                               placeholder="PROMO CODE"
                               class="input flex-1 uppercase tracking-wider font-bold !py-2 !px-3 !text-sm"
                               maxlength="50">
                        <button id="apply-coupon" class="btn-accent whitespace-nowrap !py-2 !px-4 shadow-sm">Apply</button>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5 mt-3">
                        <span class="text-[9px] text-slate-400 font-bold uppercase">Try:</span>
                        @foreach(['SAVE10', 'FLAT200', 'BIGSALE'] as $c)
                            <button onclick="document.getElementById('coupon-input').value='{{$c}}'"
                                    class="font-mono text-[9px] bg-slate-100 hover:bg-slate-200 border border-slate-200 px-1.5 py-0.5 rounded transition-colors text-slate-600 font-bold">
                                {{$c}}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Pricing Breakdown --}}
        <div class="card" id="pricing-breakdown">
            @include('partials.pricing-breakdown', ['breakdown' => $breakdown])
        </div>

    </div>
</div>

@endif
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Cart Event Delegation (Remove & Quantity) ────────────────
document.getElementById('cart-items')?.addEventListener('click', async (e) => {
    // 1. Remove Item Logic
    const removeBtn = e.target.closest('.remove-btn');
    if (removeBtn) {
        const productId = removeBtn.dataset.productId;
        const card      = document.querySelector(`.card-sm[data-product-id="${productId}"]`);
        
        const res = await fetch('/cart/remove', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ product_id: productId }),
        });
        const data = await res.json();
        
        if (data.status === 'success') {
            if (card) {
                card.style.opacity = '0';
                card.style.transform = 'translateX(15px)';
                card.style.transition = 'all 0.3s ease-out';
                setTimeout(() => {
                    card.remove();
                    checkEmptyCart();
                    refreshBreakdown();
                }, 310);
            } else {
                refreshBreakdown();
            }
        }
        return;
    }

    // 2. Quantity Decrement
    const decBtn = e.target.closest('.qty-dec');
    if (decBtn) {
        const input = decBtn.closest('.qty-ctrl').querySelector('.qty-input');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
        return;
    }

    // 3. Quantity Increment
    const incBtn = e.target.closest('.qty-inc');
    if (incBtn) {
        const input = incBtn.closest('.qty-ctrl').querySelector('.qty-input');
        const max   = parseInt(input.max) || 100;
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
        return;
    }
});

// ── Quantity Update Handler (Debounced) ────────────────────
async function updateQty(input) {
    const productId = input.dataset.productId;
    const quantity  = parseInt(input.value);
    if (isNaN(quantity) || quantity < 1) { input.value = 1; return; }

    const res  = await fetch('/cart/update', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity }),
    });
    const data = await res.json();

    if (data.status === 'success') {
        refreshBreakdown();
    } else {
        showToast(data.message, 'error');
        input.value = input.defaultValue;
    }
}

let timer;
document.addEventListener('input', (e) => {
    if (e.target.classList.contains('qty-input')) {
        clearTimeout(timer);
        timer = setTimeout(() => updateQty(e.target), 600);
    }
});

// ── Clear Cart ─────────────────────────────────────────────
document.getElementById('clear-cart-btn')?.addEventListener('click', async () => {
    if (!confirm('Remove all items from cart?')) return;
    await fetch('/cart/clear', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    location.reload();
});

// ── Coupon Logic (Apply/Remove) ──────────────────────────────
async function applyCoupon() {
    const code = document.getElementById('coupon-input')?.value.trim();
    const msg  = document.getElementById('coupon-message');
    if (!code) { showCouponMsg(msg, 'Please enter a coupon code.', 'error'); return; }

    const res  = await fetch('/coupon/apply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ coupon_code: code }),
    });
    const data = await res.json();

    if (data.status === 'success') {
        showCouponMsg(msg, data.message, 'success');
        renderBreakdown(data.breakdown);
    } else {
        showCouponMsg(msg, data.message, 'error');
    }
}

async function removeCoupon() {
    const res = await fetch('/coupon/remove', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.status === 'success') {
        const msg = document.getElementById('coupon-message');
        if (msg) msg.classList.add('hidden'); // Clear message on remove
        renderBreakdown(data.breakdown);
    }
}

function bindCouponEvents() {
    document.getElementById('apply-coupon')?.addEventListener('click', applyCoupon);
    document.getElementById('remove-coupon')?.addEventListener('click', removeCoupon);
}

// Initial binding
bindCouponEvents();

// ── Live Breakdown Refresh ─────────────────────────────────
async function refreshBreakdown() {
    const res  = await fetch('/cart/summary', { headers: { 'Accept': 'application/json' } });
    const data = await res.json();
    updateCartCount(data.cartCount);
    renderBreakdown(data.breakdown);
}

function renderBreakdown(b) {
    const el = document.getElementById('pricing-breakdown');
    if (!el || !b) return;

    // A. Smart Update for Coupon Box (Only if state changed)
    const couponBox = document.getElementById('coupon-box-contents');
    if (couponBox) {
        const currentCode = couponBox.querySelector('.font-mono')?.textContent.trim() || '';
        const serverCode  = b.applied_coupon || '';

        if (currentCode !== serverCode) {
            if (serverCode) {
                couponBox.innerHTML = `
                    <div class="flex items-center justify-between bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2 animate-fadeIn">
                        <span class="text-emerald-800 font-bold text-xs uppercase">
                            <span class="font-mono bg-emerald-100/50 px-1 rounded border border-emerald-200">${b.applied_coupon}</span> active
                        </span>
                        <button id="remove-coupon" class="text-[10px] font-bold text-red-500 hover:underline">Remove</button>
                    </div>`;
            } else {
                couponBox.innerHTML = `
                    <div class="flex gap-2 animate-fadeIn">
                        <input type="text" id="coupon-input" placeholder="PROMO CODE" class="input flex-1 uppercase tracking-wider font-bold !py-2 !px-3 !text-sm" maxlength="50">
                        <button id="apply-coupon" class="btn-accent whitespace-nowrap !py-2 !px-4 shadow-sm">Apply</button>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5 mt-3">
                        <span class="text-[9px] text-slate-400 font-bold uppercase">Try:</span>
                        ${['SAVE10', 'FLAT200', 'BIGSALE'].map(c => `
                            <button onclick="document.getElementById('coupon-input').value='${c}'"
                                    class="font-mono text-[9px] bg-slate-100 hover:bg-slate-200 border border-slate-200 px-1.5 py-0.5 rounded transition-colors text-slate-600 font-bold">
                                ${c}
                            </button>`).join('')}
                    </div>`;
            }
            bindCouponEvents();
        }
    }

    const row = (label, amount, cls) =>
        `<div class="${cls}"><span>${label}</span><span>−₹${fmt(amount)}</span></div>`;

    el.innerHTML = `
      <h2 class="section-title" style="font-size: 1rem; margin-bottom: 1.25rem">Price Breakdown</h2>
      <div class="space-y-2.5 text-sm">
        <div class="price-row"><span>Subtotal</span><span class="font-semibold text-slate-900">₹${fmt(b.subtotal)}</span></div>
        ${b.is_weekend ? `
            <div class="flex justify-between items-center text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-100 uppercase tracking-tight animate-fadeIn">
                <span>Weekend Surge (+10%)</span>
                <span>+₹${fmt(b.weekend_surge)}</span>
            </div>` : ''}
        ${b.item_discount   > 0 ? row('Item Discount (qty ≥ 3)', b.item_discount,   'price-row-discount') : ''}
        ${b.cart_discount   > 0 ? row('Cart Discount (≥₹1,000)', b.cart_discount,   'price-row-discount') : ''}
        ${b.coupon_discount > 0 ? row('Coupon Discount',          b.coupon_discount, 'price-row-discount') : ''}
        ${b.premium_discount > 0 ? row('★ Premium Discount (5%)', b.premium_discount,'price-row-premium') : ''}
        ${b.total_discount > 0 ? `<div class="border-t border-dashed border-slate-200 pt-2.5 price-row-discount"><span class="font-semibold">Total Savings</span><span class="font-semibold">−₹${fmt(b.total_discount)}</span></div>` : ''}
        <div class="border-t border-slate-100 pt-2.5 price-row"><span>Pre-Tax Total</span><span class="font-medium text-slate-900">₹${fmt(b.pre_tax_total)}</span></div>
        <div class="price-row"><span>GST (${b.tax_rate}%)</span><span>₹${fmt(b.tax_amount)}</span></div>
        <div class="price-row ${b.is_free_delivery ? 'text-emerald-600 font-medium' : ''}">
          <span>Delivery</span><span>${b.is_free_delivery ? '🚚 FREE' : '₹' + fmt(b.delivery_charge)}</span>
        </div>
        ${!b.is_free_delivery ? `<p class="text-[10px] text-slate-400 text-right mt-1 italic">Spend ₹${fmt(Math.max(0, 500 - b.pre_tax_total))} more for FREE delivery</p>` : ''}
      </div>
      <div class="border-t-2 border-slate-900 mt-4 pt-4 flex justify-between items-center">
        <span class="font-bold text-slate-900 text-base">Grand Total</span>
        <span class="font-extrabold text-indigo-600 text-2xl">₹${fmt(b.grand_total)}</span>
      </div>
      ${b.grand_total > 0 ? `
      <form action="/order/place" method="POST" class="mt-5" id="place-order-form">
        <input type="hidden" name="_token" value="${CSRF}">
        <button type="submit" id="place-order-btn" class="btn-primary w-full !py-3 !text-base">Place Order →</button>
      </form>` : ''}
    `;

    // A. Sync existing items
    b.items.forEach(item => {
        const row = document.querySelector(`.card-sm[data-product-id="${item.product_id}"]`);
        if (row) {
            const lineTotalEl = row.querySelector('.item-line-total');
            if (lineTotalEl) lineTotalEl.textContent = '₹' + fmt(item.line_total);
            
            const discText = row.querySelector('.item-discount-text');
            if (discText) item.quantity >= 3 ? discText.classList.remove('hidden') : discText.classList.add('hidden');
            
            const discAmount = row.querySelector('.item-discount-amount');
            if (discAmount) {
                if (item.item_discount > 0) {
                    discAmount.textContent = '−₹' + fmt(item.item_discount);
                    discAmount.classList.remove('hidden');
                } else {
                    discAmount.classList.add('hidden');
                }
            }
        }
    });

    // B. Prune items that were removed on server but still in DOM
    const serverProductIds = b.items.map(i => i.product_id.toString());
    document.querySelectorAll('#cart-items .card-sm[data-product-id]').forEach(row => {
        const pid = row.dataset.productId;
        if (!serverProductIds.includes(pid)) {
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
        }
    });

    // re-bind submit guard
    document.getElementById('place-order-form')?.addEventListener('submit', () => {
        const btn = document.getElementById('place-order-btn');
        btn.textContent = 'Placing Order…';
        btn.disabled    = true;
    });
}

function fmt(n) {
    return parseFloat(n).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function showCouponMsg(el, text, type) {
    el.textContent = text;
    el.className   = `text-sm mb-3 px-3 py-2 rounded-lg animate-fadeIn ${type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-600 border border-red-200'}`;
    el.classList.remove('hidden');
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-5 right-5 z-50 px-5 py-3 rounded-lg text-sm font-medium shadow-lg animate-fadeIn
        ${type === 'error' ? 'bg-red-600 text-white' : 'bg-slate-800 text-white'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

function checkEmptyCart() {
    if (!document.querySelector('#cart-items [data-product-id]')) location.reload();
}
</script>
@endpush
