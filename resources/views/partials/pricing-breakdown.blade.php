<h2 class="section-title" style="font-size: 1rem; margin-bottom: 1.25rem">Price Breakdown</h2>

<div class="space-y-2.5 text-sm">

    <div class="price-row">
        <span>Subtotal</span>
        <span class="font-semibold text-slate-900">₹{{ number_format($breakdown['subtotal'], 2) }}</span>
    </div>

    @if($breakdown['is_weekend'])
    <div class="flex justify-between items-center text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-100 uppercase tracking-tight">
        <span>Weekend Surge (+10%)</span>
        <span>+₹{{ number_format($breakdown['weekend_surge'], 2) }}</span>
    </div>
    @endif

    @if($breakdown['item_discount'] > 0)
    <div class="price-row-discount">
        <span>Item Discount (qty ≥ 3)</span>
        <span>−₹{{ number_format($breakdown['item_discount'], 2) }}</span>
    </div>
    @endif

    @if($breakdown['cart_discount'] > 0)
    <div class="price-row-discount">
        <span>Cart Discount (≥₹1,000)</span>
        <span>−₹{{ number_format($breakdown['cart_discount'], 2) }}</span>
    </div>
    @endif

    @if($breakdown['coupon_discount'] > 0)
    <div class="price-row-discount">
        <span>Coupon Discount</span>
        <span>−₹{{ number_format($breakdown['coupon_discount'], 2) }}</span>
    </div>
    @endif

    @if($breakdown['premium_discount'] > 0)
    <div class="price-row-premium">
        <span>★ Premium Discount (5%)</span>
        <span>−₹{{ number_format($breakdown['premium_discount'], 2) }}</span>
    </div>
    @endif

    @if($breakdown['total_discount'] > 0)
    <div class="border-t border-dashed border-slate-200 pt-2.5 price-row-discount">
        <span class="font-semibold">Total Savings</span>
        <span class="font-semibold">−₹{{ number_format($breakdown['total_discount'], 2) }}</span>
    </div>
    @endif

    <div class="border-t border-slate-100 pt-2.5 price-row">
        <span>Pre-Tax Total</span>
        <span class="font-medium text-slate-900">₹{{ number_format($breakdown['pre_tax_total'], 2) }}</span>
    </div>

    <div class="price-row">
        <span>GST ({{ $breakdown['tax_rate'] }}%)</span>
        <span>₹{{ number_format($breakdown['tax_amount'], 2) }}</span>
    </div>

    <div class="price-row {{ $breakdown['is_free_delivery'] ? 'text-emerald-600 font-medium' : '' }}">
        <span>Delivery</span>
        <span>
            @if($breakdown['is_free_delivery'])
                🚚 FREE
            @else
                ₹{{ number_format($breakdown['delivery_charge'], 2) }}
            @endif
        </span>
    </div>

    @if(!$breakdown['is_free_delivery'])
    <p class="text-[10px] text-slate-400 text-right mt-1 italic">
        Spend ₹{{ number_format(max(0, 500 - $breakdown['pre_tax_total']), 2) }} more for FREE delivery
    </p>
    @endif

</div>

{{-- Grand Total --}}
<div class="border-t-2 border-slate-900 mt-4 pt-4 flex justify-between items-center">
    <span class="font-bold text-slate-900 text-base">Grand Total</span>
    <span class="font-extrabold text-indigo-600 text-2xl">₹{{ number_format($breakdown['grand_total'], 2) }}</span>
</div>

{{-- Place Order Button --}}
@if($breakdown['grand_total'] > 0)
<form action="{{ route('order.place') }}" method="POST" class="mt-5" id="place-order-form">
    @csrf
    <button type="submit"
            id="place-order-btn"
            class="btn-primary w-full !py-3 !text-base">
        Place Order →
    </button>
</form>
<script>
    document.getElementById('place-order-form')?.addEventListener('submit', () => {
        const btn = document.getElementById('place-order-btn');
        btn.textContent = 'Placing Order…';
        btn.disabled    = true;
    });
</script>
@endif
