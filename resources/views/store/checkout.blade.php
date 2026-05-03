@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
<style>
    /* ── Form inputs ── */
    .co-input {
        width: 100%;
        border-radius: 12px;
        border: 1px solid var(--color-border);
        background: var(--color-surface);
        color: var(--color-text);
        padding: 10px 16px;
        font-size: 0.875rem;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        resize: none;
    }
    .co-input::placeholder { color: var(--color-text-placeholder); }
    .co-input:focus { border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb),0.12); }
    .co-input.is-error { border-color: var(--color-danger); }

    /* ── Field label ── */
    .co-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--color-text-muted);
        margin-bottom: 6px;
    }

    /* ── Step badge ── */
    .step-badge {
        width: 1.75rem; height: 1.75rem;
        border-radius: 50%;
        background: var(--color-primary);
        color: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; font-weight: 700;
        flex-shrink: 0;
    }

    /* ── Payment radio ── */
    .payment-label {
        display: flex; align-items: center; gap: 1rem;
        padding: 1rem;
        border-radius: 14px;
        border: 2px solid var(--color-border);
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        flex: 1;
    }
    .payment-label:hover { border-color: var(--color-accent); }
    .payment-label:has(input:checked) {
        border-color: var(--color-primary);
        background: var(--color-bg-soft);
    }

    /* ── Payment icon box ── */
    .payment-icon {
        width: 2.5rem; height: 2.5rem;
        border-radius: 10px;
        background: var(--color-bg-soft);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* ── Coupon bar ── */
    .coupon-bar {
        display: flex; align-items: center;
        border-radius: 12px;
        border: 1px solid var(--color-border);
        background: var(--color-bg-soft);
        overflow: hidden;
        transition: border-color 0.2s;
    }
    .coupon-bar:focus-within { border-color: var(--color-primary); }

    /* ── Discount rows: flex when visible (JS toggles .hidden to reveal) ── */
    #coDiscountRow { display: flex; justify-content: space-between; }

    /* ── Skeleton block ── */
    .skel { border-radius: 10px; background: var(--color-bg-soft); animation: pulse 1.5s ease-in-out infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
</style>
@endpush

@section('content')
<div style="background: var(--color-bg);" class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-6 md:py-10">

        {{-- Breadcrumb --}}
        <nav class="flex text-xs mb-6" style="color: var(--color-text-muted);">
            <ol class="flex items-center gap-1.5">
                <li><a href="{{ route('home') }}"
                       onmouseover="this.style.color='var(--color-primary)'"
                       onmouseout="this.style.color='var(--color-text-muted)'">Home</a></li>
                <li style="color: var(--color-border);">›</li>
                <li><a href="{{ route('cart.view') }}"
                       onmouseover="this.style.color='var(--color-primary)'"
                       onmouseout="this.style.color='var(--color-text-muted)'">Cart</a></li>
                <li style="color: var(--color-border);">›</li>
                <li style="color: var(--color-text-secondary); font-weight: 500;">Checkout</li>
            </ol>
        </nav>

        <form id="checkoutForm" action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 items-start">

                {{-- ══════════════════════════
                     LEFT: Form
                ══════════════════════════ --}}
                <div class="space-y-4">

                    {{-- 1. Customer Information --}}
                    <div class="card p-5">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="step-badge">1</div>
                            <h3 class="text-base font-semibold" style="color: var(--color-text);">Customer Information</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="co-label" for="co_name">
                                    Full Name <span style="color: var(--color-danger);">*</span>
                                </label>
                                <input id="co_name" name="customer_name" type="text"
                                       value="{{ auth()->user()?->name ?? '' }}"
                                       placeholder="Your full name"
                                       class="co-input @error('customer_name') is-error @enderror">
                                @error('customer_name')
                                    <p class="text-xs mt-1.5" style="color: var(--color-danger);">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="co-label" for="co_phone">
                                    Phone Number <span style="color: var(--color-danger);">*</span>
                                </label>
                                <input id="co_phone" name="customer_phone" type="tel"
                                       value="{{ auth()->user()?->phone ?? '' }}"
                                       placeholder="01XXXXXXXXX"
                                       class="co-input @error('customer_phone') is-error @enderror">
                                @error('customer_phone')
                                    <p class="text-xs mt-1.5" style="color: var(--color-danger);">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="co-label" for="co_email">
                                    Email <span class="font-normal normal-case" style="color: var(--color-text-placeholder);">(optional)</span>
                                </label>
                                <input id="co_email" name="customer_email" type="email"
                                       value="{{ auth()->user()?->email ?? '' }}"
                                       placeholder="you@example.com"
                                       class="co-input @error('customer_email') is-error @enderror">
                                @error('customer_email')
                                    <p class="text-xs mt-1.5" style="color: var(--color-danger);">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="co-label" for="co_city">
                                    City <span style="color: var(--color-danger);">*</span>
                                </label>
                                <input id="co_city" name="city" type="text"
                                       placeholder="Dhaka"
                                       class="co-input @error('city') is-error @enderror">
                                @error('city')
                                    <p class="text-xs mt-1.5" style="color: var(--color-danger);">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="co-label" for="co_address">
                                    Delivery Address <span style="color: var(--color-danger);">*</span>
                                </label>
                                <textarea id="co_address" name="address_line" rows="2"
                                          placeholder="House no., road, area…"
                                          class="co-input @error('address_line') is-error @enderror"></textarea>
                                @error('address_line')
                                    <p class="text-xs mt-1.5" style="color: var(--color-danger);">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="co-label" for="co_notes">
                                    Order Notes <span class="font-normal normal-case" style="color: var(--color-text-placeholder);">(optional)</span>
                                </label>
                                <textarea id="co_notes" name="notes" rows="2"
                                          placeholder="Any special instructions…"
                                          class="co-input @error('notes') is-error @enderror"></textarea>
                                @error('notes')
                                    <p class="text-xs mt-1.5" style="color: var(--color-danger);">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 2. Delivery Zone --}}
                    <div id="zonesModule" class="card p-5">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="step-badge">2</div>
                            <h3 class="text-base font-semibold" style="color: var(--color-text);">Delivery Zone</h3>
                        </div>

                        <div id="zonesLoader" class="space-y-3 animate-pulse">
                            @foreach (range(1, 3) as $i)
                                <div class="h-16 rounded-xl skel"></div>
                            @endforeach
                        </div>

                        <div id="shippingZones"
                             class="flex flex-col md:flex-row flex-1 items-center justify-between gap-4">
                            {{-- Rendered by CheckoutManager --}}
                        </div>
                    </div>

                    {{-- 3. Payment Method --}}
                    <div class="card p-5">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="step-badge">3</div>
                            <h3 class="text-base font-semibold" style="color: var(--color-text);">Payment Method</h3>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            {{-- Cash on Delivery --}}
                            <label class="payment-label">
                                <input type="radio" name="payment_method" value="cod" checked
                                       class="w-4 h-4 shrink-0" style="accent-color: var(--color-primary);">
                                <div class="payment-icon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                         style="color: var(--color-primary);">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold" style="color: var(--color-text);">Cash on Delivery</p>
                                    <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">Pay when order arrives</p>
                                </div>
                            </label>

                            {{-- Online Payment --}}
                            <label class="payment-label" style="opacity: 0.65; cursor: not-allowed;">
                                <input type="radio" name="payment_method" value="sslcommerz" disabled
                                       class="w-4 h-4 shrink-0" style="accent-color: var(--color-primary);">
                                <div class="payment-icon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                         style="color: var(--color-text-muted);">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold" style="color: var(--color-text);">
                                        Online Payment
                                        <span class="text-[10px] font-medium ml-1 px-1.5 py-0.5 rounded-full"
                                              style="background: var(--color-bg-soft); color: var(--color-primary); border: 1px solid var(--color-border);">
                                            Coming soon
                                        </span>
                                    </p>
                                    <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">bKash, Nagad, Cards</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- 4. Promo Code --}}
                    <div class="card p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="step-badge">4</div>
                            <h3 class="text-base font-semibold" style="color: var(--color-text);">Promo Code</h3>
                        </div>

                        <div class="coupon-bar">
                            <svg class="w-4 h-4 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color: var(--color-primary);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <input id="co_coupon" type="text" placeholder="Enter promo code"
                                   autocomplete="off"
                                   class="flex-1 bg-transparent text-sm px-3 py-2.5 focus:outline-none uppercase tracking-wider"
                                   style="color: var(--color-text-secondary);">
                            <button id="co_couponBtn" type="button"
                                    class="px-4 py-2.5 text-sm font-semibold shrink-0 cursor-pointer transition-all duration-200"
                                    style="background: var(--color-primary); color: white;"
                                    onmouseover="this.style.background='var(--color-primary-hover)'"
                                    onmouseout="this.style.background='var(--color-primary)'">
                                Apply
                            </button>
                        </div>
                        <p id="co_couponFeedback" class="hidden text-xs mt-2 font-medium"></p>
                    </div>

                </div>

                {{-- ══════════════════════════
                     RIGHT: Order Summary (sticky)
                ══════════════════════════ --}}
                <div class="sticky top-6 space-y-4">
                    <div class="card p-6">

                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold" style="color: var(--color-text);">Order Summary</h3>
                            <span id="coItemCount" class="text-xs font-medium" style="color: var(--color-text-muted);"></span>
                        </div>

                        {{-- Items list (JS-rendered) --}}
                        <div id="coItemsList" class="mb-4">
                            <div class="space-y-3 animate-pulse">
                                @foreach (range(1, 2) as $i)
                                    <div class="flex gap-3 py-3" style="border-bottom: 1px solid var(--color-border);">
                                        <div class="w-12 h-12 rounded-xl shrink-0 skel"></div>
                                        <div class="flex-1 space-y-1.5">
                                            <div class="h-3.5 rounded-lg w-3/4 skel"></div>
                                            <div class="h-3 rounded-lg w-1/2 skel"></div>
                                        </div>
                                        <div class="h-3.5 w-12 rounded skel shrink-0"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Totals --}}
                        <div class="space-y-2.5 pt-3" style="border-top: 1px solid var(--color-border);">
                            <div class="flex justify-between text-sm">
                                <span style="color: var(--color-text-muted);">Subtotal</span>
                                <span id="coSubtotal" class="font-semibold font-bengali"
                                      style="color: var(--color-text-secondary);">৳0</span>
                            </div>

                            <div id="coDiscountRow" class="hidden text-sm">
                                <span style="color: var(--color-text-muted);">Total Discount</span>
                                <span id="coDiscount" class="font-semibold font-bengali"
                                      style="color: var(--color-primary);">−৳0</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span style="color: var(--color-text-muted);">Shipping</span>
                                <span id="coShipping" class="font-semibold font-bengali"
                                      style="color: var(--color-text-secondary);">Select zone</span>
                            </div>

                            <div class="flex justify-between items-center pt-2"
                                 style="border-top: 1px solid var(--color-border);">
                                <span class="font-bold" style="color: var(--color-text);">Total</span>
                                <span id="coTotal" class="text-2xl font-bold font-bengali"
                                      style="color: var(--color-primary);">—</span>
                            </div>
                        </div>

                        {{-- Place Order --}}
                        <button id="placeOrderBtn" type="button"
                                class="btn-primary btn-shimmer w-full py-3 mt-5 font-bold text-sm cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed active:scale-[.98] flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span id="placeOrderLabel">Place Order</span>
                        </button>

                        {{-- Trust --}}
                        <div class="flex justify-center gap-5 mt-4 pt-4"
                             style="border-top: 1px solid var(--color-border);">
                            <div class="flex items-center gap-1.5 text-xs" style="color: var(--color-text-muted);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Secure Checkout
                            </div>
                            <div class="flex items-center gap-1.5 text-xs" style="color: var(--color-text-muted);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                </svg>
                                Fast Delivery
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('cart.view') }}"
                       class="flex items-center justify-center gap-2 text-sm font-semibold transition-colors duration-200"
                       style="color: var(--color-primary);"
                       onmouseover="this.style.color='var(--color-primary-hover)'"
                       onmouseout="this.style.color='var(--color-primary)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Cart
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

