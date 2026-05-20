@extends('layouts.app')

@section('title', $landing->meta_title ?? $landing->title)
@section('meta_description', $landing->meta_description ?? 'Special offers on premium products')

@section('content')
    <section class="bg-ivory min-h-screen">

        {{-- Hero Section --}}
        <div class="relative bg-linear-to-br from-brand via-brown to-gold-antique text-white overflow-hidden">
            <div class="max-w-8xl mx-auto px-4 py-12 md:py-20 text-center">
                @if ($landing->hero_image)
                    <img src="{{ asset('storage/' . $landing->hero_image) }}" alt="{{ $landing->title }}"
                        class="max-w-md w-full mx-auto mb-8 rounded-2xl shadow-2xl">
                @endif
                <h1 class="text-3xl md:text-5xl font-bold mb-4">{{ $landing->title }}</h1>
                @if ($landing->content)
                    <p class="text-cream text-lg max-w-2xl mx-auto">{{ Str::limit(strip_tags($landing->content), 200) }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="max-w-6xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach ($salesItems as $item)
                    @php
                        $isVariant = $item->product_variant_id !== null;
                        $label = $isVariant
                            ? ($item->variant->product->name ?? '') .
                                ' - ' .
                                ($item->variant->name ?? $item->variant->sku)
                            : $item->combo->name ?? 'Combo';
                        $price = $isVariant ? $item->variant->price : $item->combo->price ?? 0;
                        $image = $isVariant ? $item->variant->product->thumbnail ?? null : $item->combo->image ?? null;
                        $itemKey = $isVariant ? 'v_' . $item->product_variant_id : 'c_' . $item->combo_id;
                        $tierPrices = $isVariant ? $item->variant->tierPrices ?? collect() : collect();
                    @endphp

                    <div class="sales-item-card bg-white rounded-2xl shadow-sm border border-champagne overflow-hidden transition-all hover:shadow-md"
                        data-sales-key="{{ $itemKey }}"
                        data-sales-variant-id="{{ $isVariant ? $item->product_variant_id : '' }}"
                        data-sales-combo-id="{{ !$isVariant ? $item->combo_id : '' }}"
                        data-sales-label="{{ e($label) }}">

                        {{-- Image --}}
                        @if ($image)
                            <div class="aspect-square bg-cream flex items-center justify-center p-4">
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $label }}"
                                    class="max-h-full max-w-full object-contain">
                            </div>
                        @endif

                        <div class="p-5">
                            {{-- Title & Price --}}
                            <h3 class="font-bold text-brown mb-1">{{ $label }}</h3>
                            <p class="text-lg font-bold text-gold-antique font-bengali mb-1">
                                &#2547;{{ number_format($price, 0) }}
                            </p>

                            {{-- Tier price hints --}}
                            @if ($tierPrices->isNotEmpty())
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach ($tierPrices->sortBy('min_qty') as $tier)
                                        <span
                                            class="text-[10px] bg-ivory text-gold-antique border border-sand rounded-full px-2 py-0.5 font-semibold">
                                            {{ $tier->min_qty }}+&nbsp;&rarr;&nbsp;&#2547;{{ number_format($tier->price, 0) }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <div class="mb-3"></div>
                            @endif

                            {{-- Selection + Quantity --}}
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="sales-item-check w-5 h-5 rounded accent-green-700">
                                    <span class="text-sm font-semibold text-muted">Select</span>
                                </label>

                                <div class="sales-qty-controls hidden items-center gap-2">
                                    <button type="button" data-sales-qty-delta="-1"
                                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-muted font-bold transition-all">
                                        &minus;
                                    </button>
                                    <span class="sales-qty w-8 text-center font-bold text-brown">1</span>
                                    <button type="button" data-sales-qty-delta="1"
                                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-muted font-bold transition-all">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Selected Items Summary --}}
            <div id="salesSelectedSummary"
                class="hidden bg-white rounded-2xl shadow-sm border border-champagne p-5 mb-8">
                <h3 class="font-bold text-brown mb-3">
                    Selected Items (<span id="salesSelectedCount">0</span>)
                </h3>
                <div id="salesSelectedList" class="space-y-2"></div>
            </div>
        </div>

        {{-- Checkout Section --}}
        <div class="max-w-2xl mx-auto px-4 pb-16">
            @include('landing.partials._checkout')
        </div>
    </section>

    <script>
        var initialItems = @json(
            $salesItems->filter(fn($i) => $i->is_preselected)->map(function ($item) {
                    $data = ['quantity' => 1];
                    if ($item->product_variant_id) {
                        $data['variant_id'] = $item->product_variant_id;
                    }
                    if ($item->combo_id) {
                        $data['combo_id'] = $item->combo_id;
                    }
                    return $data;
                })->values());

        (() => {
            const preselected = @json(
                $salesItems->filter(fn($i) => $i->is_preselected)->map(fn($item) => [
                    'key' => $item->product_variant_id ? 'v_' . $item->product_variant_id : 'c_' . $item->combo_id,
                ])->pluck('key')->values()
            );
            const selected = {};

            function cardData(card) {
                return {
                    key: card.dataset.salesKey,
                    variant_id: card.dataset.salesVariantId ? Number(card.dataset.salesVariantId) : null,
                    combo_id: card.dataset.salesComboId ? Number(card.dataset.salesComboId) : null,
                    label: card.dataset.salesLabel || card.dataset.salesKey,
                };
            }

            function render() {
                document.querySelectorAll('.sales-item-card').forEach((card) => {
                    const key = card.dataset.salesKey;
                    const active = Boolean(selected[key]);
                    card.classList.toggle('ring-2', active);
                    card.classList.toggle('ring-primary', active);
                    card.querySelector('.sales-item-check').checked = active;
                    card.querySelector('.sales-qty-controls').classList.toggle('hidden', !active);
                    card.querySelector('.sales-qty-controls').classList.toggle('flex', active);
                    card.querySelector('.sales-qty').textContent = selected[key]?.quantity || 1;
                });

                const entries = Object.values(selected);
                document.getElementById('salesSelectedSummary').classList.toggle('hidden', entries.length === 0);
                document.getElementById('salesSelectedCount').textContent = entries.length;
                document.getElementById('salesSelectedList').innerHTML = entries.map((item) =>
                    `<div class="flex justify-between items-center text-sm text-muted py-1 border-b border-gray-50"><span>${item.label}</span><span class="font-semibold">x${item.quantity}</span></div>`
                ).join('');
            }

            function syncItems() {
                const items = Object.values(selected).map((item) => {
                    const payload = { quantity: item.quantity };
                    if (item.variant_id) payload.variant_id = item.variant_id;
                    if (item.combo_id) payload.combo_id = item.combo_id;
                    return payload;
                });
                window.initialItems = items;
                window.LandingCheckout?.updateItems(items);
                render();
            }

            document.querySelectorAll('.sales-item-card').forEach((card) => {
                const data = cardData(card);
                if (preselected.includes(data.key)) {
                    selected[data.key] = { ...data, quantity: 1 };
                }

                card.querySelector('.sales-item-check').addEventListener('change', (event) => {
                    if (event.target.checked) {
                        selected[data.key] = { ...data, quantity: 1 };
                    } else {
                        delete selected[data.key];
                    }
                    syncItems();
                });

                card.querySelectorAll('[data-sales-qty-delta]').forEach((button) => {
                    button.addEventListener('click', () => {
                        if (!selected[data.key]) return;
                        const nextQty = selected[data.key].quantity + Number(button.dataset.salesQtyDelta);
                        if (nextQty < 1) {
                            delete selected[data.key];
                        } else {
                            selected[data.key].quantity = nextQty;
                        }
                        syncItems();
                    });
                });
            });

            syncItems();
        })();
    </script>
@endsection
