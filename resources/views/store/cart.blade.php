@extends('layouts.app')

@section('title', 'Your Cart')

@push('styles')
    <style>
        .qty-stepper {
            display: flex;
            align-items: center;
            border: 1px solid var(--color-border);
            border-radius: 10px;
            overflow: hidden;
            background: var(--color-surface);
        }

        .qty-stepper button {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.15s;
            color: var(--color-text-muted);
        }

        .qty-stepper button:hover {
            background: var(--color-bg-soft);
            color: var(--color-text-secondary);
        }

        .qty-stepper span {
            width: 2rem;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--color-text);
        }

        .coupon-wrap {
            display: flex;
            align-items: center;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--color-border);
            background: var(--color-bg-soft);
            transition: border-color 0.2s;
        }

        .coupon-wrap:focus-within {
            border-color: var(--color-primary);
        }

        /* Discount row: flex when visible (JS toggles .hidden) */
        #pageDiscountRow {
            display: flex;
            justify-content: space-between;
        }
    </style>
@endpush

@section('content')
    <div style="background: var(--color-bg);" class="min-h-screen">
        <div class="max-w-8xl mx-auto px-4 py-6 md:py-10">

            {{-- Breadcrumb --}}
            <nav class="flex text-xs mb-6" style="color: var(--color-text-muted);">
                <ol class="flex items-center gap-1.5">
                    <li><a href="{{ route('home') }}" onmouseover="this.style.color='var(--color-primary)'"
                            onmouseout="this.style.color='var(--color-text-muted)'">Home</a></li>
                    <li style="color: var(--color-border);">›</li>
                    <li style="color: var(--color-text-secondary); font-weight: 500;">Cart</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                {{-- ════════════════════════════════
                 LEFT: Cart Items
            ════════════════════════════════ --}}
                <div class="lg:col-span-2">
                    <div class="card overflow-hidden">

                        {{-- Header --}}
                        <div class="px-6 pt-6 pb-0 flex items-center justify-between"
                            style="border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                            <h1 class="text-xl font-semibold" style="color: var(--color-text);">Shopping Cart</h1>
                            <span id="pageCartCount" class="text-sm font-medium" style="color: var(--color-text-muted);">0
                                items</span>
                        </div>

                        {{-- Items container (JS-injected) --}}
                        <div id="pageCartItems" class="px-6 py-4 space-y-0">
                            {{-- Loading skeleton --}}
                            <div id="pageCartSkeleton" class="space-y-4 animate-pulse">
                                @foreach (range(1, 2) as $i)
                                    <div class="flex items-center gap-4 py-5"
                                        style="border-bottom: 1px solid var(--color-border);">
                                        <div class="w-20 h-20 rounded-xl shrink-0"
                                            style="background: var(--color-bg-soft);"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="h-4 rounded-lg w-2/3" style="background: var(--color-bg-soft);">
                                            </div>
                                            <div class="h-3 rounded-lg w-1/3" style="background: var(--color-bg-soft);">
                                            </div>
                                            <div class="flex justify-between mt-3">
                                                <div class="h-8 w-28 rounded-xl" style="background: var(--color-bg-soft);">
                                                </div>
                                                <div class="h-5 w-20 rounded" style="background: var(--color-bg-soft);">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Continue shopping --}}
                        <div class="px-6 pb-5">
                            <a href="{{ route('shop') }}"
                                class="inline-flex items-center gap-2 text-sm font-semibold transition-colors duration-200"
                                style="color: var(--color-primary);"
                                onmouseover="this.style.color='var(--color-primary-hover)'"
                                onmouseout="this.style.color='var(--color-primary)'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════
                 RIGHT: Order Summary
            ════════════════════════════════ --}}
                <div class="lg:col-span-1" id="pageSummaryBox">
                    <div class="card p-6 sticky top-24">
                        <h2 class="text-lg font-semibold mb-5" style="color: var(--color-text);">Order Summary</h2>

                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span style="color: var(--color-text-muted);">Subtotal</span>
                                <span id="pageSubtotal" class="font-bold font-bengali"
                                    style="color: var(--color-text);">৳0</span>
                            </div>

                            <div id="pageDiscountRow" class="hidden text-sm">
                                <span style="color: var(--color-text-muted);">Coupon Discount</span>
                                <span id="pageDiscountAmount" class="font-bold font-bengali"
                                    style="color: var(--color-primary);">− ৳0</span>
                            </div>

                            <div style="border-top: 1px solid var(--color-border); margin: 8px 0;"></div>

                            <div class="flex justify-between items-center">
                                <span class="text-base font-semibold"
                                    style="color: var(--color-text-secondary);">Total</span>
                                <span id="pageTotal" class="text-2xl font-bold font-bengali"
                                    style="color: var(--color-primary);">৳0</span>
                            </div>
                        </div>

                        {{-- Coupon --}}
                        <div class="mt-5">
                            <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                                style="color: var(--color-text-muted);">Promo Code</label>
                            <div class="coupon-wrap">
                                <svg class="w-4 h-4 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    style="color: var(--color-primary);">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <input id="couponInput" type="text" placeholder="Enter promo code" autocomplete="off"
                                    class="flex-1 bg-transparent text-sm px-3 py-2.5 focus:outline-none uppercase tracking-wider"
                                    style="color: var(--color-text-secondary);">
                                <button id="couponApplyBtn"
                                    class="px-4 py-2.5 text-sm font-semibold shrink-0 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-200"
                                    style="background: var(--color-primary); color: white;"
                                    onmouseover="this.style.background='var(--color-primary-hover)'"
                                    onmouseout="this.style.background='var(--color-primary)'">
                                    Apply
                                </button>
                            </div>
                            <p id="couponFeedback" class="hidden text-xs mt-2 font-medium font-bengali"></p>
                        </div>

                        {{-- Checkout CTA --}}
                        <button id="pageCheckoutBtn"
                            class="btn-primary w-full py-3 mt-4 font-bold text-sm cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed active:scale-[.98] flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Proceed to Checkout
                        </button>

                        {{-- Trust --}}
                        <div class="flex justify-center gap-5 mt-4">
                            <div class="flex items-center gap-1 text-xs" style="color: var(--color-text-muted);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Secure Checkout
                            </div>
                            <div class="flex items-center gap-1 text-xs" style="color: var(--color-text-muted);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Quality Guarantee
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
