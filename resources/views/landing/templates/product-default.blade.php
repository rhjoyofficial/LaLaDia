@extends('layouts.app')

@section('title', $landing->meta_title ?? $product->name)
@section('meta_description', $landing->meta_description ?? $product->short_description)

@section('content')
    <section class="bg-ivory min-h-screen">

        {{-- Hero Section --}}
        <div class="relative bg-linear-to-br from-brand via-brown to-gold-antique text-white overflow-hidden">
            <div class="max-w-8xl mx-auto px-4 py-12 md:py-20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                    {{-- Product Image --}}
                    <div class="flex justify-center">
                        @if ($landing->hero_image)
                            <img src="{{ asset('storage/' . $landing->hero_image) }}" alt="{{ $product->name }}"
                                class="max-w-sm w-full rounded-2xl shadow-2xl">
                        @elseif($product->thumbnail)
                            <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}"
                                class="max-w-sm w-full rounded-2xl shadow-2xl">
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold mb-4">
                            {{ $landing->title ?? $product->name }}
                        </h1>
                        @if ($product->short_description)
                            <p class="text-cream text-lg mb-6">{{ $product->short_description }}</p>
                        @endif

                        {{-- Variant Selector --}}
                        @if ($product->variants->count() > 1)
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-champagne mb-2">Select Variant</label>
                                <div class="flex flex-wrap gap-3">
                                    @foreach ($product->variants as $variant)
                                        <button type="button"
                                            data-product-variant-id="{{ $variant->id }}"
                                            data-product-variant-price="{{ (float) $variant->price }}"
                                            class="product-variant-btn px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ $loop->first ? 'bg-white text-gold-antique ring-2 ring-white' : 'bg-gold-antique/50 text-white hover:bg-primary/50' }}">
                                            {{ $variant->name ?? $variant->sku }}
                                            &mdash; <span
                                                class="font-bengali">&#2547;{{ number_format($variant->price, 0) }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Tier price hints for selected variant --}}
                        <div id="productTierHints" class="flex flex-wrap gap-1.5 mb-4"></div>

                        {{-- Quantity --}}
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-champagne mb-2">Quantity</label>
                            <div class="flex items-center gap-3">
                                <button id="productQtyMinus" type="button"
                                    class="w-10 h-10 rounded-full bg-gold-antique/50 hover:bg-primary/50 flex items-center justify-center text-white text-xl font-bold transition-all">
                                    &minus;
                                </button>
                                <span id="productQty" class="text-2xl font-bold w-12 text-center">1</span>
                                <button id="productQtyPlus" type="button"
                                    class="w-10 h-10 rounded-full bg-gold-antique/50 hover:bg-primary/50 flex items-center justify-center text-white text-xl font-bold transition-all">
                                    +
                                </button>
                            </div>
                        </div>

                        {{-- Price Display --}}
                        <div class="flex items-baseline gap-3 flex-wrap">
                            <span id="productTotalPrice" class="text-3xl font-bold font-bengali">৳0</span>
                            <span id="productComparePrice"
                                class="hidden text-gold-warm text-base line-through font-bengali"></span>
                            <span id="productUnitPriceText" class="hidden text-champagne text-sm"></span>
                        </div>

                        {{-- Scroll to checkout CTA --}}
                        <a href="#landingCheckout"
                            class="mt-6 inline-flex items-center gap-2 bg-white text-gold-antique px-8 py-3 rounded-full font-bold text-base hover:bg-ivory transition-all shadow-lg">
                            Order Now
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Section (from CMS) --}}
        @if ($landing->content)
            <div class="max-w-4xl mx-auto px-4 py-12">
                <div class="prose prose-green max-w-none bg-white rounded-2xl shadow-sm border border-champagne p-8">
                    {!! clean($landing->content) !!}
                </div>
            </div>
        @endif

        {{-- Checkout Section --}}
        <div class="max-w-2xl mx-auto px-4 py-12">
            @include('landing.partials._checkout')
        </div>
    </section>

    <script>
        var initialItems = [{
            variant_id: {{ $product->variants->first()?->id ?? 'null' }},
            quantity: 1
        }];

        (() => {
            const variants = @json(
                $product->variants->map(fn($variant) => [
                    'id' => $variant->id,
                    'price' => (float) $variant->price,
                    'tierPrices' => $variant->tierPrices->map(fn($tier) => [
                        'min_qty' => $tier->min_qty,
                        'price' => (float) $tier->price,
                    ])->sortBy('min_qty')->values(),
                ])->values()
            );
            let selectedVariantId = {{ $product->variants->first()?->id ?? 'null' }};
            let unitPrice = {{ (float) ($product->variants->first()?->price ?? 0) }};
            let tierPrices = variants[0]?.tierPrices || [];
            let quantity = 1;

            function effectivePrice() {
                const sorted = [...tierPrices].sort((a, b) => b.min_qty - a.min_qty);
                return sorted.find((tier) => quantity >= Number(tier.min_qty))?.price ?? unitPrice;
            }

            function render() {
                const price = Number(effectivePrice());
                document.getElementById('productQty').textContent = quantity;
                document.getElementById('productTotalPrice').textContent = '৳' + (price * quantity).toFixed(0);

                const compare = document.getElementById('productComparePrice');
                compare.classList.toggle('hidden', price >= unitPrice);
                compare.textContent = '৳' + (unitPrice * quantity).toFixed(0);

                const unitText = document.getElementById('productUnitPriceText');
                unitText.classList.toggle('hidden', quantity <= 1);
                unitText.textContent = `(${quantity} × ৳${price.toFixed(0)} each)`;

                const hints = document.getElementById('productTierHints');
                hints.innerHTML = tierPrices.map((tier) =>
                    `<span class="text-xs bg-white/20 text-white border border-white/30 rounded-full px-2.5 py-0.5 font-semibold">${tier.min_qty}+ → ৳${Number(tier.price).toFixed(0)}</span>`
                ).join('');
            }

            function syncItems() {
                if (!selectedVariantId) return;
                const items = [{ variant_id: selectedVariantId, quantity }];
                window.initialItems = items;
                window.LandingCheckout?.updateItems(items);
                render();
            }

            document.querySelectorAll('.product-variant-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    selectedVariantId = Number(button.dataset.productVariantId);
                    unitPrice = Number(button.dataset.productVariantPrice);
                    tierPrices = variants.find((variant) => Number(variant.id) === selectedVariantId)?.tierPrices || [];
                    document.querySelectorAll('.product-variant-btn').forEach((btn) => {
                        btn.classList.remove('bg-white', 'text-gold-antique', 'ring-2', 'ring-white');
                        btn.classList.add('bg-gold-antique/50', 'text-white', 'hover:bg-primary/50');
                    });
                    button.classList.remove('bg-gold-antique/50', 'text-white', 'hover:bg-primary/50');
                    button.classList.add('bg-white', 'text-gold-antique', 'ring-2', 'ring-white');
                    syncItems();
                });
            });

            document.getElementById('productQtyMinus')?.addEventListener('click', () => {
                quantity = Math.max(1, quantity - 1);
                syncItems();
            });
            document.getElementById('productQtyPlus')?.addEventListener('click', () => {
                quantity += 1;
                syncItems();
            });

            syncItems();
        })();
    </script>
@endsection
