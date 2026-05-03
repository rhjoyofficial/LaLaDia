@props(['product'])
@php
    $variants        = $product->variants;
    $frontendVariants = $variants->map->toFrontend();
    $first           = $variants->first();
    $hasDiscount     = (bool) $first?->discount_percent;
    $isNew           = $product->created_at?->diffInDays() <= 14;
    $inStock         = ($first?->available_stock ?? 0) > 0;
@endphp

<div class="card overflow-hidden flex flex-col h-full transition-all duration-300 hover:shadow-lg hover:scale-[1.02]"
     data-variants='@json($frontendVariants)'>

    {{-- ── IMAGE ── --}}
    <div class="relative aspect-square overflow-hidden" style="background: var(--color-bg-soft);">
        <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                 loading="lazy"
                 class="aspect-square object-cover w-full h-full transition-transform duration-500 hover:scale-105">
        </a>

        {{-- SALE / NEW BADGE (top-left) --}}
        @if ($hasDiscount)
            <span class="discountBadge badge-primary absolute top-2 left-2 text-[10px] font-bold px-2 py-0.5 rounded-md tracking-wide">
                -{{ $first->discount_percent }}%
            </span>
        @elseif ($isNew)
            <span class="absolute top-2 left-2 text-[10px] font-bold px-2 py-0.5 rounded-md tracking-wide"
                  style="background: var(--color-primary); color: white;">
                New
            </span>
        @endif

        {{-- TIER PREVIEW (top-right) --}}
        <div class="tierPreview absolute top-2 right-2 flex flex-col gap-1 pointer-events-none">
            @if ($first?->tierPrices?->count())
                @foreach ($first->tierPrices->take(1) as $tier)
                    <div class="text-[9px] font-bold px-1.5 py-0.5 rounded-md leading-tight"
                         style="background: var(--color-surface); color: var(--color-primary); border: 1px solid var(--color-border);">
                        {{ $tier->min_quantity }}+
                        Save {{ $tier->discount_type === 'percentage' ? $tier->discount_value . '%' : '৳' . $tier->discount_value }}
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- ── BODY ── --}}
    <div class="flex flex-col grow p-3 md:p-4">

        {{-- TITLE --}}
        <a href="{{ route('product.show', $product->slug) }}"
           class="text-xs md:text-sm font-medium line-clamp-2 leading-snug mb-2 transition-colors duration-200"
           style="color: var(--color-text);"
           onmouseover="this.style.color='var(--color-primary)'"
           onmouseout="this.style.color='var(--color-text)'">
            {{ $product->name }}
        </a>

        {{-- VARIANT SELECTOR (multi-variant only) --}}
        @if ($variants->count() > 1)
            <div class="relative mb-2">
                <svg class="absolute inset-0 w-full h-full pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                    <rect rx="12" ry="12" class="animated-border-line" height="100%" width="100%"
                          fill="transparent" stroke-linejoin="round" />
                </svg>
                <select class="variantSelect relative z-10 w-full appearance-none rounded-xl px-3 py-2 text-sm font-medium outline-none transition-all cursor-pointer"
                        style="background: var(--color-bg-soft); border: 1px solid var(--color-border); color: var(--color-text);">
                    @foreach ($variants as $v)
                        <option value="{{ $v->id }}" {{ $loop->first ? 'selected' : '' }}>
                            {{ $v->title }} — {{ format_currency($v->final_price) }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none z-20"
                     style="color: var(--color-primary);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        @endif

        {{-- PRICE --}}
        <div class="priceBox flex items-center gap-2 mt-auto mb-3">
            <span class="finalPrice text-sm md:text-base font-semibold"
                  style="color: var(--color-primary); font-weight: 600;">
                ৳{{ number_format($first?->final_price) }}
            </span>
            <span class="oldPrice text-xs line-through {{ $hasDiscount ? '' : 'hidden' }}"
                  style="color: var(--color-text-muted);">
                ৳{{ number_format($first?->price) }}
            </span>
        </div>

        {{-- ADD TO CART --}}
        <button class="addToCartBtn {{ $inStock ? '' : 'hidden' }} btn-primary w-full py-2 text-sm font-semibold cursor-pointer active:scale-95 flex items-center justify-center gap-1.5"
                data-variant="{{ $first?->id }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Add to Cart
        </button>

        {{-- CONTACT (out of stock) --}}
        <button class="contactBtn {{ $inStock ? 'hidden' : '' }} w-full py-2 text-sm font-semibold rounded-xl transition-all"
                style="background: var(--color-border); color: var(--color-text-secondary);">
            Contact Us
        </button>

    </div>
</div>

