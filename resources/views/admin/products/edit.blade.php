@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')

<div x-data="productForm({{ $productId }})" x-init="init()">

    {{-- Page Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.products') }}" class="text-taupe hover:text-brown transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-lg font-bold text-brown">Edit Product</h2>
            <p class="text-sm text-muted mt-0.5" x-text="form.name ? 'Editing: ' + form.name : 'Loading…'"></p>
        </div>
    </div>

    {{-- Loading overlay --}}
    <div x-show="loading" class="flex items-center justify-center py-20">
        <div class="text-center text-taupe">
            <i class="fa-solid fa-spinner fa-spin text-3xl mb-3 block text-primary"></i>
            <p class="text-sm">Loading product data…</p>
        </div>
    </div>

    {{-- Global error banner --}}
    <div x-show="submitError" x-cloak class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        <i class="fa-solid fa-circle-exclamation mr-2"></i>
        <span x-text="submitError"></span>
    </div>

    <form @submit.prevent="submit()" x-show="!loading">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- ==============================
                 LEFT COLUMN (main content)
            ============================== --}}
            <div class="xl:col-span-2 space-y-6">

                {{-- Basic Info --}}
                <div class="bg-white border border-champagne rounded-xl p-6 space-y-4">
                    <h3 class="text-sm font-bold text-brown">Basic Information</h3>

                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.name"
                            class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                            :class="errors.name ? 'border-red-400' : 'border-champagne'">
                        <p x-show="errors.name" class="mt-1 text-xs text-red-600" x-text="errors.name?.[0]"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Slug</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-taupe shrink-0">/products/</span>
                            <input type="text" x-model="form.slug"
                                class="flex-1 border rounded-lg px-3 py-2 text-sm font-mono outline-none focus:ring-2 focus:ring-gold-antique"
                                :class="errors.slug ? 'border-red-400' : 'border-champagne'"
                                placeholder="product-slug">
                        </div>
                        <p class="mt-1 text-xs text-taupe">Changing this updates the product URL. Leave unchanged to keep the current slug.</p>
                        <p x-show="errors.slug" class="mt-1 text-xs text-red-600" x-text="errors.slug?.[0]"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Short Description</label>
                        <input type="text" x-model="form.short_description"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Product Note <span class="text-taupe text-xs font-normal">(shown on product page near the price)</span></label>
                        <textarea x-model="form.note" rows="2"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique resize-none"
                            placeholder="e.g. Seasonal product — available May to June only."></textarea>
                        <p x-show="errors.note" class="mt-1 text-xs text-red-600" x-text="errors.note?.[0]"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Full Description</label>
                        <textarea x-model="form.description" rows="6"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique resize-y"></textarea>
                    </div>
                </div>

                {{-- Variants --}}
                <div class="bg-white border border-champagne rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-brown">Variants</h3>
                        <button type="button" @click="addVariant()"
                            class="inline-flex items-center gap-1.5 text-sm text-gold-antique font-medium hover:text-brand transition cursor-pointer">
                            <i class="fa-solid fa-plus text-xs"></i> Add Variant
                        </button>
                    </div>

                    <p x-show="errors.variants" class="mb-3 text-xs text-red-600" x-text="errors.variants?.[0]"></p>

                    <div class="space-y-4">
                        <template x-for="(variant, index) in variants" :key="variant.id ?? 'new-' + index">
                            <div class="border border-champagne rounded-xl p-4 relative"
                                :class="!variant.is_active ? 'opacity-60 bg-cream' : ''">

                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-muted uppercase tracking-wider"
                                            x-text="'Variant ' + (index + 1) + (variant.title ? ' — ' + variant.title : '')"></span>
                                        <template x-if="variant.id">
                                            <span class="text-xs text-taupe font-mono" x-text="'#' + variant.id"></span>
                                        </template>
                                    </div>
                                    <button type="button" @click="removeVariant(index)"
                                        x-show="variants.length > 1"
                                        class="text-red-400 hover:text-red-600 transition cursor-pointer text-xs">
                                        <i class="fa-solid fa-xmark"></i> Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-muted mb-1">Title <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="variant.title"
                                            class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                                            :class="errors['variants.' + index + '.title'] ? 'border-red-400' : 'border-champagne'">
                                        <p x-show="errors['variants.' + index + '.title']" class="mt-1 text-xs text-red-600"
                                            x-text="errors['variants.' + index + '.title']?.[0]"></p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-muted mb-1">SKU <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="variant.sku"
                                            class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique font-mono"
                                            :class="errors['variants.' + index + '.sku'] ? 'border-red-400' : 'border-champagne'">
                                        <p x-show="errors['variants.' + index + '.sku']" class="mt-1 text-xs text-red-600"
                                            x-text="errors['variants.' + index + '.sku']?.[0]"></p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-muted mb-1">Price (৳) <span class="text-red-500">*</span></label>
                                        <input type="number" x-model.number="variant.price" min="0" step="0.01"
                                            class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                                            :class="errors['variants.' + index + '.price'] ? 'border-red-400' : 'border-champagne'">
                                        <p x-show="errors['variants.' + index + '.price']" class="mt-1 text-xs text-red-600"
                                            x-text="errors['variants.' + index + '.price']?.[0]"></p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-muted mb-1">Stock</label>
                                        <div class="flex gap-2 items-center">
                                            <input type="number" x-model.number="variant.stock" min="0"
                                                class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                                            <span class="text-xs text-taupe whitespace-nowrap"
                                                x-show="variant.reserved_stock > 0"
                                                x-text="variant.reserved_stock + ' reserved'"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-muted mb-1">Weight (grams)</label>
                                        <input type="number" x-model.number="variant.weight_grams" min="0"
                                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-muted mb-1">Discount Type</label>
                                        <select x-model="variant.discount_type"
                                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique cursor-pointer">
                                            <option value="">No Discount</option>
                                            <option value="percentage">Percentage (%)</option>
                                            <option value="fixed">Fixed Amount (৳)</option>
                                        </select>
                                    </div>
                                    <template x-if="variant.discount_type">
                                        <div>
                                            <label class="block text-xs font-medium text-muted mb-1"
                                                x-text="variant.discount_type === 'percentage' ? 'Discount %' : 'Discount Amount (৳)'"></label>
                                            <input type="number" x-model.number="variant.discount_value" min="0" step="0.01"
                                                class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                                        </div>
                                    </template>
                                    <template x-if="variant.discount_type">
                                        <div>
                                            <label class="block text-xs font-medium text-muted mb-1">Sale Ends At</label>
                                            <input type="datetime-local" x-model="variant.sale_ends_at"
                                                class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                                        </div>
                                    </template>
                                </div>

                                {{-- Variant note --}}
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-muted mb-1">Variant Note <span class="text-taupe">(shown on product page)</span></label>
                                    <textarea x-model="variant.note" rows="2"
                                        class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique resize-none"
                                        placeholder="e.g. Weight may vary slightly based on mango size."></textarea>
                                </div>

                                <div class="mt-3 pt-3 border-t border-champagne flex items-center gap-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <div class="relative">
                                            <input type="checkbox" x-model="variant.is_active" class="sr-only peer">
                                            <div class="w-8 h-5 bg-gray-200 peer-checked:bg-primary rounded-full transition"></div>
                                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-3"></div>
                                        </div>
                                        <span class="text-xs font-medium text-muted">Variant Active</span>
                                    </label>
                                </div>

                                <template x-if="variant.id">
                                    <div class="mt-4 pt-4 border-t border-champagne">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-xs font-bold text-brown uppercase tracking-wider">Tier Prices / Incentives</h4>
                                            <button type="button" @click="addTierPrice(index)"
                                                class="inline-flex items-center gap-1.5 text-xs text-gold-antique font-medium hover:text-brand transition cursor-pointer">
                                                <i class="fa-solid fa-plus text-[10px]"></i> Add Tier
                                            </button>
                                        </div>

                                        <div class="space-y-3">
                                            <template x-for="(tier, tIndex) in variant.tier_prices" :key="tIndex">
                                                <div class="bg-ivory border border-champagne rounded-lg p-3">
                                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                                        <div>
                                                            <label class="block text-[10px] font-medium text-muted mb-1">Min Qty <span class="text-red-500">*</span></label>
                                                            <input type="number" x-model.number="tier.min_quantity" min="1"
                                                                class="w-full border border-champagne rounded-md px-2 py-1.5 text-xs outline-none focus:ring-1 focus:ring-gold-antique">
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] font-medium text-muted mb-1">Discount Type</label>
                                                            <select x-model="tier.discount_type"
                                                                class="w-full border border-champagne rounded-md px-2 py-1.5 text-xs outline-none focus:ring-1 focus:ring-gold-antique cursor-pointer">
                                                                <option value="percentage">Percentage (%)</option>
                                                                <option value="fixed">Fixed Amount (৳)</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] font-medium text-muted mb-1">Discount Value <span class="text-red-500">*</span></label>
                                                            <input type="number" x-model.number="tier.discount_value" min="0" step="0.01"
                                                                class="w-full border border-champagne rounded-md px-2 py-1.5 text-xs outline-none focus:ring-1 focus:ring-gold-antique">
                                                        </div>
                                                        {{-- Gift Variant Searcher --}}
                                                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                                            <label class="block text-[10px] font-medium text-muted mb-1">Gift Variant</label>
                                                            <input
                                                                type="text"
                                                                :value="tier.gift_label || ''"
                                                                @input="tier.gift_label = $event.target.value; searchGiftVariant(index, tIndex, $event.target.value); open = true"
                                                                @focus="open = true"
                                                                placeholder="Search by name or SKU…"
                                                                class="w-full border border-champagne rounded-md px-2 py-1.5 text-xs outline-none focus:ring-1 focus:ring-gold-antique"
                                                                autocomplete="off">
                                                            <button type="button" x-show="tier.gift_product_variant_id" @click="tier.gift_product_variant_id = null; tier.gift_label = ''; open = false" class="absolute right-2 top-6 text-muted hover:text-red-500 cursor-pointer">
                                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                                            </button>
                                                            <div x-show="open && tier.giftResults && tier.giftResults.length > 0" x-cloak
                                                                class="absolute z-50 left-0 right-0 mt-0.5 bg-white border border-champagne rounded-md shadow-lg max-h-40 overflow-y-auto">
                                                                <template x-for="result in (tier.giftResults || [])" :key="result.variant_id">
                                                                    <button type="button"
                                                                        @click="tier.gift_product_variant_id = result.variant_id; tier.gift_label = result.product_name + ' — ' + result.variant_title + ' (#' + result.variant_id + ')'; open = false"
                                                                        class="w-full text-left px-2 py-1.5 hover:bg-ivory text-[10px] border-b border-champagne last:border-0 cursor-pointer">
                                                                        <span class="font-medium text-brown" x-text="result.product_name"></span>
                                                                        <span class="text-muted" x-text="' — ' + result.variant_title"></span>
                                                                        <span class="text-taupe font-mono ml-1" x-text="'#' + result.variant_id"></span>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                            <p x-show="tier.gift_product_variant_id" class="mt-0.5 text-[9px] text-green-600 font-mono" x-text="'ID: ' + tier.gift_product_variant_id"></p>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] font-medium text-muted mb-1">Gift Qty</label>
                                                            <input type="number" x-model.number="tier.gift_quantity" min="1"
                                                                class="w-full border border-champagne rounded-md px-2 py-1.5 text-xs outline-none focus:ring-1 focus:ring-gold-antique">
                                                        </div>
                                                        <div class="sm:col-span-2">
                                                            <div class="flex items-center mt-4 mb-1">
                                                                <label class="flex items-center gap-2 cursor-pointer">
                                                                    <input type="checkbox" x-model="tier.has_free_delivery" class="rounded border-champagne text-gold-antique focus:ring-gold-antique w-4 h-4">
                                                                    <span class="text-xs font-medium text-muted">Free Delivery Override</span>
                                                                </label>
                                                            </div>
                                                            {{-- Zone multi-selector (visible only when free delivery is checked) --}}
                                                            <div x-show="tier.has_free_delivery" x-cloak class="mt-1 border border-champagne rounded-md p-2 bg-ivory">
                                                                <p class="text-[9px] text-muted mb-1">Limit to zones (leave empty = all zones)</p>
                                                                <div class="grid grid-cols-2 gap-1 max-h-24 overflow-y-auto">
                                                                    <template x-for="zone in shippingZones" :key="zone.id">
                                                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                                                            <input type="checkbox"
                                                                                :value="zone.id"
                                                                                :checked="(tier.free_delivery_zones || []).includes(zone.id)"
                                                                                @change="toggleZone(index, tIndex, zone.id, $event.target.checked)"
                                                                                class="rounded border-champagne text-gold-antique focus:ring-gold-antique w-3 h-3">
                                                                            <span class="text-[10px] text-brown" x-text="zone.name"></span>
                                                                        </label>
                                                                    </template>
                                                                    <p x-show="shippingZones.length === 0" class="text-[10px] text-muted col-span-2">Loading zones…</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-end justify-end gap-2">
                                                            <button type="button" @click="saveTierPrice(index, tIndex)" class="text-[10px] font-bold bg-gold-antique text-white px-3 py-1.5 rounded cursor-pointer hover:bg-gold-warm transition">Save</button>
                                                            <button type="button" @click="removeTierPrice(index, tIndex)" class="text-[10px] font-bold bg-red-100 text-red-600 px-3 py-1.5 rounded cursor-pointer hover:bg-red-200 transition">Del</button>
                                                        </div>
                                                    </div>
                                                    <div x-show="tier.saveError" class="mt-2 text-[10px] text-red-600" x-text="tier.saveError"></div>
                                                    <div x-show="tier.saveSuccess" class="mt-2 text-[10px] text-green-600">Saved!</div>
                                                </div>
                                            </template>
                                            <p x-show="!variant.tier_prices || variant.tier_prices.length === 0" class="text-[10px] text-muted italic">No tier prices yet.</p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- SEO --}}
                <div class="bg-white border border-champagne rounded-xl p-6 space-y-4">
                    <h3 class="text-sm font-bold text-brown">SEO</h3>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1">Meta Title</label>
                        <input type="text" x-model="form.meta_title"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1">Meta Description</label>
                        <textarea x-model="form.meta_description" rows="2"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1">Meta Keywords</label>
                        <input type="text" x-model="form.meta_keywords"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                    </div>
                </div>

                {{-- Landing Page --}}
                <div class="bg-white border border-champagne rounded-xl p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-brown">Landing Page</h3>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" x-model="form.is_landing_enabled" class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-primary rounded-full transition"></div>
                                <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-sm font-medium text-brown">Enabled</span>
                        </label>
                    </div>
                    <div x-show="form.is_landing_enabled">
                        <label class="block text-xs font-medium text-muted mb-1">Landing Slug</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-taupe">/landing/</span>
                            <input type="text" x-model="form.landing_slug"
                                class="flex-1 border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                                :class="errors.landing_slug ? 'border-red-400' : 'border-champagne'">
                        </div>
                        <p x-show="errors.landing_slug" class="mt-1 text-xs text-red-600" x-text="errors.landing_slug?.[0]"></p>
                    </div>
                </div>

            </div>

            {{-- ==============================
                 RIGHT COLUMN (sidebar)
            ============================== --}}
            <div class="space-y-6">

                {{-- Publish box --}}
                <div class="bg-white border border-champagne rounded-xl p-5 space-y-4">
                    <h3 class="text-sm font-bold text-brown">Publish</h3>

                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Category <span class="text-red-500">*</span></label>
                        <select x-model="form.category_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique cursor-pointer"
                            :class="errors.category_id ? 'border-red-400' : 'border-champagne'">
                            <option value="">Select category…</option>
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                        <p x-show="errors.category_id" class="mt-1 text-xs text-red-600" x-text="errors.category_id?.[0]"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Base Price (৳) <span class="text-red-500">*</span></label>
                        <input type="number" x-model.number="form.base_price" min="0" step="0.01"
                            class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                            :class="errors.base_price ? 'border-red-400' : 'border-champagne'">
                        <p x-show="errors.base_price" class="mt-1 text-xs text-red-600" x-text="errors.base_price?.[0]"></p>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-champagne">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-primary rounded-full transition"></div>
                                <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-sm font-medium text-brown">Published (Active)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" x-model="form.is_featured" class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-yellow-500 rounded-full transition"></div>
                                <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-sm font-medium text-brown">Featured</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" x-model="form.is_trending" class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-orange-500 rounded-full transition"></div>
                                <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-sm font-medium text-brown">Trending</span>
                        </label>
                    </div>

                    <div class="pt-2 border-t border-champagne flex gap-3">
                        <a href="{{ route('admin.products') }}"
                            class="flex-1 text-center text-sm font-medium text-muted border border-champagne rounded-lg py-2 hover:bg-cream transition">
                            Cancel
                        </a>
                        <button type="submit" :disabled="saving"
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-gold-antique hover:bg-gold-antique disabled:opacity-60 text-white text-sm font-medium py-2 rounded-lg transition cursor-pointer">
                            <i x-show="saving" class="fa-solid fa-spinner fa-spin text-xs"></i>
                            <span x-text="saving ? 'Saving…' : 'Save Changes'"></span>
                        </button>
                    </div>
                </div>

                {{-- Thumbnail --}}
                <div class="bg-white border border-champagne rounded-xl p-5">
                    <h3 class="text-sm font-bold text-brown mb-3">Thumbnail</h3>

                    <template x-if="thumbnailPreview">
                        <div class="mb-3 relative group">
                            <img :src="thumbnailPreview" class="w-full h-40 object-cover rounded-lg border border-champagne">
                            <button type="button" @click="thumbnailPreview = null; thumbnail = null"
                                class="absolute top-2 right-2 w-6 h-6 bg-red-600 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition cursor-pointer flex items-center justify-center">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </template>

                    <label class="cursor-pointer flex flex-col items-center gap-2 border-2 border-dashed border-champagne rounded-xl px-4 py-5 hover:border-gold-warm hover:bg-ivory transition text-center">
                        <i class="fa-solid fa-image text-gray-300 text-2xl"></i>
                        <span class="text-sm text-muted" x-text="thumbnailPreview ? 'Replace thumbnail' : 'Upload thumbnail'"></span>
                        <span class="text-xs text-taupe">JPG, PNG, WebP · Max 2MB</span>
                        <input type="file" accept="image/*" class="sr-only" @change="handleThumbnail($event)">
                    </label>
                    <p x-show="errors.thumbnail" class="mt-1 text-xs text-red-600" x-text="errors.thumbnail?.[0]"></p>
                </div>

                {{-- Certifications --}}
                <div class="bg-white border border-champagne rounded-xl p-5">
                    <h3 class="text-sm font-bold text-brown mb-3">Certifications</h3>
                    <div class="space-y-2 max-h-60 overflow-y-auto no-scrollbar pr-2">
                        <template x-for="cert in availableCertifications" :key="cert.id">
                            <label class="flex items-center gap-3 p-2 rounded-lg border border-transparent hover:border-champagne hover:bg-cream transition cursor-pointer group">
                                <input type="checkbox" 
                                    :value="cert.id" 
                                    x-model="form.certifications"
                                    class="w-4 h-4 rounded border-champagne text-gold-antique focus:ring-gold-antique">
                                <div class="flex items-center gap-2 flex-1">
                                    <template x-if="cert.logo_url">
                                        <img :src="cert.logo_url" class="w-6 h-6 object-contain bg-white rounded border border-champagne p-0.5">
                                    </template>
                                    <span class="text-sm text-brown group-hover:text-gold-antique transition" x-text="cert.name"></span>
                                </div>
                            </label>
                        </template>
                        <template x-if="availableCertifications.length === 0">
                            <p class="text-xs text-muted italic">No certifications available.</p>
                        </template>
                    </div>
                </div>

                {{-- Gallery --}}
                <div class="bg-white border border-champagne rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-brown">Gallery</h3>
                        <span class="text-xs text-taupe"
                            x-text="(galleryExisting.length + galleryFiles.length) + ' images'"></span>
                    </div>

                    {{-- Existing gallery images --}}
                    <template x-if="galleryExisting.length > 0">
                        <div class="mb-3">
                            <p class="text-xs text-muted mb-2">Current images</p>
                            <div class="grid grid-cols-3 gap-2">
                                <template x-for="(img, i) in galleryExisting" :key="i">
                                    <div class="relative group">
                                        <img :src="'/storage/' + img" class="w-full h-20 object-cover rounded-lg border border-champagne">
                                        <button type="button" @click="removeGalleryExisting(img)"
                                            class="absolute top-1 right-1 w-5 h-5 bg-red-600 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition cursor-pointer flex items-center justify-center">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- New gallery images to upload --}}
                    <template x-if="galleryPreviews.length > 0">
                        <div class="mb-3">
                            <p class="text-xs text-muted mb-2">New images to add</p>
                            <div class="grid grid-cols-3 gap-2">
                                <template x-for="(img, i) in galleryPreviews" :key="i">
                                    <div class="relative group">
                                        <img :src="img" class="w-full h-20 object-cover rounded-lg border border-blue-100">
                                        <button type="button" @click="removeGalleryNew(i)"
                                            class="absolute top-1 right-1 w-5 h-5 bg-red-600 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition cursor-pointer flex items-center justify-center">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <label class="cursor-pointer flex items-center gap-2 border border-dashed border-champagne rounded-lg px-4 py-3 hover:border-gold-warm hover:bg-ivory transition text-sm text-muted">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Add more images
                        <input type="file" accept="image/*" multiple class="sr-only" @change="handleGallery($event)">
                    </label>
                </div>

            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function productForm(productId) {
    return {
        productId,
        isEdit: productId !== null,
        loading: productId !== null,
        saving: false,
        errors: {},
        submitError: null,
        categories: [],

        form: {
            name: '',
            slug: '',
            short_description: '',
            note: '',
            description: '',
            category_id: '',
            base_price: '',
            is_active: true,
            is_featured: false,
            is_trending: false,
            meta_title: '',
            meta_description: '',
            meta_keywords: '',
            landing_slug: '',
            is_landing_enabled: false,
            certifications: [],
        },
        availableCertifications: [],

        thumbnail: null,
        thumbnailPreview: null,
        galleryFiles: [],
        galleryPreviews: [],
        galleryExisting: [],
        galleryRemove: [],

        variants: [],
        shippingZones: [],
        _giftSearchTimers: {},

        async init() {
            await Promise.all([
                this.loadCategories(), 
                this.loadShippingZones(),
                this.loadAvailableCertifications()
            ]);
            if (this.isEdit) {
                await this.loadProduct();
            } else {
                this.addVariant();
            }
        },

        async loadShippingZones() {
            try {
                const r = await fetch('/api/v1/admin/shipping-zones', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await r.json();
                this.shippingZones = data.data ?? [];
            } catch (e) {
                console.error('Failed to load shipping zones', e);
            }
        },

        searchGiftVariant(vIndex, tIndex, query) {
            const key = `${vIndex}_${tIndex}`;
            clearTimeout(this._giftSearchTimers[key]);
            if (!query || query.length < 2) {
                this.variants[vIndex].tier_prices[tIndex].giftResults = [];
                return;
            }
            this._giftSearchTimers[key] = setTimeout(async () => {
                try {
                    const r = await fetch(`/api/v1/admin/orders/search-products?q=${encodeURIComponent(query)}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await r.json();
                    const results = (data.data ?? []).filter(item => item.type === 'variant');
                    this.variants[vIndex].tier_prices[tIndex].giftResults = results;
                } catch (e) {
                    console.error('Gift variant search failed', e);
                }
            }, 300);
        },

        toggleZone(vIndex, tIndex, zoneId, checked) {
            const tier = this.variants[vIndex].tier_prices[tIndex];
            if (!tier.free_delivery_zones) tier.free_delivery_zones = [];
            if (checked) {
                if (!tier.free_delivery_zones.includes(zoneId)) {
                    tier.free_delivery_zones.push(zoneId);
                }
            } else {
                tier.free_delivery_zones = tier.free_delivery_zones.filter(id => id !== zoneId);
            }
        },

        async loadCategories() {
            try {
                const r = await fetch('/api/v1/admin/categories?per_page=100', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await r.json();
                this.categories = data.data ?? [];
            } catch (e) { console.error(e); }
        },

        async loadAvailableCertifications() {
            try {
                const r = await fetch('/api/v1/admin/certifications/all', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await r.json();
                this.availableCertifications = data.data ?? [];
            } catch (e) { console.error(e); }
        },

        async loadProduct() {
            try {
                const r = await fetch(`/api/v1/admin/products/${this.productId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await r.json();

                if (!r.ok) {
                    this.submitError = data.message ?? 'Failed to load product.';
                    return;
                }

                const p = data.data;

                this.form = {
                    name: p.name ?? '',
                    slug: p.slug ?? '',
                    short_description: p.short_description ?? '',
                    note: p.note ?? '',
                    description: p.description ?? '',
                    category_id: p.category?.id ?? '',
                    base_price: p.base_price ?? '',
                    is_active: p.is_active ?? true,
                    is_featured: p.is_featured ?? false,
                    is_trending: p.is_trending ?? false,
                    meta_title: p.meta_title ?? '',
                    meta_description: p.meta_description ?? '',
                    meta_keywords: p.meta_keywords ?? '',
                    landing_slug: p.landing_slug ?? '',
                    is_landing_enabled: p.is_landing_enabled ?? false,
                    certifications: (p.certifications ?? []).map(c => c.id),
                };

                this.thumbnailPreview = p.image_url ?? null;
                this.galleryExisting = p.gallery ?? [];

                this.variants = (p.variants ?? []).map(v => ({
                    id: v.id,
                    title: v.title ?? '',
                    sku: v.sku ?? '',
                    price: v.price ?? '',
                    stock: v.stock ?? 0,
                    reserved_stock: v.reserved_stock ?? 0,
                    weight_grams: v.weight_grams ?? '',
                    note: v.note ?? '',
                    discount_type: v.discount_type ?? '',
                    discount_value: v.discount_value ?? '',
                    sale_ends_at: v.sale_ends_at
                        ? new Date(v.sale_ends_at).toISOString().slice(0, 16)
                        : '',
                    is_active: v.is_active !== false,
                    tier_prices: (v.tier_prices ?? []).map(t => ({
                        id: t.id,
                        min_quantity: t.min_quantity,
                        discount_type: t.discount_type,
                        discount_value: t.discount_value,
                        has_free_delivery: t.has_free_delivery,
                        free_delivery_zones: t.free_delivery_zones || [],
                        gift_product_variant_id: t.gift_product_variant_id || null,
                        gift_label: t.gift_product_variant_id
                            ? (t.gift_variant_name ? t.gift_variant_name + ' (#' + t.gift_product_variant_id + ')' : '#' + t.gift_product_variant_id)
                            : '',
                        gift_quantity: t.gift_quantity || 1,
                        giftResults: [],
                        saveError: null,
                        saveSuccess: false,
                    })),
                }));

                if (this.variants.length === 0) {
                    this.addVariant();
                }
            } catch (e) {
                this.submitError = 'Network error loading product data.';
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        addVariant() {
            this.variants.push({
                id: null,
                title: '',
                sku: '',
                price: '',
                stock: 0,
                reserved_stock: 0,
                weight_grams: '',
                note: '',
                discount_type: '',
                discount_value: '',
                sale_ends_at: '',
                is_active: true,
                tier_prices: [],
            });
        },

        removeVariant(index) {
            if (this.variants.length > 1) {
                this.variants.splice(index, 1);
            }
        },

        addTierPrice(vIndex) {
            if (!this.variants[vIndex].tier_prices) this.variants[vIndex].tier_prices = [];
            this.variants[vIndex].tier_prices.push({
                id: null,
                min_quantity: 2,
                discount_type: 'percentage',
                discount_value: 0,
                has_free_delivery: false,
                free_delivery_zones: [],
                gift_product_variant_id: null,
                gift_label: '',
                gift_quantity: 1,
                giftResults: [],
                saveError: null,
                saveSuccess: false,
            });
        },

        async saveTierPrice(vIndex, tIndex) {
            const variant = this.variants[vIndex];
            const tier = variant.tier_prices[tIndex];
            tier.saveError = null;
            tier.saveSuccess = false;

            if (!variant.id) {
                tier.saveError = "Save the product/variant first before adding tiers.";
                return;
            }

            try {
                const fd = {
                    min_quantity: tier.min_quantity,
                    discount_type: tier.discount_type,
                    discount_value: tier.discount_value,
                    has_free_delivery: tier.has_free_delivery,
                    free_delivery_zones: tier.free_delivery_zones,
                    gift_product_variant_id: tier.gift_product_variant_id || null,
                    gift_quantity: tier.gift_quantity || 1,
                };

                const r = await fetch(`/api/v1/admin/products/${variant.id}/tier-prices`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(fd)
                });

                const data = await r.json();

                if (r.ok) {
                    tier.id = data.data.id;
                    tier.saveSuccess = true;
                    setTimeout(() => tier.saveSuccess = false, 2000);
                } else if (r.status === 422) {
                    tier.saveError = Object.values(data.errors)[0][0];
                } else {
                    tier.saveError = data.message || "Failed to save.";
                }
            } catch (e) {
                tier.saveError = "Network error.";
            }
        },

        async removeTierPrice(vIndex, tIndex) {
            const variant = this.variants[vIndex];
            const tier = variant.tier_prices[tIndex];

            if (!tier.id) {
                variant.tier_prices.splice(tIndex, 1);
                return;
            }

            if (!confirm("Are you sure you want to delete this tier price?")) return;

            try {
                const r = await fetch(`/api/v1/admin/products/${variant.id}/tier-prices/${tier.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                if (r.ok) {
                    variant.tier_prices.splice(tIndex, 1);
                } else {
                    alert("Failed to delete tier.");
                }
            } catch (e) {
                alert("Network error.");
            }
        },

        handleThumbnail(e) {
            this.thumbnail = e.target.files[0] ?? null;
            if (this.thumbnail) {
                const reader = new FileReader();
                reader.onload = (ev) => { this.thumbnailPreview = ev.target.result; };
                reader.readAsDataURL(this.thumbnail);
            }
        },

        handleGallery(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                this.galleryFiles.push(file);
                const reader = new FileReader();
                reader.onload = (ev) => { this.galleryPreviews.push(ev.target.result); };
                reader.readAsDataURL(file);
            });
            e.target.value = '';
        },

        removeGalleryNew(index) {
            this.galleryFiles.splice(index, 1);
            this.galleryPreviews.splice(index, 1);
        },

        removeGalleryExisting(path) {
            this.galleryExisting = this.galleryExisting.filter(p => p !== path);
            this.galleryRemove.push(path);
        },

        buildFormData() {
            const fd = new FormData();

            const boolFields = ['is_active', 'is_featured', 'is_trending', 'is_landing_enabled'];
            Object.entries(this.form).forEach(([key, val]) => {
                if (val === null || val === undefined || val === '') return;
                fd.append(key, boolFields.includes(key) ? (val ? '1' : '0') : val);
            });
            boolFields.forEach(key => {
                if (!fd.has(key)) fd.append(key, '0');
            });

            if (this.thumbnail) fd.append('thumbnail', this.thumbnail);
            this.galleryFiles.forEach(f => fd.append('gallery[]', f));
            this.galleryRemove.forEach(p => fd.append('gallery_remove[]', p));

            this.variants.forEach((v, i) => {
                if (v.id) fd.append(`variants[${i}][id]`, v.id);
                fd.append(`variants[${i}][title]`, v.title ?? '');
                fd.append(`variants[${i}][sku]`, v.sku ?? '');
                fd.append(`variants[${i}][price]`, v.price ?? 0);
                fd.append(`variants[${i}][stock]`, v.stock ?? 0);
                fd.append(`variants[${i}][is_active]`, v.is_active ? '1' : '0');
                if (v.weight_grams) fd.append(`variants[${i}][weight_grams]`, v.weight_grams);
                if (v.note) fd.append(`variants[${i}][note]`, v.note);
                if (v.discount_type) fd.append(`variants[${i}][discount_type]`, v.discount_type);
                if (v.discount_value) fd.append(`variants[${i}][discount_value]`, v.discount_value);
                if (v.sale_ends_at) fd.append(`variants[${i}][sale_ends_at]`, v.sale_ends_at);
            });

            return fd;
        },

        async submit() {
            this.saving = true;
            this.errors = {};
            this.submitError = null;

            const fd = this.buildFormData();
            this.form.certifications.forEach(id => {
                fd.append('certifications[]', id);
            });

            const url = this.isEdit
                ? `/api/v1/admin/products/${this.productId}`
                : '/api/v1/admin/products';

            if (this.isEdit) fd.append('_method', 'PUT');

            try {
                const r = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: fd,
                });

                const data = await r.json();

                if (r.ok) {
                    window.location.href = '/admin/products';
                } else if (r.status === 422) {
                    this.errors = data.errors ?? {};
                    this.$nextTick(() => {
                        const el = document.querySelector('.border-red-400');
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                } else {
                    this.submitError = data.message ?? 'An unexpected error occurred.';
                }
            } catch (e) {
                this.submitError = 'Network error. Please check your connection.';
            } finally {
                this.saving = false;
            }
        },
    };
}
</script>
@endpush










