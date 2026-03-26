@extends('layouts.app')
@section('title', 'Order Confirmed')

@section('content')

<div class="max-w-xl mx-auto py-12 px-4">

    {{-- Balanced Success Header --}}
    <div class="text-center mb-10">
        <div class="relative inline-flex items-center justify-center w-16 h-16 bg-emerald-500 rounded-full mb-6 shadow-lg shadow-emerald-100">
            <div class="absolute inset-0 rounded-full bg-emerald-500 animate-ping opacity-10"></div>
            <svg class="w-8 h-8 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Order Confirmed!</h1>
        <p class="text-slate-500 text-sm">
            Thank you! Your order <strong class="text-indigo-600 font-mono">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</strong> has been placed.
        </p>
    </div>

    {{-- Normalized Receipt Card --}}
    <div class="card !p-0 overflow-hidden shadow-md">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="font-bold text-slate-700 text-xs uppercase tracking-widest">Order Summary</h2>
            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 uppercase">Confirmed</span>
        </div>

        <div class="px-6 py-5">
            {{-- Order Items --}}
            <div class="space-y-3 mb-6">
                @foreach($order->items as $item)
                <div class="flex justify-between items-baseline gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="font-semibold text-slate-900 text-sm truncate">{{ $item->product_name }}</span>
                            <span class="text-slate-400 text-xs">× {{ $item->quantity }}</span>
                        </div>
                        @if($item->item_discount > 0)
                            <span class="text-[10px] font-bold text-emerald-600 uppercase">−₹{{ number_format($item->item_discount, 2) }} disc. applied</span>
                        @endif
                    </div>
                    <span class="font-bold text-slate-900 text-sm whitespace-nowrap">₹{{ number_format($item->line_total, 2) }}</span>
                </div>
                @endforeach
            </div>

            {{-- Totals Section --}}
            <div class="space-y-2 pt-5 border-t border-slate-100 text-[13px]">
                <div class="flex justify-between text-slate-500 font-medium">
                    <span>Subtotal</span>
                    <span class="text-slate-800">₹{{ number_format($order->subtotal, 2) }}</span>
                </div>

                @if($order->item_discount > 0)
                <div class="flex justify-between text-emerald-600 font-semibold">
                    <span>Item Discount</span>
                    <span>−₹{{ number_format($order->item_discount, 2) }}</span>
                </div>
                @endif

                @if($order->cart_discount > 0)
                <div class="flex justify-between text-emerald-600 font-semibold">
                    <span>Cart Discount</span>
                    <span>−₹{{ number_format($order->cart_discount, 2) }}</span>
                </div>
                @endif

                @if($order->coupon_discount > 0)
                <div class="flex justify-between text-emerald-600 font-semibold">
                    <span>Coupon ({{ $order->coupon_code }})</span>
                    <span>−₹{{ number_format($order->coupon_discount, 2) }}</span>
                </div>
                @endif

                @if($order->premium_discount > 0)
                <div class="flex justify-between text-indigo-600 font-bold">
                    <span>★ Premium Benefit (5%)</span>
                    <span>−₹{{ number_format($order->premium_discount, 2) }}</span>
                </div>
                @endif

                <div class="flex justify-between text-slate-500 font-medium">
                    <span>GST (5%)</span>
                    <span class="text-slate-800">₹{{ number_format($order->tax_amount, 2) }}</span>
                </div>

                <div class="flex justify-between {{ $order->delivery_charge == 0 ? 'text-emerald-600 font-bold' : 'text-slate-500 font-medium' }}">
                    <span>Delivery</span>
                    <span>{{ $order->delivery_charge == 0 ? 'FREE' : '₹' . number_format($order->delivery_charge, 2) }}</span>
                </div>
            </div>

            {{-- Clean Grand Total Footer --}}
            <div class="mt-6 pt-6 border-t-2 border-slate-900 flex justify-between items-center">
                <span class="font-bold text-slate-900 text-base">Total Amount Paid</span>
                <span class="text-2xl font-black text-indigo-600 tracking-tighter">₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Bottom Action --}}
    <div class="text-center mt-10">
        <a href="{{ route('products.index') }}" class="btn-primary !px-10 !py-3 font-bold text-xs tracking-wider uppercase">
            ← Continue Shopping
        </a>
        <p class="text-[10px] text-slate-400 mt-6 font-bold uppercase tracking-widest">
            Order processing confirmed for {{ auth()->user()->email ?? 'customer' }}
        </p>
    </div>

</div>

</div>
@endsection
