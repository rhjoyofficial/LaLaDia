@extends('layouts.app')

@php
    $variants = $product->variants->map->toFrontend()->values();
    $initialVariant = $variants->first();
    $gallery = collect($product->gallery ?? [])
        ->filter()
        ->values();
    $mainImage = $product->image_url;
    $certifications = $product->certifications;

    $gaProductItem = [
        'item_id' => $product->sku ?? (string) ($initialVariant['id'] ?? ''),
        'item_name' => $product->name,
        'item_category' => $product->category?->name,
        'price' => (float) ($initialVariant['final_price'] ?? 0),
    ];
@endphp

@section('title', $product->name)
@section('meta_description', Str::limit(strip_tags($product->short_description ?? $product->name), 155))
@section('meta_image', $product->image_url ?? asset('favicon.png'))

@push('head')
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@push('styles')
    <style>
        /* ── Variant button active/inactive states ── */
        .variant-btn {
            border: 2px solid var(--color-border);
            background: var(--color-surface);
            color: var(--color-text-muted);
        }
        .variant-btn:hover { border-color: var(--color-primary); color: var(--color-primary); }
        .variant-btn.active { border-color: var(--color-primary); color: var(--color-primary); background: var(--color-bg-soft); }
        .tab-btn { border-bottom: 2px solid transparent; color: var(--color-text-muted); }
        .tab-btn.active { border-color: var(--color-primary); color: var(--color-primary); font-weight: 600; }
        .thumbBtn.active { border-color: var(--color-primary) !important; }
        .qty-btn {
            width: 2.25rem; height: 2.25rem;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 1.1rem; font-weight: 700;
            border-right: 1px solid var(--color-border);
            transition: background 0.15s;
        }
        .qty-btn:last-of-type { border-right: none; border-left: 1px solid var(--color-border); }
        .qty-btn:hover { background: var(--color-bg-soft); color: var(--color-text-secondary); }
        .btn-dark { background: var(--color-text); color: white; border-radius: 12px; transition: all 0.3s ease; }
        .btn-dark:hover { opacity: 0.88; }
        .nutrition-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--color-border); }
        .nutrition-row:last-child { border-bottom: none; }
    </style>
@endpush

@section('content')

    {{-- ══ BREADCRUMB ══ --}}
    <nav class="max-w-8xl mx-auto px-4 pt-4 pb-0" style="color: var(--color-text-muted); font-size: 0.75rem;">
        <ol class="flex items-center gap-1.5">
            <li><a href="{{ route('home') }}" style="color: var(--color-text-muted);"
                    onmouseover="this.style.color='var(--color-primary)'"
                    onmouseout="this.style.color='var(--color-text-muted)'">Home</a></li>
            <li style="color: var(--color-border);">›</li>
            <li><a href="{{ route('product.index') }}" style="color: var(--color-text-muted);"
                    onmouseover="this.style.color='var(--color-primary)'"
                    onmouseout="this.style.color='var(--color-text-muted)'">Shop</a></li>
            @if ($product->category)
                <li style="color: var(--color-border);">›</li>
                <li><a href="{{ route('product.index', ['category' => $product->category->slug]) }}"
                        style="color: var(--color-text-muted);" onmouseover="this.style.color='var(--color-primary)'"
                        onmouseout="this.style.color='var(--color-text-muted)'">{{ $product->category->name }}</a></li>
            @endif
            <li style="color: var(--color-border);">›</li>
            <li
                style="color: var(--color-text-secondary); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                {{ $product->name }}
            </li>
        </ol>
    </nav>

    {{-- ══ MAIN PRODUCT SECTION ══ --}}
    <section style="background: var(--color-bg);" class="pb-32 md:pb-16">
        <div class="max-w-8xl mx-auto px-4 py-6 md:py-10">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[2fr_3fr] gap-6 md:gap-10 lg:gap-x-16">

                {{-- ─── LEFT: IMAGE GALLERY ─── --}}
                <div class="space-y-4">
                    {{-- Main image --}}
                    <div class="aspect-square rounded-2xl overflow-hidden shadow-sm"
                        style="background: var(--color-surface); border: 1px solid var(--color-border);">
                        <img id="productMainImage" src="{{ $mainImage }}" alt="{{ $product->name }}"
                            class="w-full h-full object-contain p-4 transition-opacity duration-300">
                    </div>

                    {{-- Thumbnails --}}
                    @if ($gallery->isNotEmpty())
                        <div class="grid grid-cols-5 gap-2">
                            <button type="button"
                                class="thumbBtn active aspect-square rounded-xl overflow-hidden p-1 border-2 transition-all duration-200"
                                style="background: var(--color-surface); border-color: var(--color-primary);"
                                data-src="{{ $mainImage }}">
                                <img src="{{ $mainImage }}" alt="thumbnail" loading="lazy"
                                    class="w-full h-full object-cover rounded-lg">
                            </button>
                            @foreach ($gallery as $image)
                                <button type="button"
                                    class="thumbBtn aspect-square rounded-xl overflow-hidden p-1 border transition-all duration-200"
                                    style="background: var(--color-surface); border-color: var(--color-border);"
                                    onmouseover="this.style.borderColor='var(--color-primary)'"
                                    onmouseout="if(!this.classList.contains('active')) this.style.borderColor='var(--color-border)'"
                                    data-src="{{ asset('storage/' . $image) }}">
                                    <img src="{{ asset('storage/' . $image) }}" alt="thumbnail" loading="lazy"
                                        class="w-full h-full object-cover rounded-lg">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ─── RIGHT: PRODUCT INFO ─── --}}
                <div id="productDetailBox" data-variants='@json($variants)' class="space-y-5">

                    {{-- Category + Stars --}}
                    <div class="flex items-center gap-4 flex-wrap">
                        @if ($product->category)
                            <span
                                class="luxury-fire-bg px-3 py-1 rounded text-white text-xs font-semibold uppercase tracking-wide">
                                {{ $product->category->name }}
                            </span>
                        @endif
                        <div class="flex items-center gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 star-glow" fill="currentColor" viewBox="0 0 20 20"
                                    style="color: {{ $i <= ($product->rating ?? 5) ? 'var(--color-accent)' : 'var(--color-border)' }};">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                            @if (isset($product->reviews_count))
                                <span class="text-xs ml-1"
                                    style="color: var(--color-text-muted);">({{ $product->reviews_count }})</span>
                            @endif
                        </div>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-lg md:text-2xl font-semibold leading-tight" style="color: var(--color-text);">
                        {{ $product->name }}
                    </h1>

                    {{-- Short description --}}
                    @if ($product->short_description)
                        <p class="text-sm md:text-base leading-relaxed line-clamp-2"
                            style="color: var(--color-text-muted);">
                            {{ $product->short_description }}
                        </p>
                    @endif

                    {{-- Price row --}}
                    <div class="flex items-center gap-3 flex-wrap">
                        <span id="variantFinalPrice" class="text-2xl font-bold font-bengali"
                            style="color: var(--color-primary); font-weight: 700;">
                            ৳{{ number_format($initialVariant['final_price'] ?? 0, 2) }}
                        </span>
                        <span id="variantOriginalPrice"
                            class="text-base line-through font-bengali {{ !($initialVariant['discount_percent'] ?? null) ? 'hidden' : '' }}"
                            style="color: var(--color-text-muted);">
                            ৳{{ number_format($initialVariant['price'] ?? 0, 2) }}
                        </span>
                        <span id="variantDiscountBadge"
                            class="text-xs px-2 py-0.5 rounded font-semibold {{ !($initialVariant['discount_percent'] ?? null) ? 'hidden' : '' }}"
                            style="background: rgba(239,68,68,0.1); color: var(--color-danger);">
                            -{{ $initialVariant['discount_percent'] ?? 0 }}%
                        </span>
                    </div>

                    {{-- Tier pricing chips --}}
                    <div id="tierBox"></div>

                    {{-- Live tier incentive nudge (updated reactively on qty change) --}}
                    <div id="tierNudge" class="min-h-[1.75rem]"></div>

                    {{-- Variant selector --}}
                    @if ($product->variants->count() > 1)
                        <div>
                            <p class="text-sm font-semibold mb-3" style="color: var(--color-text-secondary);">Select Size /
                                Weight:</p>
                            <div class="flex flex-wrap gap-2" id="variantCapsuleContainer">
                                @foreach ($product->variants as $variant)
                                    @php
                                        $variantData = $variants->firstWhere('id', $variant->id);
                                        $hasDiscount = !empty($variantData['discount_percent']);
                                    @endphp
                                    <div class="relative">
                                        <button type="button" data-variant-id="{{ $variant->id }}"
                                            class="variant-btn {{ $loop->first ? 'active' : '' }} px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer select-none">
                                            {{ $variant->title }}
                                        </button>
                                        @if ($hasDiscount)
                                            <span
                                                class="absolute -top-2 -right-2 text-[10px] font-bold px-1.5 py-0.5 rounded-lg shadow-sm"
                                                style="background: var(--color-danger); color: white;">
                                                -{{ $variantData['discount_percent'] }}%
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="variantSelect" name="variant_id"
                                value="{{ $product->variants->first()?->id }}">
                        </div>
                    @else
                        <input type="hidden" id="variantSelect" name="variant_id"
                            value="{{ $product->variants->first()?->id }}">
                    @endif

                    {{-- Key values --}}
                    <div class="flex items-center gap-3 text-sm font-semibold flex-wrap"
                        style="color: var(--color-primary);">
                        <span>100% Natural</span>
                        <span style="color: var(--color-border);">•</span>
                        <span>No Preservatives</span>
                        <span style="color: var(--color-border);">•</span>
                        <span>Lab Tested</span>
                    </div>

                    {{-- Certifications --}}
                    @if ($certifications->isNotEmpty())
                        <div class="flex flex-wrap items-center gap-4 py-1">
                            @foreach ($certifications as $cert)
                                @if ($cert->logo_path)
                                    <img src="{{ asset($cert->logo_url) }}" alt="{{ $cert->name }}"
                                        class="h-12 w-12 object-contain">
                                @else
                                    <span class="text-xs font-medium"
                                        style="color: var(--color-text-secondary);">{{ $cert->name }}</span>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Stock + Qty --}}
                    <div class="flex flex-wrap items-center gap-6">
                        <div class="text-sm">
                            <span style="color: var(--color-text-muted);">Stock:</span>
                            <span id="stockText" class="ml-1 font-semibold" style="color: var(--color-text);">
                                {{ $initialVariant['available_stock'] ?? 0 }} units
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm" style="color: var(--color-text-muted);">Qty:</span>
                            <div class="flex items-center rounded-xl overflow-hidden"
                                style="border: 1px solid var(--color-border); background: var(--color-surface);">
                                <button id="qtyMinus" type="button" class="qty-btn"
                                    style="color: var(--color-text-muted);">−</button>
                                <div id="qtyInput"
                                    class="w-10 h-9 flex items-center justify-center text-sm font-bold select-none"
                                    style="color: var(--color-text-secondary);">1</div>
                                <button id="qtyPlus" type="button" class="qty-btn"
                                    style="color: var(--color-text-muted);">+</button>
                            </div>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-1">
                        <button id="addToCartBtn" type="button"
                            class="addToCartBtn btn-primary btn-shimmer animate-soft-pulse flex-1 py-3 font-bold text-sm flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:pointer-events-none"
                            data-variant="{{ $initialVariant['id'] ?? '' }}" data-ga-item='@json($gaProductItem)'>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Add to Cart
                        </button>
                        <button id="buyNowBtn" type="button"
                            class="btn-dark flex-1 py-3 font-bold text-sm cursor-pointer disabled:opacity-50 disabled:pointer-events-none">
                            Buy Now
                        </button>
                    </div>

                    {{-- Trust elements --}}
                    <div class="grid grid-cols-3 gap-3 pt-2" style="border-top: 1px solid var(--color-border);">
                        <div class="flex flex-col items-center gap-1 text-center py-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color: var(--color-primary);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span class="text-xs font-medium" style="color: var(--color-text-muted);">Secure
                                Checkout</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 text-center py-2"
                            style="border-left: 1px solid var(--color-border); border-right: 1px solid var(--color-border);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color: var(--color-primary);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            <span class="text-xs font-medium" style="color: var(--color-text-muted);">Fast Delivery</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 text-center py-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color: var(--color-primary);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <span class="text-xs font-medium" style="color: var(--color-text-muted);">Quality
                                Assured</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══ DESCRIPTION / NUTRITION TABS ══ --}}
            @if ($product->description || $product->nutritional_info)
                <div class="mt-12 rounded-2xl shadow-sm"
                    style="background: var(--color-surface); border: 1px solid var(--color-border);">

                    {{-- Tab nav --}}
                    <div style="border-bottom: 1px solid var(--color-border);" class="px-6 md:px-8">
                        <nav class="flex gap-6">
                            <button type="button" data-tab-target="description"
                                class="tab-btn active py-4 text-sm transition-all duration-200 cursor-pointer">
                                Description
                            </button>
                            <button type="button" data-tab-target="nutrition"
                                class="tab-btn py-4 text-sm transition-all duration-200 cursor-pointer">
                                Nutritional Info
                            </button>
                        </nav>
                    </div>

                    {{-- Description --}}
                    <div id="description" class="tab-content block p-6 md:p-8 animate-fadeIn">
                        <div class="prose prose-sm max-w-none text-sm md:text-base leading-relaxed"
                            style="color: var(--color-text-muted);">
                            {!! clean($product->description) !!}
                        </div>
                    </div>

                    {{-- Nutrition --}}
                    @if (!empty($product->nutritional_info))
                        <div id="nutrition" class="tab-content hidden p-6 md:p-8 animate-fadeIn">
                            <h3 class="text-sm font-semibold mb-4" style="color: var(--color-text);">
                                Nutritional Facts (per {{ $product->nutritional_info['Serving Size'] ?? '100g' }})
                            </h3>
                            <div class="max-w-md">
                                @foreach ($product->nutritional_info as $label => $value)
                                    @if ($label !== 'Serving Size' && !empty($value))
                                        <div class="nutrition-row">
                                            <span class="text-sm"
                                                style="color: var(--color-text-muted);">{{ $label }}</span>
                                            <span class="text-sm font-bold"
                                                style="color: var(--color-primary);">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <p class="text-xs mt-4 italic" style="color: var(--color-text-placeholder);">
                                * Percent Daily Values are based on a 2,000 calorie diet.
                            </p>
                        </div>
                    @else
                        <div id="nutrition" class="tab-content hidden p-6 md:p-8 animate-fadeIn">
                            <div class="max-w-lg py-8 text-center text-sm rounded-xl"
                                style="background: var(--color-bg-soft); color: var(--color-text-muted); border: 1px solid var(--color-border);">
                                Nutritional information is currently unavailable for this product.
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ══ RELATED PRODUCTS ══ --}}
            @if ($relatedProducts->isNotEmpty())
                <div class="mt-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl md:text-2xl font-semibold flex items-center gap-2"
                            style="color: var(--color-text);">
                            <span class="w-1 h-6 rounded-full" style="background: var(--color-primary);"></span>
                            You May Also Like
                        </h2>
                        <div class="flex items-center gap-2">
                            <button id="relatedPrev"
                                class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200 cursor-pointer"
                                style="border: 1px solid var(--color-border); background: var(--color-surface); color: var(--color-text-muted);"
                                onmouseover="this.style.background='var(--color-bg-soft)'"
                                onmouseout="this.style.background='var(--color-surface)'">‹</button>
                            <button id="relatedNext"
                                class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200 cursor-pointer"
                                style="border: 1px solid var(--color-border); background: var(--color-surface); color: var(--color-text-muted);"
                                onmouseover="this.style.background='var(--color-bg-soft)'"
                                onmouseout="this.style.background='var(--color-surface)'">›</button>
                        </div>
                    </div>

                    <div id="relatedCarousel"
                        class="grid grid-flow-col auto-cols-[82%] sm:auto-cols-[44%] lg:auto-cols-[30%] gap-4 overflow-x-auto snap-x snap-mandatory pb-4 no-scrollbar scroll-smooth">
                        @foreach ($relatedProducts as $related)
                            <div class="snap-start">
                                <x-ui.product-card :product="$related" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>

    {{-- ══ MOBILE STICKY CTA ══ --}}
    <div class="fixed left-0 right-0 z-40 md:hidden flex items-center px-4 gap-3"
        style="bottom: 64px; height: 70px; background: var(--color-surface); border-top: 1px solid var(--color-border); box-shadow: 0 -4px 16px rgba(0,0,0,0.06);">
        <div class="flex-1">
            <p class="text-xs" style="color: var(--color-text-muted);">Price</p>
            <p id="mobileStickyPrice" class="text-base font-bold font-bengali leading-tight"
                style="color: var(--color-primary);">
                ৳{{ number_format($initialVariant['final_price'] ?? 0) }}
            </p>
        </div>
        <button id="mobileStickyCartBtn" type="button"
            class="btn-primary btn-shimmer px-5 py-2.5 text-sm font-bold cursor-pointer active:scale-95 flex items-center gap-2"
            data-variant="{{ $initialVariant['id'] ?? '' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Add to Cart
        </button>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                /* ─── Variant data ─── */
                const detailBox = document.getElementById('productDetailBox');
                const variants = JSON.parse(detailBox?.dataset.variants || '[]');
                if (!variants.length) return;

                const variantBtns = document.querySelectorAll('.variant-btn');
                const variantSelect = document.getElementById('variantSelect');
                const finalPrice = document.getElementById('variantFinalPrice');
                const originalPrice = document.getElementById('variantOriginalPrice');
                const discountBadge = document.getElementById('variantDiscountBadge');
                const stockText = document.getElementById('stockText');
                const tierBox = document.getElementById('tierBox');
                const qtyDisplay = document.getElementById('qtyInput');
                const addToCartBtn = document.getElementById('addToCartBtn');
                const buyNowBtn = document.getElementById('buyNowBtn');
                const qtyMinus = document.getElementById('qtyMinus');
                const qtyPlus = document.getElementById('qtyPlus');
                const mobileStickyPrice = document.getElementById('mobileStickyPrice');
                const mobileStickyCartBtn = document.getElementById('mobileStickyCartBtn');
                const tierNudge = document.getElementById('tierNudge');

                /* ─── Thumbnail gallery ─── */
                const mainImg = document.getElementById('productMainImage');
                const thumbBtns = document.querySelectorAll('.thumbBtn');
                thumbBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        thumbBtns.forEach(b => {
                            b.classList.remove('active');
                            b.style.borderColor = 'var(--color-border)';
                        });
                        btn.classList.add('active');
                        btn.style.borderColor = 'var(--color-primary)';
                        mainImg.src = btn.dataset.src;
                    });
                });

                /* ─── Helpers ─── */
                function activeVariant() {
                    return variants.find(v => String(v.id) === String(variantSelect.value)) || variants[0];
                }

                function updateQtyBoundaries() {
                    const v = activeVariant();
                    const max = Math.max(1, Number(v.available_stock || 1));
                    let q = Number(qtyDisplay.textContent.trim() || 1);
                    if (isNaN(q) || q < 1) q = 1;
                    if (q > max) q = max;
                    qtyDisplay.textContent = q;
                }

                function renderTierInfo(v) {
                    if (!v.tiers?.length) {
                        tierBox.innerHTML = '';
                        return;
                    }
                    const sortedTiers = [...v.tiers].sort((a, b) => a.qty - b.qty);
                    tierBox.innerHTML = `
                <div class="flex flex-wrap gap-2">
                    ${sortedTiers.map(t => {
                        const discount = t.type === 'percentage' ? `${t.value}% off` : `৳${t.value} off/unit`;
                        const perks = [];
                        if (t.free_delivery) perks.push('<span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-sky-600 bg-sky-50 border border-sky-200 px-1.5 py-0.5 rounded-full">🚚 Free Delivery</span>');
                        if (t.gift_variant_id) perks.push('<span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-violet-600 bg-violet-50 border border-violet-200 px-1.5 py-0.5 rounded-full">🎁 Free Gift</span>');
                        return `<span class="inline-flex flex-wrap items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold font-bengali bg-primary/5 text-primary border border-primary/20">
                            Buy ${t.qty}+ ${t.value > 0 ? `&rarr; Save ${discount}` : ''}
                            ${perks.join('')}
                        </span>`;
                    }).join('')}
                </div>`;
                }

                function updateLivePricing() {
                    const v = activeVariant();
                    const qty = Number(qtyDisplay.textContent.trim() || 1);
                    if (!v.tiers?.length) {
                        if (tierNudge) tierNudge.innerHTML = '';
                        return;
                    }
                    const sortedTiers = [...v.tiers].sort((a, b) => a.qty - b.qty);
                    const activeTier = sortedTiers.filter(t => t.qty <= qty).pop() || null;
                    const nextTier = sortedTiers.find(t => t.qty > qty) || null;
                    let livePrice = parseFloat(v.final_price);
                    if (activeTier) {
                        const base = parseFloat(v.price);
                        livePrice = activeTier.type === 'percentage' ? base * (1 - activeTier.value / 100) : Math.max(0, base - activeTier.value);
                    }
                    finalPrice.textContent = `৳${livePrice.toFixed(2)}`;
                    if (mobileStickyPrice) mobileStickyPrice.textContent = `৳${livePrice.toFixed(2)}`;
                    let nudgeHtml = '';
                    if (activeTier) {
                        const saving = ((parseFloat(v.price) - livePrice) * qty).toFixed(2);
                        nudgeHtml = `<div class="mt-2 text-xs font-bold text-emerald-600 bg-emerald-50 p-2 rounded">Bulk deal active — saved ৳${saving} total</div>`;
                    } else if (nextTier) {
                        const need = nextTier.qty - qty;
                        nudgeHtml = `<div class="mt-2 text-xs text-amber-600 bg-amber-50 p-2 rounded">Add ${need} more to unlock better pricing!</div>`;
                    }
                    if (tierNudge) tierNudge.innerHTML = nudgeHtml;
                }

                function renderVariant() {
                    const v = activeVariant();

                    finalPrice.textContent = `৳${Number(v.final_price).toFixed(2)}`;
                    originalPrice.textContent = `৳${Number(v.price).toFixed(2)}`;
                    stockText.textContent = `${v.available_stock} units`;
                    addToCartBtn.dataset.variant = v.id;
                    if (mobileStickyCartBtn) mobileStickyCartBtn.dataset.variant = v.id;

                    // Keep GA4 item data in sync with selected variant
                    const _gaItem = JSON.stringify({
                        item_id: '{{ addslashes($product->sku ?? '') }}' || String(v.id),
                        item_name: '{{ addslashes($product->name) }}',
                        item_category: '{{ addslashes($product->category?->name ?? '') }}' || null,
                        price: parseFloat(v.final_price ?? 0),
                    });
                    addToCartBtn.dataset.gaItem = _gaItem;
                    if (mobileStickyCartBtn) mobileStickyCartBtn.dataset.gaItem = _gaItem;
                    if (mobileStickyPrice) mobileStickyPrice.textContent =
                        `৳${Number(v.final_price).toLocaleString('en-BD')}`;

                    if (v.discount_percent) {
                        originalPrice.classList.remove('hidden');
                        discountBadge.classList.remove('hidden');
                        discountBadge.textContent = `-${v.discount_percent}%`;
                    } else {
                        originalPrice.classList.add('hidden');
                        discountBadge.classList.add('hidden');
                    }

                    const outOfStock = Number(v.available_stock) <= 0;
                    [addToCartBtn, buyNowBtn, mobileStickyCartBtn].forEach(btn => {
                        if (!btn) return;
                        btn.disabled = outOfStock;
                        btn.classList.toggle('opacity-50', outOfStock);
                        btn.classList.toggle('pointer-events-none', outOfStock);
                    });

                    renderTierInfo(v);
                    updateQtyBoundaries();
                    updateLivePricing(); // recalculate for the reset qty after variant change

                    /* Update variant button styles */
                    variantBtns.forEach(btn => {
                        const isActive = btn.dataset.variantId === String(v.id);
                        btn.classList.toggle('active', isActive);
                    });
                }

                /**
                 * Recalculates the displayed price and nudge message based on
                 * the currently selected variant + current qty.
                 * Mirrors the backend PricingService logic on the frontend.
                 */
                function updateLivePricing() {
                    const v = activeVariant();
                    const qty = Number(qtyDisplay.textContent.trim() || 1);
                    const tierNudgeEl = document.getElementById('tierNudge');

                    if (!v.tiers?.length) {
                        if (tierNudgeEl) tierNudgeEl.innerHTML = '';
                        return;
                    }

                    // Sort ascending — find best qualifying tier
                    const sortedTiers = [...v.tiers].sort((a, b) => a.qty - b.qty);
                    const activeTier = sortedTiers.filter(t => t.qty <= qty).pop() || null;
                    const nextTier   = sortedTiers.find(t => t.qty > qty) || null;

                    // Calculate live price
                    let livePrice = parseFloat(v.final_price);
                    let liveOriginal = parseFloat(v.price);

                    if (activeTier) {
                        const base = parseFloat(v.price); // always discount off base price
                        if (activeTier.type === 'percentage') {
                            livePrice = base * (1 - activeTier.value / 100);
                        } else {
                            livePrice = Math.max(0, base - activeTier.value);
                        }
                    }

                    // Update displayed unit price
                    finalPrice.textContent = `৳${livePrice.toFixed(2)}`;
                    if (mobileStickyPrice) mobileStickyPrice.textContent = `৳${livePrice.toFixed(2)}`;

                    // Show line total hint
                    const lineTotal = (livePrice * qty).toFixed(2);

                    // Build nudge message
                    let nudgeHtml = '';
                    if (activeTier) {
                        const perks = [];
                        if (activeTier.free_delivery) perks.push('🚚 Free Delivery');
                        if (activeTier.gift_variant_id) perks.push('🎁 Free Gift included');
                        const saving = ((parseFloat(v.price) - livePrice) * qty).toFixed(2);
                        nudgeHtml = `
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-full font-bengali">
                                    <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Bulk deal active — saving ৳${saving} total
                                </span>
                                ${perks.map(p => `<span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 font-bengali">${p}</span>`).join('')}
                            </div>`;
                    } else if (nextTier) {
                        const need = nextTier.qty - qty;
                        const reward = nextTier.type === 'percentage' ? `${nextTier.value}% off` : `৳${nextTier.value} off/unit`;
                        const perks = [];
                        if (nextTier.free_delivery) perks.push('🚚 Free Delivery');
                        if (nextTier.gift_variant_id) perks.push('🎁 Free Gift');
                        const perkStr = perks.length ? ` + ${perks.join(' & ')}` : '';
                        nudgeHtml = `
                            <span class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold px-3 py-1.5 rounded-full font-bengali">
                                <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                Add ${need} more to unlock ${reward}${perkStr}
                            </span>`;
                    }

                    if (tierNudgeEl) tierNudgeEl.innerHTML = nudgeHtml;
                }

                /* ─── Variant button clicks ─── */
                variantBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        variantSelect.value = btn.dataset.variantId;
                        renderVariant();
                    });
                });

                /* ─── Qty controls ─── */
                qtyMinus?.addEventListener('click', () => {
                    let qty = Number(qtyDisplay.textContent.trim() || 1);
                    qtyDisplay.textContent = Math.max(1, qty - 1);
                    updateLivePricing();
                });
                qtyPlus?.addEventListener('click', () => {
                    const v = activeVariant();
                    let qty = Number(qtyDisplay.textContent.trim() || 1);
                    qtyDisplay.textContent = Math.min(Number(v.available_stock || 999), qty + 1);
                    updateLivePricing();
                });
                qtyDisplay?.addEventListener('blur', updateQtyBoundaries);
                variantSelect?.addEventListener('change', renderVariant);

                /* ─── Cart actions ─── */
                async function doAddToCart(btn) {
                    const variantId = btn.dataset.variant;
                    const qty = Math.max(1, Number(qtyDisplay.textContent.trim() || 1));
                    await window.Cart?.add(variantId, qty, btn);
                    // GA4 add_to_cart is fired by CartManager after the API call succeeds,
                    // using the current data-ga-item kept in sync by renderVariant().
                }

                addToCartBtn?.addEventListener('click', () => doAddToCart(addToCartBtn));
                mobileStickyCartBtn?.addEventListener('click', () => doAddToCart(mobileStickyCartBtn));

                buyNowBtn?.addEventListener('click', () => {
                    const v = activeVariant();
                    const qty = Math.max(1, Number(qtyDisplay.textContent.trim() || 1));
                    sessionStorage.setItem('bionic_buy_now', JSON.stringify({
                        variant_id: v.id,
                        quantity: qty,
                        product_name_snapshot: '{{ $product->name }}',
                        variant_title_snapshot: v.title,
                        unit_price: v.final_price,
                        image_url: mainImg?.src ?? '',
                    }));
                    window.location.href = '/checkout?buyNow=1';
                });

                /* ─── Related carousel buttons ─── */
                const relatedCarousel = document.getElementById('relatedCarousel');
                document.getElementById('relatedPrev')?.addEventListener('click', () => {
                    relatedCarousel?.scrollBy({
                        left: -280,
                        behavior: 'smooth'
                    });
                });
                document.getElementById('relatedNext')?.addEventListener('click', () => {
                    relatedCarousel?.scrollBy({
                        left: 280,
                        behavior: 'smooth'
                    });
                });

                /* ─── Tab switching ─── */
                const tabBtns = document.querySelectorAll('.tab-btn');
                const tabContents = document.querySelectorAll('.tab-content');
                tabBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const target = btn.dataset.tabTarget;
                        tabBtns.forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        tabContents.forEach(c => {
                            c.classList.add('hidden');
                            c.classList.remove('block');
                        });
                        const el = document.getElementById(target);
                        if (el) {
                            el.classList.remove('hidden');
                            el.classList.add('block');
                        }
                    });
                });

                /* ─── Init ─── */
                renderVariant();
                // Populate nudge on initial load
                updateLivePricing();
            });
        </script>
    @endpush

@endsection
