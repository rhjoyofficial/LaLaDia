@extends('layouts.admin')

@section('title', 'Edit Combo')

@section('content')

<div x-data="comboForm({{ $comboId }})" x-init="init()">

    {{-- Loading skeleton --}}
    <template x-if="loading">
        <div class="space-y-4">
            <div class="h-10 bg-gray-100 rounded-xl animate-pulse w-64"></div>
            <div class="h-64 bg-white border border-champagne rounded-xl animate-pulse"></div>
            <div class="h-48 bg-white border border-champagne rounded-xl animate-pulse"></div>
        </div>
    </template>

    <template x-if="!loading">
        <div>
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.combos') }}" class="text-taupe hover:text-brown transition">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="text-lg font-bold text-brown">Edit Combo</h2>
                        <p class="text-sm text-muted mt-0.5 font-mono" x-text="form.slug"></p>
                    </div>
                </div>
                <button @click="submit()" :disabled="saving"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold bg-gold-antique text-white rounded-lg hover:bg-gold-antique disabled:opacity-50 transition cursor-pointer disabled:cursor-not-allowed">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span x-text="saving ? 'Saving…' : 'Save Changes'"></span>
                </button>
            </div>

            {{-- Global errors --}}
            <template x-if="errors._global">
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                    <span x-text="errors._global"></span>
                </div>
            </template>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- ── LEFT / MAIN ─────────────────────────────────────── --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Basic Info --}}
                    <div class="bg-white border border-champagne rounded-xl p-5 space-y-4">
                        <h3 class="font-semibold text-brown text-sm">Basic Information</h3>

                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" x-model="form.title"
                                :class="errors.title ? 'border-red-400' : 'border-champagne'"
                                class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                            <p x-show="errors.title" class="text-xs text-red-500 mt-1" x-text="errors.title?.[0]"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Slug</label>
                            <input type="text" x-model="form.slug"
                                :class="errors.slug ? 'border-red-400' : 'border-champagne'"
                                class="w-full border rounded-lg px-3 py-2 text-sm font-mono outline-none focus:ring-2 focus:ring-gold-antique">
                            <p x-show="errors.slug" class="text-xs text-red-500 mt-1" x-text="errors.slug?.[0]"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Description</label>
                            <textarea x-model="form.description" rows="3"
                                class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique resize-none">
                            </textarea>
                        </div>

                        {{-- Image --}}
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Combo Image</label>
                            <div class="flex items-start gap-4">
                                <div class="w-24 h-24 rounded-xl border-2 border-dashed border-champagne overflow-hidden flex items-center justify-center bg-cream flex-shrink-0 cursor-pointer"
                                    @click="$refs.imageInput.click()">
                                    <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover">
                                    <div x-show="!imagePreview" class="text-center p-2">
                                        <i class="fa-solid fa-image text-gray-300 text-xl"></i>
                                        <p class="text-xs text-taupe mt-1">Upload</p>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <input type="file" x-ref="imageInput" accept="image/*" class="hidden"
                                        @change="onImageChange($event)">
                                    <button type="button" @click="$refs.imageInput.click()"
                                        class="text-sm text-blue-600 hover:text-blue-800 font-medium cursor-pointer">
                                        Replace image
                                    </button>
                                    <p class="text-xs text-taupe mt-1">JPG, PNG, WebP — max 2 MB</p>
                                    <p x-show="errors.image" class="text-xs text-red-500 mt-1" x-text="errors.image?.[0]"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Components ─────────────────────────────────── --}}
                    <div class="bg-white border border-champagne rounded-xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-brown text-sm">
                                Components
                                <span class="ml-1.5 inline-flex items-center justify-center w-5 h-5 rounded-full bg-cream text-gold-antique text-xs font-bold"
                                    x-text="items.length">
                                </span>
                            </h3>
                        </div>
                        <p x-show="errors.items" class="text-xs text-red-500" x-text="errors.items?.[0]"></p>

                        {{-- Search --}}
                        <div class="relative" x-data="{ open: false }">
                            <div class="relative">
                                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-taupe text-xs"></i>
                                <input type="text" x-model="variantSearch"
                                    @focus="open = true"
                                    @input.debounce.350ms="searchVariants()"
                                    placeholder="Search products to add or swap components…"
                                    class="w-full pl-9 pr-4 py-2 text-sm border border-champagne rounded-lg outline-none focus:ring-2 focus:ring-gold-antique">
                                <div x-show="variantSearching" class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i class="fa-solid fa-spinner fa-spin text-taupe text-xs"></i>
                                </div>
                            </div>
                            <div x-show="open && variantResults.length > 0" x-cloak
                                @click.outside="open = false"
                                class="absolute top-full left-0 right-0 mt-1 bg-white border border-champagne rounded-xl shadow-xl z-30 max-h-72 overflow-y-auto">
                                <template x-for="product in variantResults" :key="product.id">
                                    <div class="border-b border-gray-50 last:border-0">
                                        <div class="px-4 py-2 bg-cream flex items-center gap-2">
                                            <div class="w-6 h-6 rounded overflow-hidden flex-shrink-0 bg-gray-200">
                                                <img x-show="product.thumbnail" :src="product.thumbnail" class="w-full h-full object-cover">
                                            </div>
                                            <span class="text-xs font-semibold text-brown" x-text="product.name"></span>
                                        </div>
                                        <template x-for="variant in (product.variants ?? [])" :key="variant.id">
                                            <button type="button"
                                                @click="addItem(product, variant); open = false; variantSearch = ''; variantResults = [];"
                                                :disabled="isAdded(variant.id)"
                                                class="w-full text-left px-5 py-2 flex items-center justify-between hover:bg-ivory transition disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                                                <div>
                                                    <span class="text-xs font-medium text-brown" x-text="variant.title"></span>
                                                    <span class="ml-2 text-xs text-taupe font-mono" x-text="variant.sku ?? ''"></span>
                                                </div>
                                                <div class="flex items-center gap-3 text-xs text-right">
                                                    <span class="text-muted">৳<span x-text="Number(variant.final_price ?? variant.price).toLocaleString()"></span></span>
                                                    <span :class="variant.available_stock > 0 ? 'text-primary' : 'text-red-500'"
                                                        x-text="variant.available_stock > 0 ? variant.available_stock + ' in stock' : 'Out of stock'">
                                                    </span>
                                                    <span x-show="isAdded(variant.id)" class="text-primary font-bold">✓ Added</span>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Items list --}}
                        <template x-if="items.length === 0">
                            <div class="text-center py-8 border-2 border-dashed border-champagne rounded-xl text-taupe">
                                <i class="fa-solid fa-box-open text-2xl mb-2 block"></i>
                                <p class="text-sm">No components yet — search to add</p>
                            </div>
                        </template>
                        <template x-if="items.length > 0">
                            <div class="space-y-2">
                                <template x-for="(item, idx) in items" :key="item.variant_id">
                                    <div class="flex items-center gap-3 p-3 bg-cream rounded-xl border border-champagne">
                                        <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-white border border-champagne">
                                            <img x-show="item.product_thumbnail" :src="item.product_thumbnail" class="w-full h-full object-cover">
                                            <div x-show="!item.product_thumbnail" class="w-full h-full flex items-center justify-center text-gray-300">
                                                <i class="fa-solid fa-box text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-brown truncate" x-text="item.product_name"></p>
                                            <p class="text-xs text-muted truncate" x-text="item.variant_title"></p>
                                            <p class="text-xs text-taupe">৳<span x-text="Number(item.unit_price).toLocaleString()"></span> each</p>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <label class="text-xs text-muted">Qty</label>
                                            <input type="number" x-model.number="item.quantity" min="1" max="99"
                                                @input="computeAutoPrice()"
                                                class="w-16 border border-champagne rounded-lg px-2 py-1 text-sm text-center outline-none focus:ring-2 focus:ring-gold-antique">
                                        </div>
                                        <button type="button" @click="removeItem(idx)"
                                            class="text-red-400 hover:text-red-600 transition cursor-pointer ml-1">
                                            <i class="fa-solid fa-xmark text-sm"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- ── Pricing ─────────────────────────────────────── --}}
                    <div class="bg-white border border-champagne rounded-xl p-5 space-y-4">
                        <h3 class="font-semibold text-brown text-sm">Pricing</h3>
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-2">Pricing Mode</label>
                            <div class="flex gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" x-model="form.pricing_mode" value="auto" class="accent-green-700">
                                    <span class="text-sm text-brown">Auto <span class="text-xs text-taupe">(sum of components)</span></span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" x-model="form.pricing_mode" value="manual" class="accent-green-700">
                                    <span class="text-sm text-brown">Manual</span>
                                </label>
                            </div>
                        </div>
                        <div x-show="form.pricing_mode === 'auto'" class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
                            <p class="text-xs text-blue-600">Auto price: <span class="font-bold text-blue-800 ml-1">৳<span x-text="Number(autoPrice).toLocaleString()"></span></span></p>
                        </div>
                        <div x-show="form.pricing_mode === 'manual'">
                            <label class="block text-xs font-semibold text-muted mb-1">Manual Price (৳) <span class="text-red-500">*</span></label>
                            <input type="number" x-model="form.manual_price" min="0" step="0.01"
                                :class="errors.manual_price ? 'border-red-400' : 'border-champagne'"
                                class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                            <p x-show="errors.manual_price" class="text-xs text-red-500 mt-1" x-text="errors.manual_price?.[0]"></p>
                        </div>
                        <div class="border-t border-champagne pt-4">
                            <label class="block text-xs font-semibold text-muted mb-2">Discount (optional)</label>
                            <div class="grid grid-cols-2 gap-3">
                                <select x-model="form.discount_type"
                                    class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique cursor-pointer">
                                    <option value="">No discount</option>
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed (৳)</option>
                                </select>
                                <input type="number" x-model="form.discount_value" min="0" step="0.01"
                                    :disabled="!form.discount_type"
                                    :placeholder="form.discount_type === 'percentage' ? 'e.g. 10' : form.discount_type === 'fixed' ? 'e.g. 50' : 'Select type first'"
                                    class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique disabled:bg-cream disabled:text-taupe">
                            </div>
                        </div>
                        <div x-show="finalPrice > 0" class="bg-ivory border border-sand rounded-lg px-4 py-3 flex items-center justify-between">
                            <span class="text-xs text-gold-antique font-medium">Final selling price</span>
                            <span class="text-lg font-bold text-gold-antique">৳<span x-text="Number(finalPrice).toLocaleString()"></span></span>
                        </div>
                    </div>

                    {{-- ── Incentives ──────────────────────────────────── --}}
                    <div class="bg-white border border-champagne rounded-xl p-5 space-y-4">
                        <h3 class="font-semibold text-brown text-sm">Incentives</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-xs font-semibold text-muted mb-0.5">Free Delivery Override</label>
                                <p class="text-[10px] text-taupe">If enabled, this combo grants free shipping regardless of order total.</p>
                            </div>
                            <button type="button" @click="form.has_free_delivery = !form.has_free_delivery"
                                class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors"
                                :class="form.has_free_delivery ? 'bg-primary' : 'bg-gray-300'">
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition"
                                    :class="form.has_free_delivery ? 'translate-x-4' : 'translate-x-0'"></span>
                            </button>
                        </div>

                        <div x-show="form.has_free_delivery" x-cloak class="mt-2 border border-champagne rounded-xl p-4 bg-cream">
                            <h4 class="text-xs font-bold text-brown mb-2 uppercase tracking-wider">Limit to Shipping Zones</h4>
                            <p class="text-[10px] text-taupe mb-3 italic">Leave all unchecked to apply free delivery to ALL zones.</p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                <template x-for="zone in shippingZones" :key="zone.id">
                                    <label class="flex items-center gap-2.5 p-2 bg-white rounded-lg border border-champagne hover:border-gold-antique transition cursor-pointer">
                                        <input type="checkbox"
                                            :value="zone.id"
                                            :checked="(form.free_delivery_zones || []).includes(zone.id)"
                                            @change="toggleZone(zone.id, $event.target.checked)"
                                            class="rounded border-champagne text-gold-antique focus:ring-gold-antique w-4 h-4">
                                        <div class="min-w-0">
                                            <p class="text-xs font-medium text-brown truncate" x-text="zone.name"></p>
                                            <p class="text-[10px] text-taupe" x-text="'৳' + zone.base_charge"></p>
                                        </div>
                                    </label>
                                </template>
                            </div>
                            <p x-show="shippingZones.length === 0" class="text-xs text-muted py-4 text-center">Loading shipping zones…</p>
                        </div>
                    </div>

                    {{-- ── Tier Prices / Incentives ────────────────────── --}}
                    <div class="bg-white border border-champagne rounded-xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-brown text-sm">Tier Prices / Incentives</h3>
                            <button type="button" @click="addTierPrice()"
                                class="inline-flex items-center gap-1.5 text-xs text-gold-antique font-medium hover:text-brand transition cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px]"></i> Add Tier
                            </button>
                        </div>
                        <p class="text-[10px] text-taupe">Add quantity-based discounts, free delivery, or gift items for this combo — identical to product variants.</p>

                        <div class="space-y-3">
                            <template x-for="(tier, tIndex) in tierPrices" :key="tIndex">
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
                                                @input="tier.gift_label = $event.target.value; searchGiftVariant(tIndex, $event.target.value); open = true"
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
                                            <div x-show="tier.has_free_delivery" x-cloak class="mt-1 border border-champagne rounded-md p-2 bg-ivory">
                                                <p class="text-[9px] text-muted mb-1">Limit to zones (leave empty = all zones)</p>
                                                <div class="grid grid-cols-2 gap-1 max-h-24 overflow-y-auto">
                                                    <template x-for="zone in shippingZones" :key="zone.id">
                                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                                            <input type="checkbox"
                                                                :value="zone.id"
                                                                :checked="(tier.free_delivery_zones || []).includes(zone.id)"
                                                                @change="toggleTierZone(tIndex, zone.id, $event.target.checked)"
                                                                class="rounded border-champagne text-gold-antique focus:ring-gold-antique w-3 h-3">
                                                            <span class="text-[10px] text-brown" x-text="zone.name"></span>
                                                        </label>
                                                    </template>
                                                    <p x-show="shippingZones.length === 0" class="text-[10px] text-muted col-span-2">Loading zones…</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-end justify-end gap-2">
                                            <button type="button" @click="saveTierPrice(tIndex)" class="text-[10px] font-bold bg-gold-antique text-white px-3 py-1.5 rounded cursor-pointer hover:bg-gold-warm transition">Save</button>
                                            <button type="button" @click="removeTierPrice(tIndex)" class="text-[10px] font-bold bg-red-100 text-red-600 px-3 py-1.5 rounded cursor-pointer hover:bg-red-200 transition">Del</button>
                                        </div>
                                    </div>
                                    <div x-show="tier.saveError" class="mt-2 text-[10px] text-red-600" x-text="tier.saveError"></div>
                                    <div x-show="tier.saveSuccess" class="mt-2 text-[10px] text-green-600">Saved!</div>
                                </div>
                            </template>
                            <p x-show="tierPrices.length === 0" class="text-[10px] text-muted italic">No tier prices yet. Click "Add Tier" to create one.</p>
                        </div>
                    </div>

                </div>

                {{-- ── RIGHT SIDEBAR ─────────────────────────────────── --}}
                <div class="space-y-4">
                    <div class="bg-white border border-champagne rounded-xl p-5 space-y-4">
                        <h3 class="font-semibold text-brown text-sm">Publish</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-brown">Active</span>
                            <button type="button" @click="form.is_active = !form.is_active"
                                class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors"
                                :class="form.is_active ? 'bg-primary' : 'bg-gray-300'">
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition"
                                    :class="form.is_active ? 'translate-x-4' : 'translate-x-0'"></span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-brown">Featured</span>
                            <button type="button" @click="form.is_featured = !form.is_featured"
                                class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors"
                                :class="form.is_featured ? 'bg-yellow-500' : 'bg-gray-300'">
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition"
                                    :class="form.is_featured ? 'translate-x-4' : 'translate-x-0'"></span>
                            </button>
                        </div>
                        <button @click="submit()" :disabled="saving"
                            class="w-full px-4 py-2.5 text-sm font-semibold bg-gold-antique text-white rounded-lg hover:bg-gold-antique disabled:opacity-50 transition cursor-pointer disabled:cursor-not-allowed">
                            <span x-text="saving ? 'Saving…' : 'Save Changes'"></span>
                        </button>
                        <a href="{{ route('admin.combos') }}"
                            class="block w-full text-center px-4 py-2 text-sm font-medium text-muted border border-champagne rounded-lg hover:bg-cream transition">
                            Cancel
                        </a>
                    </div>

                    {{-- Summary --}}
                    <div class="bg-white border border-champagne rounded-xl p-5" x-show="items.length > 0">
                        <h3 class="font-semibold text-brown text-sm mb-3">Summary</h3>
                        <div class="space-y-2 text-xs text-muted">
                            <div class="flex justify-between">
                                <span>Components</span>
                                <span class="font-semibold" x-text="items.length"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Combined price</span>
                                <span class="font-semibold">৳<span x-text="Number(autoPrice).toLocaleString()"></span></span>
                            </div>
                            <template x-if="form.pricing_mode === 'manual' && form.manual_price">
                                <div class="flex justify-between">
                                    <span>Manual price</span>
                                    <span class="font-semibold">৳<span x-text="Number(form.manual_price).toLocaleString()"></span></span>
                                </div>
                            </template>
                            <template x-if="form.discount_type && form.discount_value">
                                <div class="flex justify-between text-orange-600">
                                    <span>Discount</span>
                                    <span class="font-semibold" x-text="form.discount_type === 'percentage' ? '-' + form.discount_value + '%' : '-৳' + Number(form.discount_value).toLocaleString()"></span>
                                </div>
                            </template>
                            <div class="flex justify-between border-t border-champagne pt-2 font-bold text-brown">
                                <span>Final Price</span>
                                <span>৳<span x-text="Number(finalPrice).toLocaleString()"></span></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </template>

</div>

@endsection

@push('scripts')
<script>
@include('admin.combos._combo_form_script')
</script>
@endpush










