@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')

<div x-data="productForm(null)" x-init="init()">

    {{-- Page Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.products') }}" class="text-taupe hover:text-brown transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-lg font-bold text-brown">Add New Product</h2>
            <p class="text-sm text-muted mt-0.5">Fill in the details to create a new product</p>
        </div>
    </div>

    {{-- Global error banner --}}
    <div x-show="submitError" x-cloak class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        <i class="fa-solid fa-circle-exclamation mr-2"></i>
        <span x-text="submitError"></span>
    </div>

    <form @submit.prevent="submit()">
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
                        <input type="text" x-model="form.name" @input.debounce.300ms="autoSlug()"
                            class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                            :class="errors.name ? 'border-red-400' : 'border-champagne'"
                            placeholder="e.g. Mangrove Gold Honey (ম্যানগ্রোভ গোল্ড হানি) 500gm">
                        <p x-show="errors.name" class="mt-1 text-xs text-red-600" x-text="errors.name?.[0]"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Slug</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-taupe shrink-0">/products/</span>
                            <input type="text" x-model="form.slug" @input="slugEdited = true"
                                class="flex-1 border rounded-lg px-3 py-2 text-sm font-mono outline-none focus:ring-2 focus:ring-gold-antique"
                                :class="errors.slug ? 'border-red-400' : 'border-champagne'"
                                placeholder="auto-generated from name">
                            <button type="button" @click="slugEdited = false; autoSlug()"
                                class="shrink-0 text-xs text-taupe hover:text-gold-antique transition cursor-pointer" title="Reset to auto">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-taupe">Leave blank to auto-generate from name. Letters, numbers, and hyphens only.</p>
                        <p x-show="errors.slug" class="mt-1 text-xs text-red-600" x-text="errors.slug?.[0]"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-brown mb-1">Short Description</label>
                        <input type="text" x-model="form.short_description"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                            placeholder="One-liner for product cards">
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
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique resize-y"
                            placeholder="Detailed product description…"></textarea>
                    </div>
                </div>

                {{-- Variants --}}
                <div class="bg-white border border-champagne rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-brown">Variants <span class="text-red-500">*</span></h3>
                        <button type="button" @click="addVariant()"
                            class="inline-flex items-center gap-1.5 text-sm text-gold-antique font-medium hover:text-brand transition cursor-pointer">
                            <i class="fa-solid fa-plus text-xs"></i> Add Variant
                        </button>
                    </div>

                    <p x-show="errors.variants" class="mb-3 text-xs text-red-600" x-text="errors.variants?.[0]"></p>

                    <div class="space-y-4">
                        <template x-for="(variant, index) in variants" :key="index">
                            <div class="border border-champagne rounded-xl p-4 relative">
                                {{-- Variant header --}}
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-semibold text-muted uppercase tracking-wider"
                                        x-text="'Variant ' + (index + 1) + (variant.title ? ' — ' + variant.title : '')"></span>
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
                                            :class="errors['variants.' + index + '.title'] ? 'border-red-400' : 'border-champagne'"
                                            placeholder="e.g. 1kg Jar">
                                        <p x-show="errors['variants.' + index + '.title']" class="mt-1 text-xs text-red-600"
                                            x-text="errors['variants.' + index + '.title']?.[0]"></p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-muted mb-1">SKU <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="variant.sku"
                                            class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique font-mono"
                                            :class="errors['variants.' + index + '.sku'] ? 'border-red-400' : 'border-champagne'"
                                            placeholder="MGH-1KG-JAR">
                                        <p x-show="errors['variants.' + index + '.sku']" class="mt-1 text-xs text-red-600"
                                            x-text="errors['variants.' + index + '.sku']?.[0]"></p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-muted mb-1">Price (৳) <span class="text-red-500">*</span></label>
                                        <input type="number" x-model.number="variant.price" min="0" step="0.01"
                                            class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                                            :class="errors['variants.' + index + '.price'] ? 'border-red-400' : 'border-champagne'"
                                            placeholder="0.00">
                                        <p x-show="errors['variants.' + index + '.price']" class="mt-1 text-xs text-red-600"
                                            x-text="errors['variants.' + index + '.price']?.[0]"></p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-muted mb-1">Stock</label>
                                        <input type="number" x-model.number="variant.stock" min="0"
                                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                                            placeholder="0">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-muted mb-1">Weight (grams)</label>
                                        <input type="number" x-model.number="variant.weight_grams" min="0"
                                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                                            placeholder="1000">
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
                                                class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                                                placeholder="0">
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

                                {{-- Variant active toggle --}}
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
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                            placeholder="Overrides product name in search results">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1">Meta Description</label>
                        <textarea x-model="form.meta_description" rows="2"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique resize-none"
                            placeholder="160 chars max"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1">Meta Keywords</label>
                        <input type="text" x-model="form.meta_keywords"
                            class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                            placeholder="protein, whey, supplement">
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
                            <span class="text-sm text-taupe">/product-page/</span>
                            <input type="text" x-model="form.landing_slug"
                                class="flex-1 border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique"
                                :class="errors.landing_slug ? 'border-red-400' : 'border-champagne'"
                                placeholder="my-product-campaign">
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
                            :class="errors.base_price ? 'border-red-400' : 'border-champagne'"
                            placeholder="0.00">
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
                            <span x-text="saving ? 'Saving…' : 'Create Product'"></span>
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

                    <label class="cursor-pointer flex flex-col items-center gap-2 border-2 border-dashed border-champagne rounded-xl px-4 py-6 hover:border-gold-warm hover:bg-ivory transition text-center">
                        <i class="fa-solid fa-image text-gray-300 text-3xl"></i>
                        <span class="text-sm text-muted">Click to upload thumbnail</span>
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
                    <h3 class="text-sm font-bold text-brown mb-3">Gallery</h3>

                    <template x-if="galleryPreviews.length > 0">
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <template x-for="(img, i) in galleryPreviews" :key="i">
                                <div class="relative group">
                                    <img :src="img" class="w-full h-20 object-cover rounded-lg border border-champagne">
                                    <button type="button" @click="removeGalleryNew(i)"
                                        class="absolute top-1 right-1 w-5 h-5 bg-red-600 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition cursor-pointer flex items-center justify-center">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    <label class="cursor-pointer flex items-center gap-2 border border-dashed border-champagne rounded-lg px-4 py-3 hover:border-gold-warm hover:bg-ivory transition text-sm text-muted">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Add gallery images
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
        loading: false,
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
        slugEdited: false,
        availableCertifications: [],

        thumbnail: null,
        thumbnailPreview: null,
        galleryFiles: [],
        galleryPreviews: [],

        variants: [],

        async init() {
            await Promise.all([
                this.loadCategories(),
                this.loadAvailableCertifications()
            ]);
            if (!this.isEdit) {
                this.addVariant();
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

        autoSlug() {
            if (this.slugEdited) return;
            const s = this.form.name
                .toLowerCase()
                .replace(/[^\p{L}\p{N}]+/gu, '-')
                .replace(/(^-|-$)/g, '');
            this.form.slug = s;
        },

        addVariant() {
            this.variants.push({
                id: null,
                title: '',
                sku: '',
                price: '',
                stock: 0,
                weight_grams: '',
                note: '',
                discount_type: '',
                discount_value: '',
                sale_ends_at: '',
                is_active: true,
            });
        },

        removeVariant(index) {
            if (this.variants.length > 1) {
                this.variants.splice(index, 1);
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

        buildFormData() {
            const fd = new FormData();

            // Basic fields
            const boolFields = ['is_active', 'is_featured', 'is_trending', 'is_landing_enabled'];
            Object.entries(this.form).forEach(([key, val]) => {
                if (val === null || val === undefined || val === '') return;
                if (key === 'certifications') return; // Handled below
                fd.append(key, boolFields.includes(key) ? (val ? '1' : '0') : val);
            });

            this.form.certifications.forEach(id => {
                fd.append('certifications[]', id);
            });
            // Ensure booleans are always sent
            boolFields.forEach(key => {
                if (!fd.has(key)) fd.append(key, '0');
            });

            if (this.thumbnail) fd.append('thumbnail', this.thumbnail);
            this.galleryFiles.forEach(f => fd.append('gallery[]', f));

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










