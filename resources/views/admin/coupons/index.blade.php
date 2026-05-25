@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')

    <div x-data="couponManager()" x-init="init()">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-brown">Coupons</h2>
                <p class="text-sm text-muted mt-0.5">Manage discount codes and usage analytics</p>
            </div>
            <div class="flex gap-2">
                @can('coupon.create')
                    <button @click="openBulkModal()"
                        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium bg-white border border-gray-300 rounded-lg hover:bg-cream transition cursor-pointer">
                        <i class="fa-solid fa-wand-magic-sparkles text-xs"></i> Bulk Generate
                    </button>
                    <button @click="openCreateModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-gold-antique text-white rounded-lg hover:bg-gold-antique transition cursor-pointer">
                        <i class="fa-solid fa-plus text-xs"></i> New Coupon
                    </button>
                @endcan
            </div>
        </div>

        {{-- Stats Bar --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-5">
            <div class="bg-white border border-champagne rounded-xl px-4 py-3">
                <p class="text-xs text-muted mb-0.5">Total</p>
                <p class="text-xl font-bold text-brand" x-text="stats.total ?? '—'"></p>
            </div>
            <div class="bg-white border border-champagne rounded-xl px-4 py-3">
                <p class="text-xs text-muted mb-0.5">Active</p>
                <p class="text-xl font-bold text-gold-antique" x-text="stats.active ?? '—'"></p>
            </div>
            <div class="bg-white border border-champagne rounded-xl px-4 py-3">
                <p class="text-xs text-muted mb-0.5">Expired</p>
                <p class="text-xl font-bold text-red-600" x-text="stats.expired ?? '—'"></p>
            </div>
            <div class="bg-white border border-champagne rounded-xl px-4 py-3">
                <p class="text-xs text-muted mb-0.5">Total Uses</p>
                <p class="text-xl font-bold text-brand" x-text="stats.total_usages ?? '—'"></p>
            </div>
            <div class="bg-white border border-champagne rounded-xl px-4 py-3">
                <p class="text-xs text-muted mb-0.5">Discount Given</p>
                <p class="text-xl font-bold text-indigo-700">৳<span x-text="stats.total_discount ? Number(stats.total_discount).toLocaleString() : '0'"></span></p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white border border-champagne rounded-xl mb-4 p-4 flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-52">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-taupe text-xs"></i>
                <input type="text" x-model="search" @input.debounce.400ms="load(1)" placeholder="Search by code…"
                    class="w-full pl-9 pr-4 py-2 text-sm border border-champagne rounded-lg outline-none focus:ring-2 focus:ring-gold-antique">
            </div>
            <select x-model="filterStatus" @change="load(1)"
                class="border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique cursor-pointer">
                <option value="">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="expired">Expired</option>
            </select>
            <button @click="search=''; filterStatus=''; load(1)" x-show="search || filterStatus"
                class="text-xs text-muted hover:text-red-600 transition cursor-pointer px-2">
                <i class="fa-solid fa-xmark"></i> Clear
            </button>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-champagne rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-cream border-b border-champagne">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Code</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Discount</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Scope</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Benefits</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Min. Purchase</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Usage</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Validity</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-muted uppercase"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">

                        <template x-if="loading">
                            <template x-for="i in 8" :key="i">
                                <tr>
                                    <td colspan="9" class="px-5 py-4">
                                        <div class="h-4 bg-gray-100 rounded animate-pulse w-full"></div>
                                    </td>
                                </tr>
                            </template>
                        </template>

                        <template x-if="!loading">
                            <template x-for="c in coupons" :key="c.id">
                                <tr class="hover:bg-cream transition">
                                    {{-- Code --}}
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2">
                                            <button @click="openDetailModal(c)"
                                                class="font-mono text-xs font-semibold text-brown tracking-wide hover:text-gold-antique transition cursor-pointer"
                                                x-text="c.code"></button>
                                            <button @click="copyCode(c.code)"
                                                class="text-taupe hover:text-gold-antique transition cursor-pointer text-xs" title="Copy">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Discount --}}
                                    <td class="px-5 py-3 text-xs">
                                        <template x-if="c.value > 0">
                                            <span>
                                                <span class="font-semibold text-brand"
                                                    x-text="c.type === 'percentage' ? c.value + '%' : '৳' + Number(c.value).toLocaleString()"></span>
                                                <span class="ml-1 text-taupe"
                                                    x-text="c.type === 'percentage' ? 'off' : 'flat'"></span>
                                            </span>
                                        </template>
                                        <template x-if="c.value === 0 || c.value == null">
                                            <span class="text-taupe italic text-xs">No discount</span>
                                        </template>
                                    </td>

                                    {{-- Scope --}}
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-gray-100 text-gray-600': c.applies_to === 'all' || !c.applies_to,
                                                'bg-blue-100 text-blue-700': c.applies_to === 'products',
                                                'bg-purple-100 text-purple-700': c.applies_to === 'combos',
                                            }">
                                            <template x-if="!c.applies_to || c.applies_to === 'all'">
                                                <span><i class="fa-solid fa-globe mr-1 text-[10px]"></i>Global</span>
                                            </template>
                                            <template x-if="c.applies_to === 'products'">
                                                <span><i class="fa-solid fa-box mr-1 text-[10px]"></i>Products</span>
                                            </template>
                                            <template x-if="c.applies_to === 'combos'">
                                                <span><i class="fa-solid fa-cubes mr-1 text-[10px]"></i>Combos</span>
                                            </template>
                                        </span>
                                    </td>

                                    {{-- Benefits --}}
                                    <td class="px-5 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            <template x-if="c.is_free_delivery">
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[10px] font-semibold">
                                                    <i class="fa-solid fa-truck-fast text-[9px]"></i> Free Ship
                                                </span>
                                            </template>
                                            <template x-if="c.gift_product_variant_id">
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-pink-100 text-pink-700 rounded text-[10px] font-semibold">
                                                    <i class="fa-solid fa-gift text-[9px]"></i> Gift
                                                </span>
                                            </template>
                                            <template x-if="!c.is_free_delivery && !c.gift_product_variant_id">
                                                <span class="text-taupe text-xs">—</span>
                                            </template>
                                        </div>
                                    </td>

                                    {{-- Min Purchase --}}
                                    <td class="px-5 py-3 text-xs text-muted"
                                        x-text="c.min_purchase ? '৳' + Number(c.min_purchase).toLocaleString() : '—'"></td>

                                    {{-- Usage --}}
                                    <td class="px-5 py-3 text-xs">
                                        <span class="font-semibold text-brown" x-text="c.usages_count ?? c.used_count"></span>
                                        <span class="text-taupe" x-text="c.usage_limit ? ' / ' + c.usage_limit : ' / ∞'"></span>
                                    </td>

                                    {{-- Validity --}}
                                    <td class="px-5 py-3 text-xs text-muted">
                                        <template x-if="c.start_date || c.end_date">
                                            <span>
                                                <span x-text="c.start_date ? fmtDate(c.start_date) : '∞'"></span>
                                                →
                                                <span x-text="c.end_date ? fmtDate(c.end_date) : '∞'"></span>
                                            </span>
                                        </template>
                                        <template x-if="!c.start_date && !c.end_date">
                                            <span class="text-taupe">No limit</span>
                                        </template>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="statusBadge(c)">
                                            <span x-text="statusLabel(c)"></span>
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <button @click="openDetailModal(c)"
                                                class="text-xs text-taupe hover:text-brand transition cursor-pointer" title="View details">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            @can('coupon.update')
                                                <button @click="openEditModal(c)"
                                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium transition cursor-pointer">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                            @endcan
                                            @can('coupon.delete')
                                                <button @click="confirmDelete(c)"
                                                    class="text-xs text-red-500 hover:text-red-700 transition cursor-pointer">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </template>

                        <template x-if="!loading && coupons.length === 0">
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center text-taupe">
                                    <i class="fa-solid fa-ticket text-2xl mb-2 block"></i>
                                    No coupons found
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-3 border-t border-champagne flex items-center justify-between" x-show="meta.last_page > 1">
                <p class="text-xs text-muted">
                    Page <span x-text="meta.current_page"></span> of <span x-text="meta.last_page"></span>
                    &bull; <span x-text="meta.total"></span> coupons
                </p>
                <div class="flex gap-2">
                    <button @click="load(meta.current_page - 1)" :disabled="meta.current_page <= 1"
                        class="px-3 py-1.5 text-xs font-medium border border-champagne rounded-lg disabled:opacity-40 hover:bg-cream transition cursor-pointer disabled:cursor-not-allowed">
                        &larr; Prev
                    </button>
                    <button @click="load(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page"
                        class="px-3 py-1.5 text-xs font-medium border border-champagne rounded-lg disabled:opacity-40 hover:bg-cream transition cursor-pointer disabled:cursor-not-allowed">
                        Next &rarr;
                    </button>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- CREATE / EDIT MODAL                                          --}}
        {{-- ============================================================ --}}
        <div x-show="showFormModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @keydown.escape.window="showFormModal = false">

            <div @click.outside="showFormModal = false"
                class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] overflow-y-auto">

                <div class="flex items-center justify-between px-6 py-4 border-b border-champagne sticky top-0 bg-white z-10">
                    <h3 class="font-bold text-brown" x-text="editingId ? 'Edit Coupon' : 'New Coupon'"></h3>
                    <button @click="showFormModal = false" class="text-taupe hover:text-brown cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form @submit.prevent="saveCoupon()" class="px-6 py-5 space-y-6">

                    {{-- ─── BASIC ──────────────────────────────────────────────── --}}
                    <div>
                        <p class="text-xs font-bold text-muted uppercase tracking-wider mb-3">Basic</p>
                        <div class="space-y-4">

                            {{-- Code --}}
                            <div x-show="!editingId">
                                <label class="block text-xs font-semibold text-muted mb-1">Code <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.code"
                                    :class="errors.code ? 'border-red-400' : 'border-champagne'"
                                    placeholder="e.g. SAVE20"
                                    class="w-full border rounded-lg px-3 py-2 text-sm uppercase tracking-wide outline-none focus:ring-2 focus:ring-gold-antique">
                                <p x-show="errors.code" class="text-xs text-red-500 mt-1" x-text="errors.code?.[0]"></p>
                            </div>
                            <div x-show="editingId">
                                <label class="block text-xs font-semibold text-muted mb-1">Code</label>
                                <p class="font-mono font-bold text-brown text-sm tracking-wide px-3 py-2 bg-cream rounded-lg" x-text="form.code"></p>
                            </div>

                            {{-- Type + Value --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-muted mb-1">Type <span class="text-red-500">*</span></label>
                                    <select x-model="form.type" :class="errors.type ? 'border-red-400' : 'border-champagne'"
                                        class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique cursor-pointer">
                                        <option value="fixed">Fixed (৳)</option>
                                        <option value="percentage">Percentage (%)</option>
                                    </select>
                                    <p x-show="errors.type" class="text-xs text-red-500 mt-1" x-text="errors.type?.[0]"></p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-muted mb-1">
                                        Value <span class="text-red-500">*</span>
                                        <span class="text-taupe font-normal" x-text="form.type === 'percentage' ? '(%)' : '(৳)'"></span>
                                    </label>
                                    <input type="number" x-model="form.value" min="0" step="0.01"
                                        :class="errors.value ? 'border-red-400' : 'border-champagne'"
                                        placeholder="e.g. 10"
                                        class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                                    <p class="text-[10px] text-taupe mt-0.5">Use 0 for benefit-only (free delivery / gift) coupons</p>
                                    <p x-show="errors.value" class="text-xs text-red-500 mt-1" x-text="errors.value?.[0]"></p>
                                </div>
                            </div>

                            {{-- Min purchase + Usage limits --}}
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-muted mb-1">Min Purchase (৳)</label>
                                    <input type="number" x-model="form.min_purchase" min="0" step="0.01" placeholder="None"
                                        class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-muted mb-1">Total Usage Limit</label>
                                    <input type="number" x-model="form.usage_limit" min="1" placeholder="Unlimited"
                                        class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-muted mb-1">Per User Limit</label>
                                    <input type="number" x-model="form.limit_per_user" min="1" placeholder="∞"
                                        class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                                    <p class="text-[10px] text-taupe mt-0.5">Leave blank for unlimited</p>
                                </div>
                            </div>

                            {{-- Validity dates --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-muted mb-1">Start Date</label>
                                    <input type="datetime-local" x-model="form.start_date"
                                        class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                                    <p x-show="errors.start_date" class="text-xs text-red-500 mt-1" x-text="errors.start_date?.[0]"></p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-muted mb-1">End Date</label>
                                    <input type="datetime-local" x-model="form.end_date"
                                        class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                                    <p x-show="errors.end_date" class="text-xs text-red-500 mt-1" x-text="errors.end_date?.[0]"></p>
                                </div>
                            </div>

                            {{-- Active toggle --}}
                            <div class="flex items-center gap-3">
                                <button type="button" @click="form.is_active = !form.is_active"
                                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none"
                                    :class="form.is_active ? 'bg-primary' : 'bg-gray-300'">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="form.is_active ? 'translate-x-4' : 'translate-x-0'"></span>
                                </button>
                                <span class="text-sm text-brown" x-text="form.is_active ? 'Active' : 'Inactive'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- ─── SCOPE ──────────────────────────────────────────────── --}}
                    <div class="border-t border-champagne pt-5">
                        <p class="text-xs font-bold text-muted uppercase tracking-wider mb-3">Applies To</p>
                        <div class="space-y-3">

                            {{-- Scope selector --}}
                            <div class="grid grid-cols-3 gap-2">
                                <template x-for="opt in [{v:'all',label:'All items',icon:'fa-globe'},{v:'products',label:'Specific variants',icon:'fa-box'},{v:'combos',label:'Specific combos',icon:'fa-cubes'}]" :key="opt.v">
                                    <label class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition"
                                        :class="form.applies_to === opt.v ? 'border-gold-antique bg-cream' : 'border-champagne hover:border-gray-300'">
                                        <input type="radio" :value="opt.v" x-model="form.applies_to" class="hidden">
                                        <i class="fa-solid text-xs" :class="opt.icon + (form.applies_to === opt.v ? ' text-gold-antique' : ' text-taupe')"></i>
                                        <span class="text-xs font-medium" :class="form.applies_to === opt.v ? 'text-brown' : 'text-muted'" x-text="opt.label"></span>
                                    </label>
                                </template>
                            </div>

                            {{-- Variant picker (applies_to = products) --}}
                            <div x-show="form.applies_to === 'products'" x-cloak class="space-y-2">
                                <p class="text-xs text-muted">Search and select product variants this coupon applies to:</p>

                                {{-- Search input --}}
                                <div class="relative">
                                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-taupe text-xs"></i>
                                    <input type="text" x-model="variantSearch"
                                        @input.debounce.300ms="searchVariants()"
                                        @focus="showVariantResults = true"
                                        @click.outside="showVariantResults = false"
                                        placeholder="Search by name or SKU…"
                                        class="w-full pl-9 pr-4 py-2 text-sm border border-champagne rounded-lg outline-none focus:ring-2 focus:ring-gold-antique">

                                    {{-- Dropdown results --}}
                                    <div x-show="showVariantResults && variantResults.length > 0"
                                        class="absolute z-20 w-full mt-1 bg-white border border-champagne rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        <template x-for="v in variantResults" :key="v.id">
                                            <button type="button"
                                                @click="toggleVariantScope(v); showVariantResults = false; variantSearch = '';"
                                                class="w-full flex items-center justify-between px-3 py-2 text-left text-xs hover:bg-cream transition">
                                                <div>
                                                    <span class="font-medium text-brown" x-text="v.label"></span>
                                                    <span class="text-taupe ml-2" x-text="v.sku ? '· ' + v.sku : ''"></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-taupe" x-text="'৳' + v.final_price"></span>
                                                    <i x-show="isVariantSelected(v.id)" class="fa-solid fa-check text-gold-antique"></i>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                {{-- Selected variant chips --}}
                                <div x-show="form.variant_scopes.length > 0" class="flex flex-wrap gap-2">
                                    <template x-for="sv in form.variant_scopes" :key="sv.id">
                                        <span class="inline-flex items-center gap-1.5 bg-blue-50 border border-blue-200 text-blue-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                            <span x-text="sv.label"></span>
                                            <button type="button" @click="removeVariantScope(sv.id)"
                                                class="text-blue-400 hover:text-blue-700 cursor-pointer leading-none">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                            </button>
                                        </span>
                                    </template>
                                </div>
                                <p x-show="errors.variant_ids" class="text-xs text-red-500" x-text="errors.variant_ids?.[0]"></p>
                            </div>

                            {{-- Combo picker (applies_to = combos) --}}
                            <div x-show="form.applies_to === 'combos'" x-cloak class="space-y-2">
                                <p class="text-xs text-muted">Search and select combos this coupon applies to:</p>

                                <div class="relative">
                                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-taupe text-xs"></i>
                                    <input type="text" x-model="comboSearch"
                                        @input.debounce.300ms="searchCombos()"
                                        @focus="showComboResults = true"
                                        @click.outside="showComboResults = false"
                                        placeholder="Search combos…"
                                        class="w-full pl-9 pr-4 py-2 text-sm border border-champagne rounded-lg outline-none focus:ring-2 focus:ring-gold-antique">

                                    <div x-show="showComboResults && comboResults.length > 0"
                                        class="absolute z-20 w-full mt-1 bg-white border border-champagne rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        <template x-for="cb in comboResults" :key="cb.id">
                                            <button type="button"
                                                @click="toggleComboScope(cb); showComboResults = false; comboSearch = '';"
                                                class="w-full flex items-center justify-between px-3 py-2 text-left text-xs hover:bg-cream transition">
                                                <span class="font-medium text-brown" x-text="cb.label ?? cb.name"></span>
                                                <i x-show="isComboSelected(cb.id)" class="fa-solid fa-check text-gold-antique"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div x-show="form.combo_scopes.length > 0" class="flex flex-wrap gap-2">
                                    <template x-for="sc in form.combo_scopes" :key="sc.id">
                                        <span class="inline-flex items-center gap-1.5 bg-purple-50 border border-purple-200 text-purple-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                            <span x-text="sc.label"></span>
                                            <button type="button" @click="removeComboScope(sc.id)"
                                                class="text-purple-400 hover:text-purple-700 cursor-pointer leading-none">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                            </button>
                                        </span>
                                    </template>
                                </div>
                                <p x-show="errors.combo_ids" class="text-xs text-red-500" x-text="errors.combo_ids?.[0]"></p>
                            </div>
                        </div>
                    </div>

                    {{-- ─── BENEFITS ───────────────────────────────────────────── --}}
                    <div class="border-t border-champagne pt-5">
                        <p class="text-xs font-bold text-muted uppercase tracking-wider mb-3">Benefits</p>
                        <div class="space-y-4">

                            {{-- Free Delivery toggle --}}
                            <div class="flex items-start gap-3 p-3 rounded-xl border border-champagne">
                                <button type="button" @click="form.is_free_delivery = !form.is_free_delivery"
                                    class="mt-0.5 relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none"
                                    :class="form.is_free_delivery ? 'bg-emerald-500' : 'bg-gray-300'">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="form.is_free_delivery ? 'translate-x-4' : 'translate-x-0'"></span>
                                </button>
                                <div>
                                    <p class="text-sm font-semibold text-brown flex items-center gap-2">
                                        <i class="fa-solid fa-truck-fast text-emerald-600 text-xs"></i>
                                        Free Delivery
                                    </p>
                                    <p class="text-xs text-muted mt-0.5">Waive shipping cost when this coupon is applied. Works on top of any discount.</p>
                                </div>
                            </div>

                            {{-- Gift Item --}}
                            <div class="p-3 rounded-xl border border-champagne space-y-3">
                                <div class="flex items-start gap-3">
                                    <button type="button" @click="toggleGiftSection()"
                                        class="mt-0.5 relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none"
                                        :class="showGiftSection ? 'bg-pink-500' : 'bg-gray-300'">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="showGiftSection ? 'translate-x-4' : 'translate-x-0'"></span>
                                    </button>
                                    <div>
                                        <p class="text-sm font-semibold text-brown flex items-center gap-2">
                                            <i class="fa-solid fa-gift text-pink-600 text-xs"></i>
                                            Gift Item
                                        </p>
                                        <p class="text-xs text-muted mt-0.5">Add a free product variant to the order when this coupon is used.</p>
                                    </div>
                                </div>

                                {{-- Gift variant picker --}}
                                <div x-show="showGiftSection" x-cloak class="space-y-2 pt-1">
                                    {{-- Selected gift --}}
                                    <template x-if="form.gift_variant">
                                        <div class="flex items-center justify-between bg-pink-50 border border-pink-200 rounded-lg px-3 py-2">
                                            <div>
                                                <p class="text-xs font-semibold text-pink-800" x-text="form.gift_variant.label"></p>
                                                <p class="text-[10px] text-pink-500" x-text="form.gift_variant.sku ? 'SKU: ' + form.gift_variant.sku : ''"></p>
                                            </div>
                                            <button type="button" @click="clearGiftVariant()" class="text-pink-400 hover:text-pink-700 cursor-pointer text-xs">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    </template>

                                    {{-- Gift search --}}
                                    <template x-if="!form.gift_variant">
                                        <div class="relative">
                                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-taupe text-xs"></i>
                                            <input type="text" x-model="giftSearch"
                                                @input.debounce.300ms="searchGiftVariants()"
                                                @focus="showGiftResults = true"
                                                @click.outside="showGiftResults = false"
                                                placeholder="Search variant to gift…"
                                                class="w-full pl-9 pr-4 py-2 text-sm border border-champagne rounded-lg outline-none focus:ring-2 focus:ring-pink-300">
                                            <div x-show="showGiftResults && giftResults.length > 0"
                                                class="absolute z-20 w-full mt-1 bg-white border border-champagne rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                                <template x-for="gv in giftResults" :key="gv.id">
                                                    <button type="button"
                                                        @click="selectGiftVariant(gv); showGiftResults = false; giftSearch = '';"
                                                        class="w-full flex items-center justify-between px-3 py-2 text-left text-xs hover:bg-cream transition">
                                                        <div>
                                                            <span class="font-medium text-brown" x-text="gv.label"></span>
                                                            <span class="text-taupe ml-2" x-text="gv.sku ? '· ' + gv.sku : ''"></span>
                                                        </div>
                                                        <span class="text-taupe" x-text="'৳' + gv.final_price"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Gift quantity --}}
                                    <div class="flex items-center gap-3">
                                        <label class="text-xs font-semibold text-muted whitespace-nowrap">Gift Qty:</label>
                                        <input type="number" x-model="form.gift_quantity" min="1" max="100"
                                            class="w-24 border border-champagne rounded-lg px-3 py-1.5 text-sm outline-none focus:ring-2 focus:ring-pink-300">
                                    </div>
                                    <p x-show="errors.gift_product_variant_id" class="text-xs text-red-500" x-text="errors.gift_product_variant_id?.[0]"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-2 border-t border-champagne">
                        <button type="button" @click="showFormModal = false"
                            class="px-4 py-2 text-sm font-medium text-muted border border-champagne rounded-lg hover:bg-cream transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" :disabled="saving"
                            class="px-5 py-2 text-sm font-semibold bg-gold-antique text-white rounded-lg hover:bg-gold-antique disabled:opacity-50 transition cursor-pointer disabled:cursor-not-allowed">
                            <span x-text="saving ? 'Saving…' : (editingId ? 'Update' : 'Create')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- DETAIL / VIEW MODAL                                          --}}
        {{-- ============================================================ --}}
        <div x-show="showDetailModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @keydown.escape.window="showDetailModal = false">

            <div @click.outside="showDetailModal = false"
                class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between px-6 py-4 border-b border-champagne sticky top-0 bg-white z-10">
                    <div>
                        <h3 class="font-bold text-brown font-mono" x-text="detailCoupon?.code"></h3>
                        <p class="text-xs text-muted mt-0.5">Coupon Details</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @can('coupon.update')
                            <button @click="showDetailModal = false; openEditModal(detailCoupon)"
                                class="text-xs text-blue-600 hover:text-blue-800 font-semibold cursor-pointer">
                                <i class="fa-solid fa-pen mr-1"></i> Edit
                            </button>
                        @endcan
                        <button @click="showDetailModal = false" class="text-taupe hover:text-brown cursor-pointer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div x-show="detailLoading" class="p-10 text-center text-muted text-sm">
                    <i class="fa-solid fa-spinner fa-spin text-xl mb-2 block"></i> Loading…
                </div>

                <div x-show="!detailLoading && detailCoupon" class="px-6 py-5 space-y-5">

                    {{-- Summary grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="bg-cream rounded-xl p-3">
                            <p class="text-xs text-muted mb-0.5">Discount</p>
                            <p class="font-bold text-brown text-sm"
                                x-text="detailCoupon?.value > 0 ? (detailCoupon.type === 'percentage' ? detailCoupon.value + '%' : '৳' + detailCoupon.value) + (detailCoupon.type === 'percentage' ? ' off' : ' flat') : '—'">
                            </p>
                        </div>
                        <div class="bg-cream rounded-xl p-3">
                            <p class="text-xs text-muted mb-0.5">Min Purchase</p>
                            <p class="font-bold text-brown text-sm"
                                x-text="detailCoupon?.min_purchase ? '৳' + Number(detailCoupon.min_purchase).toLocaleString() : 'None'"></p>
                        </div>
                        <div class="bg-cream rounded-xl p-3">
                            <p class="text-xs text-muted mb-0.5">Status</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                :class="statusBadge(detailCoupon)">
                                <span x-text="statusLabel(detailCoupon)"></span>
                            </span>
                        </div>
                        <div class="bg-cream rounded-xl p-3">
                            <p class="text-xs text-muted mb-0.5">Usage</p>
                            <p class="font-bold text-brown text-sm">
                                <span x-text="detailCoupon?.used_count ?? 0"></span>
                                <span class="text-taupe font-normal" x-text="detailCoupon?.usage_limit ? ' / ' + detailCoupon.usage_limit : ' / ∞'"></span>
                            </p>
                        </div>
                        <div class="bg-cream rounded-xl p-3">
                            <p class="text-xs text-muted mb-0.5">Per User</p>
                            <p class="font-bold text-brown text-sm" x-text="detailCoupon?.limit_per_user ?? '∞'"></p>
                        </div>
                        <div class="bg-cream rounded-xl p-3">
                            <p class="text-xs text-muted mb-0.5">Total Saved</p>
                            <p class="font-bold text-indigo-700 text-sm">
                                ৳<span x-text="Number(detailCoupon?.total_discount ?? 0).toLocaleString()"></span>
                            </p>
                        </div>
                    </div>

                    {{-- Validity --}}
                    <div x-show="detailCoupon?.start_date || detailCoupon?.end_date" class="flex items-center gap-2 text-sm text-muted">
                        <i class="fa-regular fa-calendar text-xs"></i>
                        <span x-text="detailCoupon?.start_date ? fmtDate(detailCoupon.start_date) : '∞'"></span>
                        <span>→</span>
                        <span x-text="detailCoupon?.end_date ? fmtDate(detailCoupon.end_date) : '∞'"></span>
                    </div>

                    {{-- Scope --}}
                    <div>
                        <p class="text-xs font-bold text-muted uppercase tracking-wider mb-2">Scope</p>
                        <template x-if="!detailCoupon?.applies_to || detailCoupon.applies_to === 'all'">
                            <p class="text-sm text-muted"><i class="fa-solid fa-globe mr-2 text-gray-400"></i>Applies to all items in cart</p>
                        </template>
                        <template x-if="detailCoupon?.applies_to === 'products'">
                            <div>
                                <p class="text-xs text-muted mb-2"><i class="fa-solid fa-box mr-1 text-blue-400"></i>Applies to specific product variants only</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-if="detailCoupon?.product_variant_scopes?.length > 0">
                                        <template x-for="sv in detailCoupon.product_variant_scopes" :key="sv.id">
                                            <span class="inline-flex items-center bg-blue-50 border border-blue-200 text-blue-800 text-xs font-medium px-2.5 py-1 rounded-full" x-text="sv.label"></span>
                                        </template>
                                    </template>
                                    <template x-if="!detailCoupon?.product_variant_scopes?.length">
                                        <span class="text-xs text-taupe italic">No variants specified</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="detailCoupon?.applies_to === 'combos'">
                            <div>
                                <p class="text-xs text-muted mb-2"><i class="fa-solid fa-cubes mr-1 text-purple-400"></i>Applies to specific combos only</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-if="detailCoupon?.combo_scopes?.length > 0">
                                        <template x-for="sc in detailCoupon.combo_scopes" :key="sc.id">
                                            <span class="inline-flex items-center bg-purple-50 border border-purple-200 text-purple-800 text-xs font-medium px-2.5 py-1 rounded-full" x-text="sc.label"></span>
                                        </template>
                                    </template>
                                    <template x-if="!detailCoupon?.combo_scopes?.length">
                                        <span class="text-xs text-taupe italic">No combos specified</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Benefits --}}
                    <div>
                        <p class="text-xs font-bold text-muted uppercase tracking-wider mb-2">Benefits</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-if="detailCoupon?.is_free_delivery">
                                <span class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold px-3 py-1.5 rounded-full">
                                    <i class="fa-solid fa-truck-fast text-emerald-600"></i> Free Delivery
                                </span>
                            </template>
                            <template x-if="detailCoupon?.gift_variant">
                                <span class="inline-flex items-center gap-2 bg-pink-50 border border-pink-200 text-pink-800 text-xs font-semibold px-3 py-1.5 rounded-full">
                                    <i class="fa-solid fa-gift text-pink-600"></i>
                                    Gift: <span x-text="detailCoupon.gift_variant.label"></span>
                                    × <span x-text="detailCoupon.gift_quantity"></span>
                                </span>
                            </template>
                            <template x-if="!detailCoupon?.is_free_delivery && !detailCoupon?.gift_variant">
                                <span class="text-xs text-taupe">No extra benefits</span>
                            </template>
                        </div>
                    </div>

                    {{-- Usage history --}}
                    <div>
                        <p class="text-xs font-bold text-muted uppercase tracking-wider mb-2">Recent Usage</p>
                        <template x-if="detailCoupon?.recent_usages?.length > 0">
                            <div class="rounded-xl border border-champagne overflow-hidden">
                                <table class="w-full text-xs">
                                    <thead class="bg-cream border-b border-champagne">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-muted font-semibold">Customer</th>
                                            <th class="px-4 py-2 text-left text-muted font-semibold">Order</th>
                                            <th class="px-4 py-2 text-left text-muted font-semibold">Discount</th>
                                            <th class="px-4 py-2 text-left text-muted font-semibold">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <template x-for="u in detailCoupon.recent_usages" :key="u.id">
                                            <tr>
                                                <td class="px-4 py-2">
                                                    <span x-text="u.user?.name ?? 'Guest'"></span>
                                                    <span x-show="u.user?.phone" class="text-taupe ml-1" x-text="'· ' + u.user?.phone"></span>
                                                </td>
                                                <td class="px-4 py-2 font-mono text-brown" x-text="u.order_number ?? '—'"></td>
                                                <td class="px-4 py-2 font-semibold text-indigo-700">৳<span x-text="Number(u.discount_amount).toLocaleString()"></span></td>
                                                <td class="px-4 py-2 text-taupe" x-text="u.created_at ? fmtDate(u.created_at) : '—'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                        <template x-if="!detailCoupon?.recent_usages?.length">
                            <p class="text-xs text-taupe italic">Not yet used</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- BULK GENERATE MODAL                                          --}}
        {{-- ============================================================ --}}
        <div x-show="showBulkModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @keydown.escape.window="showBulkModal = false">

            <div @click.outside="showBulkModal = false"
                class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between px-6 py-4 border-b border-champagne">
                    <h3 class="font-bold text-brown">Bulk Generate Coupons</h3>
                    <button @click="showBulkModal = false" class="text-taupe hover:text-brown cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <template x-if="generatedCodes.length > 0">
                    <div class="px-6 py-4 border-b border-champagne bg-ivory">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold text-gold-antique">
                                <i class="fa-solid fa-check-circle mr-1"></i>
                                <span x-text="generatedCodes.length"></span> coupons generated!
                            </p>
                            <button @click="copyAllCodes()" class="text-xs text-gold-antique hover:underline cursor-pointer font-medium">Copy all</button>
                        </div>
                        <div class="max-h-40 overflow-y-auto rounded-lg bg-white border border-sand p-3">
                            <div class="grid grid-cols-2 gap-1">
                                <template x-for="code in generatedCodes" :key="code">
                                    <span class="font-mono text-xs text-brown bg-cream px-2 py-0.5 rounded" x-text="code"></span>
                                </template>
                            </div>
                        </div>
                        <button @click="generatedCodes = []; resetBulkForm()"
                            class="mt-3 text-xs text-gold-antique hover:underline cursor-pointer">Generate more</button>
                    </div>
                </template>

                <form x-show="generatedCodes.length === 0" @submit.prevent="doBulkGenerate()" class="px-6 py-5 space-y-4">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Prefix <span class="text-red-500">*</span></label>
                            <input type="text" x-model="bulkForm.prefix" placeholder="e.g. SALE"
                                :class="bulkErrors.prefix ? 'border-red-400' : 'border-champagne'"
                                class="w-full border rounded-lg px-3 py-2 text-sm uppercase tracking-wide outline-none focus:ring-2 focus:ring-gold-antique">
                            <p x-show="bulkErrors.prefix" class="text-xs text-red-500 mt-1" x-text="bulkErrors.prefix?.[0]"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Count <span class="text-red-500">*</span> <span class="text-taupe font-normal">(max 500)</span></label>
                            <input type="number" x-model="bulkForm.count" min="1" max="500" placeholder="10"
                                :class="bulkErrors.count ? 'border-red-400' : 'border-champagne'"
                                class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                            <p x-show="bulkErrors.count" class="text-xs text-red-500 mt-1" x-text="bulkErrors.count?.[0]"></p>
                        </div>
                    </div>
                    <p class="text-xs text-taupe -mt-2">Codes will look like: <span class="font-mono font-semibold text-muted"
                            x-text="(bulkForm.prefix || 'SALE').toUpperCase() + 'XXXXXXXX'"></span></p>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Type <span class="text-red-500">*</span></label>
                            <select x-model="bulkForm.type"
                                class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique cursor-pointer">
                                <option value="fixed">Fixed (৳)</option>
                                <option value="percentage">Percentage (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Value <span class="text-red-500">*</span>
                                <span class="text-taupe font-normal" x-text="bulkForm.type === 'percentage' ? '(%)' : '(৳)'"></span>
                            </label>
                            <input type="number" x-model="bulkForm.value" min="0" step="0.01"
                                :class="bulkErrors.value ? 'border-red-400' : 'border-champagne'"
                                class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                            <p x-show="bulkErrors.value" class="text-xs text-red-500 mt-1" x-text="bulkErrors.value?.[0]"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Min Purchase (৳)</label>
                            <input type="number" x-model="bulkForm.min_purchase" min="0" placeholder="None"
                                class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Usage Limit / Code</label>
                            <input type="number" x-model="bulkForm.usage_limit" min="1" placeholder="∞"
                                class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Per User Limit</label>
                            <input type="number" x-model="bulkForm.limit_per_user" min="1" placeholder="∞"
                                class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                            <p class="text-[10px] text-taupe mt-0.5">Leave blank for unlimited</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">Start Date</label>
                            <input type="datetime-local" x-model="bulkForm.start_date"
                                class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1">End Date</label>
                            <input type="datetime-local" x-model="bulkForm.end_date"
                                class="w-full border border-champagne rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="bulkForm.is_active = !bulkForm.is_active"
                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors"
                            :class="bulkForm.is_active ? 'bg-primary' : 'bg-gray-300'">
                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition"
                                :class="bulkForm.is_active ? 'translate-x-4' : 'translate-x-0'"></span>
                        </button>
                        <span class="text-sm text-brown" x-text="bulkForm.is_active ? 'Active immediately' : 'Inactive'"></span>
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-champagne mt-4">
                        <button type="button" @click="showBulkModal = false"
                            class="px-4 py-2 text-sm font-medium text-muted border border-champagne rounded-lg hover:bg-cream transition cursor-pointer">Cancel</button>
                        <button type="submit" :disabled="bulkSaving"
                            class="px-5 py-2 text-sm font-semibold bg-gold-antique text-white rounded-lg hover:bg-gold-antique disabled:opacity-50 transition cursor-pointer disabled:cursor-not-allowed">
                            <span x-text="bulkSaving ? 'Generating…' : 'Generate'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- DELETE CONFIRM MODAL                                         --}}
        {{-- ============================================================ --}}
        <div x-show="showDeleteModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @keydown.escape.window="showDeleteModal = false">

            <div @click.outside="showDeleteModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-trash text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-brown">Delete Coupon</h3>
                        <p class="text-xs text-muted">This action cannot be undone.</p>
                    </div>
                </div>
                <p class="text-sm text-brown mb-5">
                    Delete coupon <span class="font-mono font-bold" x-text="deleteTarget?.code"></span>?
                    <span x-show="deleteTarget?.usages_count > 0" class="text-yellow-600 block mt-1 text-xs">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        This coupon has been used <span x-text="deleteTarget?.usages_count"></span> time(s).
                    </span>
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="showDeleteModal = false"
                        class="px-4 py-2 text-sm font-medium text-muted border border-champagne rounded-lg hover:bg-cream transition cursor-pointer">Cancel</button>
                    <button @click="doDelete()" :disabled="deleting"
                        class="px-5 py-2 text-sm font-semibold bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 transition cursor-pointer disabled:cursor-not-allowed">
                        <span x-text="deleting ? 'Deleting…' : 'Delete'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        function couponManager() {
            const emptyForm = () => ({
                code: '',
                type: 'fixed',
                value: '',
                min_purchase: '',
                usage_limit: '',
                limit_per_user: 1,
                start_date: '',
                end_date: '',
                is_active: true,
                // Scope
                applies_to: 'all',
                variant_scopes: [],   // [{id, label, sku}]
                combo_scopes: [],     // [{id, label}]
                // Benefits
                is_free_delivery: false,
                gift_variant: null,   // {id, label, sku}
                gift_quantity: 1,
                gift_product_variant_id: null,
            });

            const emptyBulk = () => ({
                prefix: '',
                count: 10,
                type: 'fixed',
                value: '',
                min_purchase: '',
                usage_limit: '',
                limit_per_user: 1,
                start_date: '',
                end_date: '',
                is_active: true,
            });

            return {
                coupons: [],
                meta: {},
                loading: true,
                stats: {},
                search: '',
                filterStatus: '',

                // form modal
                showFormModal: false,
                editingId: null,
                form: emptyForm(),
                errors: {},
                saving: false,

                // scope pickers
                variantSearch: '',
                variantResults: [],
                showVariantResults: false,
                comboSearch: '',
                comboResults: [],
                showComboResults: false,

                // gift section
                showGiftSection: false,
                giftSearch: '',
                giftResults: [],
                showGiftResults: false,

                // detail modal
                showDetailModal: false,
                detailCoupon: null,
                detailLoading: false,

                // bulk modal
                showBulkModal: false,
                bulkForm: emptyBulk(),
                bulkErrors: {},
                bulkSaving: false,
                generatedCodes: [],

                // delete modal
                showDeleteModal: false,
                deleteTarget: null,
                deleting: false,

                async init() {
                    await Promise.all([this.load(), this.loadStats()]);
                },

                // ── Load list ──────────────────────────────────────────────────
                async load(page = 1) {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({ page });
                        if (this.search) params.set('q', this.search);
                        if (this.filterStatus) params.set('status', this.filterStatus);

                        const r = await fetch(`/api/v1/admin/coupons?${params}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await r.json();
                        this.coupons = data.data ?? [];
                        this.meta = data.meta ?? {};
                    } catch (e) {
                        console.error('Failed to load coupons', e);
                    } finally {
                        this.loading = false;
                    }
                },

                async loadStats() {
                    try {
                        const r = await fetch('/api/v1/admin/coupons/stats', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await r.json();
                        this.stats = data.data ?? {};
                    } catch (e) {}
                },

                // ── Detail modal ───────────────────────────────────────────────
                async openDetailModal(coupon) {
                    this.detailCoupon = coupon;
                    this.showDetailModal = true;
                    this.detailLoading = true;
                    try {
                        const r = await fetch(`/api/v1/admin/coupons/${coupon.id}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await r.json();
                        this.detailCoupon = data.data;
                    } catch (e) {
                        console.error('Failed to load coupon detail', e);
                    } finally {
                        this.detailLoading = false;
                    }
                },

                // ── Create modal ───────────────────────────────────────────────
                openCreateModal() {
                    this.editingId = null;
                    this.form = emptyForm();
                    this.errors = {};
                    this.variantSearch = '';
                    this.variantResults = [];
                    this.comboSearch = '';
                    this.comboResults = [];
                    this.showGiftSection = false;
                    this.giftSearch = '';
                    this.giftResults = [];
                    this.showFormModal = true;
                },

                // ── Edit modal (fetches full detail first) ─────────────────────
                async openEditModal(coupon) {
                    this.editingId = coupon.id;
                    this.errors = {};
                    this.variantSearch = '';
                    this.variantResults = [];
                    this.comboSearch = '';
                    this.comboResults = [];
                    this.giftSearch = '';
                    this.giftResults = [];

                    // Show modal immediately with basic data while detail loads
                    this.form = {
                        ...emptyForm(),
                        code: coupon.code,
                        type: coupon.type,
                        value: coupon.value,
                        min_purchase: coupon.min_purchase ?? '',
                        usage_limit: coupon.usage_limit ?? '',
                        limit_per_user: coupon.limit_per_user ?? '',
                        start_date: coupon.start_date ? this.toDatetimeLocal(coupon.start_date) : '',
                        end_date: coupon.end_date ? this.toDatetimeLocal(coupon.end_date) : '',
                        is_active: coupon.is_active,
                        applies_to: coupon.applies_to ?? 'all',
                        is_free_delivery: coupon.is_free_delivery ?? false,
                        gift_quantity: coupon.gift_quantity ?? 1,
                        gift_product_variant_id: coupon.gift_product_variant_id ?? null,
                        variant_scopes: [],
                        combo_scopes: [],
                        gift_variant: null,
                    };
                    this.showGiftSection = !!coupon.gift_product_variant_id;
                    this.showFormModal = true;

                    // Fetch full detail to get scope labels
                    try {
                        const r = await fetch(`/api/v1/admin/coupons/${coupon.id}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await r.json();
                        const full = data.data;

                        // Populate scope chips with labels
                        if (full.product_variant_scopes) {
                            this.form.variant_scopes = full.product_variant_scopes.map(sv => ({
                                id: sv.id, label: sv.label, sku: sv.sku ?? ''
                            }));
                        }
                        if (full.combo_scopes) {
                            this.form.combo_scopes = full.combo_scopes.map(sc => ({
                                id: sc.id, label: sc.label
                            }));
                        }
                        if (full.gift_variant) {
                            this.form.gift_variant = full.gift_variant;
                        }
                    } catch (e) {
                        console.error('Failed to load coupon detail for edit', e);
                    }
                },

                // ── Save (create or update) ────────────────────────────────────
                async saveCoupon() {
                    this.saving = true;
                    this.errors = {};
                    try {
                        const url = this.editingId ?
                            `/api/v1/admin/coupons/${this.editingId}` :
                            `/api/v1/admin/coupons`;
                        const method = this.editingId ? 'PUT' : 'POST';

                        const payload = { ...this.form };
                        // Nullable fields: convert empty string to null
                        ['min_purchase', 'usage_limit', 'limit_per_user', 'start_date', 'end_date'].forEach(k => {
                            if (payload[k] === '' || payload[k] === null) payload[k] = null;
                        });
                        if (!this.editingId) payload.code = payload.code.toUpperCase();

                        // Convert scope objects to ID arrays for the API
                        payload.variant_ids = this.form.variant_scopes.map(sv => sv.id);
                        payload.combo_ids   = this.form.combo_scopes.map(sc => sc.id);

                        // Gift variant id
                        payload.gift_product_variant_id = this.form.gift_variant?.id ?? null;
                        if (!this.showGiftSection) {
                            payload.gift_product_variant_id = null;
                            payload.gift_quantity = 1;
                        }

                        // Remove UI-only fields
                        delete payload.variant_scopes;
                        delete payload.combo_scopes;
                        delete payload.gift_variant;

                        const r = await fetch(url, {
                            method,
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await r.json();

                        if (r.status === 422) {
                            this.errors = data.errors ?? {};
                            return;
                        }
                        if (!r.ok) {
                            window.flash?.(data.message ?? 'An error occurred', 'error');
                            return;
                        }
                        window.flash?.(this.editingId ? 'Coupon updated' : 'Coupon created', 'success');
                        this.showFormModal = false;
                        await this.load(this.meta.current_page ?? 1);
                        await this.loadStats();
                    } finally {
                        this.saving = false;
                    }
                },

                // ── Scope: variant picker ──────────────────────────────────────
                async searchVariants() {
                    if (this.variantSearch.length < 2) {
                        this.variantResults = [];
                        return;
                    }
                    try {
                        const r = await fetch(`/api/v1/admin/products/variants/search?q=${encodeURIComponent(this.variantSearch)}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await r.json();
                        this.variantResults = data.data ?? [];
                        this.showVariantResults = true;
                    } catch (e) {}
                },

                toggleVariantScope(v) {
                    if (this.isVariantSelected(v.id)) {
                        this.form.variant_scopes = this.form.variant_scopes.filter(sv => sv.id !== v.id);
                    } else {
                        this.form.variant_scopes.push({ id: v.id, label: v.label, sku: v.sku ?? '' });
                    }
                },

                removeVariantScope(id) {
                    this.form.variant_scopes = this.form.variant_scopes.filter(sv => sv.id !== id);
                },

                isVariantSelected(id) {
                    return this.form.variant_scopes.some(sv => sv.id === id);
                },

                // ── Scope: combo picker ────────────────────────────────────────
                async searchCombos() {
                    if (this.comboSearch.length < 2) {
                        this.comboResults = [];
                        return;
                    }
                    try {
                        const r = await fetch(`/api/v1/admin/combos/search?search=${encodeURIComponent(this.comboSearch)}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await r.json();
                        // searchCombos returns {id, name, image} — normalise to {id, label}
                        this.comboResults = (data.data ?? []).map(c => ({ id: c.id, label: c.name ?? c.title }));
                        this.showComboResults = true;
                    } catch (e) {}
                },

                toggleComboScope(cb) {
                    if (this.isComboSelected(cb.id)) {
                        this.form.combo_scopes = this.form.combo_scopes.filter(sc => sc.id !== cb.id);
                    } else {
                        this.form.combo_scopes.push({ id: cb.id, label: cb.label });
                    }
                },

                removeComboScope(id) {
                    this.form.combo_scopes = this.form.combo_scopes.filter(sc => sc.id !== id);
                },

                isComboSelected(id) {
                    return this.form.combo_scopes.some(sc => sc.id === id);
                },

                // ── Benefits: gift variant picker ──────────────────────────────
                toggleGiftSection() {
                    this.showGiftSection = !this.showGiftSection;
                    if (!this.showGiftSection) {
                        this.form.gift_variant = null;
                        this.form.gift_product_variant_id = null;
                        this.form.gift_quantity = 1;
                        this.giftSearch = '';
                        this.giftResults = [];
                    }
                },

                async searchGiftVariants() {
                    if (this.giftSearch.length < 2) {
                        this.giftResults = [];
                        return;
                    }
                    try {
                        const r = await fetch(`/api/v1/admin/products/variants/search?q=${encodeURIComponent(this.giftSearch)}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await r.json();
                        this.giftResults = data.data ?? [];
                        this.showGiftResults = true;
                    } catch (e) {}
                },

                selectGiftVariant(gv) {
                    this.form.gift_variant = gv;
                    this.form.gift_product_variant_id = gv.id;
                },

                clearGiftVariant() {
                    this.form.gift_variant = null;
                    this.form.gift_product_variant_id = null;
                },

                // ── Bulk generate ──────────────────────────────────────────────
                openBulkModal() {
                    this.generatedCodes = [];
                    this.resetBulkForm();
                    this.showBulkModal = true;
                },

                resetBulkForm() {
                    this.bulkForm = emptyBulk();
                    this.bulkErrors = {};
                },

                async doBulkGenerate() {
                    this.bulkSaving = true;
                    this.bulkErrors = {};
                    try {
                        const payload = { ...this.bulkForm };
                        ['min_purchase', 'usage_limit', 'limit_per_user', 'start_date', 'end_date'].forEach(k => {
                            if (payload[k] === '') payload[k] = null;
                        });

                        const r = await fetch('/api/v1/admin/coupons/bulk-generate', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await r.json();

                        if (r.status === 422) {
                            this.bulkErrors = data.errors ?? {};
                            return;
                        }
                        if (!r.ok) {
                            window.flash?.(data.message ?? 'An error occurred', 'error');
                            return;
                        }

                        this.generatedCodes = data.data?.codes ?? [];
                        await this.load(1);
                        await this.loadStats();
                    } finally {
                        this.bulkSaving = false;
                    }
                },

                async copyAllCodes() {
                    await navigator.clipboard.writeText(this.generatedCodes.join('\n'));
                    window.flash?.('All codes copied!', 'success');
                },

                // ── Delete ─────────────────────────────────────────────────────
                confirmDelete(coupon) {
                    this.deleteTarget = coupon;
                    this.showDeleteModal = true;
                },

                async doDelete() {
                    this.deleting = true;
                    try {
                        const r = await fetch(`/api/v1/admin/coupons/${this.deleteTarget.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });
                        if (r.ok) {
                            window.flash?.(`Coupon "${this.deleteTarget.code}" deleted`, 'success');
                            this.showDeleteModal = false;
                            await this.load(this.meta.current_page ?? 1);
                            await this.loadStats();
                        }
                    } finally {
                        this.deleting = false;
                    }
                },

                // ── Helpers ────────────────────────────────────────────────────
                async copyCode(code) {
                    await navigator.clipboard.writeText(code);
                    window.flash?.(`"${code}" copied`, 'success');
                },

                statusLabel(c) {
                    if (!c) return '';
                    if (!c.is_active) return 'Inactive';
                    if (c.end_date && new Date(c.end_date) < new Date()) return 'Expired';
                    if (c.start_date && new Date(c.start_date) > new Date()) return 'Scheduled';
                    return 'Active';
                },

                statusBadge(c) {
                    const label = this.statusLabel(c);
                    if (label === 'Active') return 'bg-cream text-gold-antique';
                    if (label === 'Expired') return 'bg-red-100 text-red-800';
                    if (label === 'Scheduled') return 'bg-blue-100 text-blue-800';
                    return 'bg-gray-100 text-brown';
                },

                fmtDate(d) {
                    return new Date(d).toLocaleDateString('en-GB', {
                        day: '2-digit', month: 'short', year: '2-digit'
                    });
                },

                toDatetimeLocal(iso) {
                    return new Date(iso).toISOString().slice(0, 16);
                },
            };
        }
    </script>
@endpush
