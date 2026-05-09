@extends('layouts.app')

@php
    $finalPrice = $combo->final_price;
    $autoPrice  = $combo->auto_price;
    $savings    = $combo->total_savings;
    $inStock    = $combo->isInStock();
    $isLowStock = $combo->isLowStock();
    $maxQty     = max(1, $combo->available_stock);

    $comboData = [
        'id'          => $combo->id,
        'title'       => $combo->title,
        'final_price' => (float) $finalPrice,
        'image_url'   => $combo->image_url ?? asset('images/placeholder.png'),
        'stock'       => $combo->available_stock,
    ];
@endphp

@section('title', $combo->title)
@section('meta_description', Str::limit(strip_tags($combo->description ?? 'Exclusive combo pack'), 155))

@push('styles')
<style>
    .tab-btn         { border-bottom: 2px solid transparent; }
    .tab-btn.active  { border-bottom-color: var(--color-primary); color: var(--color-primary); }
    .combo-item-chip { display: flex; align-items: center; gap: 0.75rem; border-radius: 0.75rem; padding: 0.75rem; border: 1px solid var(--color-border); background: var(--color-surface); }
    .qty-wrap        { display: flex; align-items: center; border: 1px solid var(--color-border); border-radius: 0.625rem; overflow: hidden; background: var(--color-surface); }
    .qty-wrap button { width: 2.25rem; height: 2.25rem; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: background 0.15s; }
    .qty-wrap button:hover { background: var(--color-bg-soft); }
    .qty-wrap span   { width: 2.5rem; text-align: center; font-size: 0.875rem; font-weight: 700; }
    .btn-dark        { background: var(--color-text); color: var(--color-surface); border-radius: 0.5rem; font-weight: 700; transition: opacity 0.2s, transform 0.15s; }
    .btn-dark:hover  { opacity: 0.88; transform: translateY(-2px); }
    .btn-dark:active { transform: scale(0.97); }
</style>
@endpush

@section('content')
    <section class="min-h-screen" style="background: var(--color-bg);">
        <div class="max-w-7xl mx-auto px-4 py-6 md:py-10">

            {{-- Breadcrumb --}}
            <x-page-header :breadcrumbs="[
                ['label' => 'Home',   'url' => route('shop')],
                ['label' => 'Combos', 'url' => route('combos.index')],
                ['label' => $combo->title, 'url' => null],
            ]" />

            {{-- ── Main Grid ──────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[2fr_3fr] gap-8 lg:gap-16">

                {{-- Left: Image --}}
                <div class="space-y-4">
                    <div class="aspect-square rounded-2xl overflow-hidden relative"
                         style="border: 1px solid var(--color-border); background: var(--color-surface);">
                        @if ($savings > 0)
                            <div class="absolute top-3 left-3 z-10">
                                <span class="badge-primary font-bengali text-xs font-bold px-3 py-1.5 rounded-full shadow">
                                    Save ৳{{ number_format($savings, 0) }}
                                </span>
                            </div>
                        @endif
                        <img id="comboMainImage"
                             src="{{ $combo->image_url ?? asset('images/placeholder.png') }}"
                             alt="{{ $combo->title }}"
                             class="w-full h-full object-contain p-4">
                    </div>

                    {{-- What's inside (mobile) --}}
                    <div class="md:hidden card p-4">
                        <p class="text-xs font-bold uppercase tracking-widest mb-3"
                           style="color: var(--color-text-muted);">What's inside</p>
                        @foreach ($combo->items as $item)
                            @php $v = $item->variant; $p = $v?->product; @endphp
                            <div class="flex items-center gap-3 py-2.5"
                                 style="border-bottom: 1px solid var(--color-border);">
                                <div class="w-8 h-8 rounded-lg overflow-hidden shrink-0"
                                     style="background: var(--color-bg-soft); border: 1px solid var(--color-border);">
                                    @if ($p?->image_url)
                                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-lg">📦</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold truncate" style="color: var(--color-text);">
                                        {{ $p?->name ?? 'Item' }}
                                    </p>
                                    <p class="text-[10px]" style="color: var(--color-text-muted);">
                                        {{ $v?->title }} &times; {{ $item->quantity }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Details --}}
                <div id="comboDetailBox" data-combo='@json($comboData)' class="space-y-5">

                    {{-- Badge + Stars --}}
                    <div class="flex items-center gap-4">
                        <span class="badge-primary text-sm font-medium px-3 py-1 rounded-lg">Combo Pack</span>
                        <div class="flex items-center gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"
                                     style="color: var(--color-primary);">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: var(--color-text);">
                        {{ $combo->title }}
                    </h1>

                    @if ($combo->description)
                        <p class="text-sm leading-relaxed line-clamp-2" style="color: var(--color-text-muted);">
                            {{ Str::limit(strip_tags($combo->description), 120) }}
                        </p>
                    @endif

                    {{-- Pricing --}}
                    <div class="flex items-baseline gap-3 flex-wrap">
                        <span id="comboFinalPrice" class="text-3xl font-bold font-bengali"
                              style="color: var(--color-primary);">
                            ৳{{ number_format($finalPrice, 0) }}
                        </span>
                        @if ($savings > 0)
                            <span class="text-base line-through font-bengali" style="color: var(--color-text-muted);">
                                ৳{{ number_format($autoPrice, 0) }}
                            </span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-lg font-bengali"
                                  style="background: rgba(239,68,68,0.1); color: var(--color-danger);">
                                Save ৳{{ number_format($savings, 0) }}
                            </span>
                        @endif
                    </div>

                    {{-- Key values --}}
                    <div class="flex items-center gap-3 text-sm font-semibold" style="color: var(--color-primary);">
                        <span>Curated Bundle</span>
                        <span style="color: var(--color-border);">•</span>
                        <span>Best Value</span>
                        <span style="color: var(--color-border);">•</span>
                        <span>Lab Tested</span>
                    </div>

                    {{-- What's inside (desktop) --}}
                    <div class="hidden md:block">
                        <p class="text-xs font-bold uppercase tracking-widest mb-3"
                           style="color: var(--color-text-muted);">What's inside</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($combo->items as $item)
                                @php $v = $item->variant; $p = $v?->product; @endphp
                                <div class="combo-item-chip">
                                    <div class="w-11 h-11 rounded-xl overflow-hidden shrink-0"
                                         style="background: var(--color-bg-soft); border: 1px solid var(--color-border);">
                                        @if ($p?->image_url)
                                            <img src="{{ $p->image_url }}" alt="{{ $p?->name }}"
                                                 class="w-full h-full object-cover" loading="lazy">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xl">📦</div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold truncate" style="color: var(--color-text);">
                                            {{ $p?->name ?? 'Product' }}
                                        </p>
                                        <p class="text-xs" style="color: var(--color-text-muted);">{{ $v?->title }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs font-bold font-bengali" style="color: var(--color-text);">
                                            × {{ $item->quantity }}
                                        </p>
                                        @if ($v)
                                            <p class="text-xs font-semibold font-bengali" style="color: var(--color-primary);">
                                                ৳{{ number_format($v->final_price * $item->quantity, 0) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm italic" style="color: var(--color-text-muted);">
                                    No items configured yet.
                                </p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Stock & Quantity --}}
                    <div class="flex flex-wrap items-center gap-6 py-2">
                        <div class="flex items-center gap-1.5 text-sm">
                            <span style="color: var(--color-text-muted);">Stock:</span>
                            <span id="comboStockText" class="font-medium"
                                  style="color: {{ $inStock ? ($isLowStock ? '#B45309' : 'var(--color-primary)') : 'var(--color-danger)' }};">
                                @if ($inStock)
                                    {{ $combo->available_stock }} sets
                                    @if ($isLowStock)
                                        — <span class="text-xs font-semibold">Low stock!</span>
                                    @endif
                                @else
                                    Out of Stock
                                @endif
                            </span>
                        </div>

                        @if ($inStock)
                            <div class="flex items-center gap-2 text-sm">
                                <span style="color: var(--color-text-muted);">Qty:</span>
                                <div class="qty-wrap">
                                    <button id="comboQtyMinus" type="button"
                                            style="color: var(--color-text-muted); border-right: 1px solid var(--color-border);">−</button>
                                    <span id="comboQtyDisplay" style="color: var(--color-text);">1</span>
                                    <button id="comboQtyPlus" type="button"
                                            style="color: var(--color-text-muted); border-left: 1px solid var(--color-border);">+</button>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        @if ($inStock)
                            <button id="comboAddToCartBtn" type="button"
                                    class="btn-primary btn-shimmer flex-1 py-4 font-bold flex items-center justify-center gap-2 cursor-pointer active:scale-95"
                                    data-ga-item='@json([
                                        "item_id"       => "combo_" . $combo->id,
                                        "item_name"     => $combo->name,
                                        "item_category" => "Combo",
                                        "price"         => (float) $combo->final_price,
                                    ])'>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Add to Cart
                            </button>
                            <button id="comboBuyNowBtn" type="button"
                                    class="btn-dark flex-1 py-4 cursor-pointer active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Buy Now
                            </button>
                        @else
                            <div class="flex-1 py-4 text-center rounded-xl font-bold text-sm"
                                 style="background: var(--color-bg-soft); border: 1px solid var(--color-border); color: var(--color-text-muted);">
                                Out of Stock
                            </div>
                        @endif
                    </div>

                    {{-- Trust strip --}}
                    <div class="flex items-center gap-4 pt-2">
                        <span class="flex items-center gap-1.5 text-xs" style="color: var(--color-text-muted);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            100% Authentic
                        </span>
                        <span style="color: var(--color-border);">·</span>
                        <span class="flex items-center gap-1.5 text-xs" style="color: var(--color-text-muted);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                            Fast Delivery
                        </span>
                        <span style="color: var(--color-border);">·</span>
                        <span class="flex items-center gap-1.5 text-xs" style="color: var(--color-text-muted);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Easy Returns
                        </span>
                    </div>

                </div>{{-- /comboDetailBox --}}
            </div>{{-- /main grid --}}

            {{-- Description --}}
            @if ($combo->description)
                <div class="mt-12 card p-6 md:p-8">
                    <div style="border-bottom: 1px solid var(--color-border);" class="mb-6">
                        <nav class="flex space-x-8">
                            <button type="button" data-tab-target="comboDescription"
                                    class="tab-btn active pb-4 text-sm font-semibold whitespace-nowrap transition-all duration-200 cursor-pointer"
                                    style="color: var(--color-primary);">
                                Description
                            </button>
                        </nav>
                    </div>
                    <div id="comboDescription" class="tab-content block animate-fadeIn">
                        <div class="text-sm md:text-base leading-relaxed max-w-4xl font-bengali"
                             style="color: var(--color-text-muted);">
                            {!! $combo->description !!}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Related Combos --}}
            @if ($relatedCombos->isNotEmpty())
                <div class="mt-14">
                    <div class="flex items-center justify-between gap-6 mb-8">
                        <h2 class="font-heading text-2xl md:text-3xl font-bold shrink-0"
                            style="color: var(--color-text);">More Combo Packs</h2>
                        <span class="h-px flex-1 hidden md:block" style="background: var(--color-border);"></span>
                        <div class="flex gap-2 shrink-0">
                            <button id="relatedComboPrev"
                                    class="w-9 h-9 rounded-xl flex items-center justify-center cursor-pointer transition-all duration-200"
                                    style="border: 1px solid var(--color-border); color: var(--color-text-muted);"
                                    onmouseover="this.style.background='var(--color-primary)'; this.style.color='white'; this.style.borderColor='var(--color-primary)'"
                                    onmouseout="this.style.background='transparent'; this.style.color='var(--color-text-muted)'; this.style.borderColor='var(--color-border)'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button id="relatedComboNext"
                                    class="w-9 h-9 rounded-xl flex items-center justify-center cursor-pointer transition-all duration-200"
                                    style="border: 1px solid var(--color-border); color: var(--color-text-muted);"
                                    onmouseover="this.style.background='var(--color-primary)'; this.style.color='white'; this.style.borderColor='var(--color-primary)'"
                                    onmouseout="this.style.background='transparent'; this.style.color='var(--color-text-muted)'; this.style.borderColor='var(--color-border)'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div id="relatedComboCarousel"
                         class="grid grid-flow-col auto-cols-[85%] sm:auto-cols-[45%] lg:auto-cols-[31%] gap-5
                                overflow-x-auto snap-x snap-mandatory pb-4 no-scrollbar scroll-smooth">
                        @foreach ($relatedCombos as $related)
                            <div class="snap-start">
                                <x-ui.combo-card :combo="$related" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const box     = document.getElementById('comboDetailBox');
            const combo   = JSON.parse(box?.dataset.combo || '{}');
            const maxQty  = Math.max(1, Number(combo.stock || 1));

            const qtyDisplay   = document.getElementById('comboQtyDisplay');
            const qtyMinus     = document.getElementById('comboQtyMinus');
            const qtyPlus      = document.getElementById('comboQtyPlus');
            const addToCartBtn = document.getElementById('comboAddToCartBtn');
            const buyNowBtn    = document.getElementById('comboBuyNowBtn');

            function getQty() {
                return Math.max(1, Math.min(maxQty, parseInt(qtyDisplay?.textContent?.trim() || '1', 10) || 1));
            }
            function setQty(val) {
                if (qtyDisplay) qtyDisplay.textContent = Math.max(1, Math.min(maxQty, val));
            }

            qtyMinus?.addEventListener('click', () => setQty(getQty() - 1));
            qtyPlus?.addEventListener('click',  () => setQty(getQty() + 1));

            addToCartBtn?.addEventListener('click', async () => {
                await window.Cart?.addCombo(combo.id, getQty(), addToCartBtn);
            });

            buyNowBtn?.addEventListener('click', () => {
                const imageEl = document.getElementById('comboMainImage');
                sessionStorage.setItem('bionic_buy_now', JSON.stringify({
                    combo_id:             combo.id,
                    quantity:             getQty(),
                    combo_name_snapshot:  combo.title,
                    unit_price:           combo.final_price,
                    image_url:            imageEl?.src ?? combo.image_url ?? '',
                }));
                window.location.href = '/checkout?buyNow=1';
            });

            const carousel = document.getElementById('relatedComboCarousel');
            document.getElementById('relatedComboPrev')?.addEventListener('click', () => {
                carousel?.scrollBy({ left: -carousel.offsetWidth * 0.85, behavior: 'smooth' });
            });
            document.getElementById('relatedComboNext')?.addEventListener('click', () => {
                carousel?.scrollBy({ left: carousel.offsetWidth * 0.85, behavior: 'smooth' });
            });
        });
    </script>
    @endpush
@endsection

