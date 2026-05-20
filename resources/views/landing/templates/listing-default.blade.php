@extends('layouts.app')

@section('title', $landing->meta_title ?? $landing->title)
@section('meta_description', $landing->meta_description ?? 'Browse and shop our curated selection')

@section('content')
    <section class="bg-ivory min-h-screen">

        {{-- Hero Section --}}
        <div class="relative bg-linear-to-br from-brand via-brown to-gold-antique text-white overflow-hidden">
            <div class="max-w-8xl mx-auto px-4 py-12 md:py-16 text-center">
                @if ($landing->hero_image)
                    <img src="{{ asset('storage/' . $landing->hero_image) }}" alt="{{ $landing->title }}"
                        class="max-w-xs w-full mx-auto mb-8 rounded-2xl shadow-2xl">
                @endif
                <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ $landing->title }}</h1>
                @if ($landing->content)
                    <p class="text-cream text-base max-w-2xl mx-auto">
                        {{ Str::limit(strip_tags($landing->content), 200) }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Items Grid --}}
        <div class="max-w-6xl mx-auto px-4 py-12">

            @if ($listingItems->isEmpty())
                <div class="text-center py-16 text-taupe">
                    <svg class="w-12 h-12 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-sm">No items have been added to this listing yet.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($listingItems as $item)
                        @php
                            $isVariant = $item->product_variant_id !== null;
                            $name = $isVariant
                                ? ($item->variant->product->name ?? '') .
                                    ($item->variant->name ? ' — ' . $item->variant->name : '')
                                : $item->combo->name ?? 'Combo';
                            $price = $isVariant ? $item->variant->price ?? 0 : $item->combo->price ?? 0;
                            $image = $isVariant
                                ? $item->variant->product->thumbnail ?? null
                                : $item->combo->image ?? null;
                            $tierPrices = $isVariant ? $item->variant->tierPrices ?? collect() : collect();
                            $itemId = $isVariant ? 'v' . $item->product_variant_id : 'c' . $item->combo_id;
                        @endphp

                        <div
                            data-listing-item
                            data-is-variant="{{ $isVariant ? '1' : '0' }}"
                            data-variant-id="{{ $item->product_variant_id }}"
                            data-combo-id="{{ $item->combo_id }}"
                            data-base-price="{{ (float) $price }}"
                            data-tier-prices='@json($tierPrices->map(fn($t) => ['min_qty' => $t->min_qty, 'price' => (float) $t->price])->values())'
                            data-item-name="{{ e($name) }}"
                            data-item-category="{{ e($isVariant ? $item->variant->product->category->name ?? '' : 'Combo') }}"
                            data-item-id="{{ e($isVariant ? (string) $item->product_variant_id : 'combo_' . $item->combo_id) }}"
                            class="bg-white rounded-2xl shadow-sm border border-champagne overflow-hidden flex flex-col hover:shadow-md transition-shadow">

                            {{-- Image --}}
                            @if ($image)
                                <div class="aspect-square bg-cream flex items-center justify-center p-4 shrink-0">
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $name }}"
                                        class="max-h-full max-w-full object-contain">
                                </div>
                            @else
                                <div class="aspect-square bg-cream flex items-center justify-center shrink-0">
                                    <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            <div class="p-4 flex flex-col flex-1">
                                {{-- Name --}}
                                <h3 class="font-semibold text-brown text-sm leading-snug mb-2">{{ $name }}</h3>

                                {{-- Price + Tier Badges --}}
                                <div class="mb-3">
                                    <div class="flex items-baseline gap-2">
                                        <span data-listing-price
                                            class="text-lg font-bold text-gold-antique font-bengali">৳{{ number_format($price, 0) }}</span>
                                        <span data-listing-compare
                                            class="hidden text-xs text-taupe line-through font-bengali">৳{{ number_format($price, 0) }}</span>
                                    </div>

                                    {{-- Tier price hints --}}
                                    @if ($tierPrices->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 mt-1.5">
                                            @foreach ($tierPrices->sortBy('min_qty') as $tier)
                                                <span
                                                    class="text-[10px] bg-ivory text-gold-antique border border-sand rounded-full px-2 py-0.5 font-semibold">
                                                    {{ $tier->min_qty }}+&nbsp;&rarr;&nbsp;&#2547;{{ number_format($tier->price, 0) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-auto space-y-2">
                                    {{-- Quantity stepper --}}
                                    <div class="flex items-center gap-2">
                                        <button data-listing-qty-delta="-1" type="button"
                                            class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-muted font-bold text-sm transition-all">
                                            &minus;
                                        </button>
                                        <span data-listing-qty class="w-8 text-center font-bold text-brown text-sm">1</span>
                                        <button data-listing-qty-delta="1" type="button"
                                            class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-muted font-bold text-sm transition-all">
                                            +
                                        </button>
                                        <span data-listing-line-total
                                            class="hidden text-xs text-taupe ml-1 font-bengali"></span>
                                    </div>

                                    {{-- Add to Cart button --}}
                                    <button data-listing-add type="button"
                                        class="w-full bg-gold-antique text-white py-2 rounded-xl font-semibold text-sm hover:bg-brand transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer">
                                        <svg data-listing-spinner class="hidden animate-spin h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        <svg data-listing-cart-icon class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span data-listing-button-text>Add to Cart</span>
                                    </button>

                                    {{-- Added flash --}}
                                    <p data-listing-added class="hidden text-center text-xs text-primary font-semibold">
                                        ✓ Added to cart!
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- CMS Content (below grid) --}}
            @if ($landing->content && $listingItems->isNotEmpty())
                <div class="mt-12 max-w-4xl mx-auto">
                    <div class="prose prose-green max-w-none bg-white rounded-2xl shadow-sm border border-champagne p-8">
                        {!! clean($landing->content) !!}
                    </div>
                </div>
            @endif
        </div>
    </section>

    <script>
        (() => {
            document.querySelectorAll('[data-listing-item]').forEach((card) => {
                const state = {
                    isVariant: card.dataset.isVariant === '1',
                    variantId: card.dataset.variantId ? Number(card.dataset.variantId) : null,
                    comboId: card.dataset.comboId ? Number(card.dataset.comboId) : null,
                    basePrice: Number(card.dataset.basePrice || 0),
                    tierPrices: JSON.parse(card.dataset.tierPrices || '[]'),
                    itemName: card.dataset.itemName || '',
                    itemCategory: card.dataset.itemCategory || null,
                    itemId: card.dataset.itemId || '',
                    quantity: 1,
                    adding: false,
                };

                function effectivePrice() {
                    const sorted = [...state.tierPrices].sort((a, b) => Number(b.min_qty) - Number(a.min_qty));
                    return Number(sorted.find((tier) => state.quantity >= Number(tier.min_qty))?.price ?? state.basePrice);
                }

                function render() {
                    const price = effectivePrice();
                    card.querySelector('[data-listing-price]').textContent = '৳' + price.toFixed(0);
                    card.querySelector('[data-listing-qty]').textContent = state.quantity;

                    const compare = card.querySelector('[data-listing-compare]');
                    compare.classList.toggle('hidden', !(state.quantity > 1 && price < state.basePrice));

                    const lineTotal = card.querySelector('[data-listing-line-total]');
                    lineTotal.classList.toggle('hidden', state.quantity <= 1);
                    lineTotal.textContent = '৳' + (price * state.quantity).toFixed(0) + ' total';

                    const btn = card.querySelector('[data-listing-add]');
                    btn.disabled = state.adding;
                    card.querySelector('[data-listing-spinner]').classList.toggle('hidden', !state.adding);
                    card.querySelector('[data-listing-cart-icon]').classList.toggle('hidden', state.adding);
                    card.querySelector('[data-listing-button-text]').textContent = state.adding ? 'Adding...' : 'Add to Cart';
                }

                card.querySelectorAll('[data-listing-qty-delta]').forEach((button) => {
                    button.addEventListener('click', () => {
                        state.quantity = Math.max(1, state.quantity + Number(button.dataset.listingQtyDelta));
                        render();
                    });
                });

                card.querySelector('[data-listing-add]').addEventListener('click', async (event) => {
                    if (state.adding || !window.Cart) return;
                    state.adding = true;
                    render();

                    try {
                        if (state.isVariant) {
                            await window.Cart.add(state.variantId, state.quantity, event.currentTarget);
                        } else {
                            await window.Cart.addCombo(state.comboId, state.quantity, event.currentTarget);
                        }

                        window.Analytics?.addToCart({
                            item_id: state.itemId,
                            item_name: state.itemName,
                            item_category: state.itemCategory,
                            price: effectivePrice(),
                        }, state.quantity);

                        const added = card.querySelector('[data-listing-added]');
                        added.classList.remove('hidden');
                        setTimeout(() => added.classList.add('hidden'), 2500);
                    } finally {
                        state.adding = false;
                        render();
                    }
                });

                render();
            });
        })();
    </script>
@endsection
