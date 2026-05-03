@extends('layouts.app')

@section('title', 'Premium Honey, Ghee, Pickles & Organic Foods')
@section('meta_description', 'LaLaDia — Pure Sundarbans honey, authentic Bengali pickles, Royal Essence Ghee, premium dates and seasonal mangoes. Trusted by 10,000+ customers across Bangladesh.')
@section('meta_keywords', 'LaLaDia, Sundarbans honey, Bengali pickles, Royal Essence Ghee, Hilsa pickle, organic food Bangladesh, premium dates, shutki, Fazli mango')

@push('styles')
<style>
    /* ── Trust strip ── */
    .trust-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 24px;
        background: var(--color-surface);
        transition: background 0.2s ease;
    }
    .trust-item:hover {
        background: var(--color-bg);
    }
    .trust-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--color-bg);
        border: 1px solid var(--color-border);
        transition: all 0.2s ease;
    }
    .trust-item:hover .trust-icon {
        background: var(--color-primary);
        border-color: var(--color-primary);
    }
    .trust-item:hover .trust-icon svg {
        color: white !important;
    }

    /* ── Mid-promo banner ── */
    .promo-mid {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        background: var(--color-bg-soft);
        border: 1px solid var(--color-border);
    }
    .promo-mid::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, var(--color-primary) 0%, transparent 60%);
        opacity: 0.06;
        pointer-events: none;
    }

    /* ── Section heading with gold accent ── */
    .section-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-primary);
        margin-bottom: 8px;
    }
    .section-eyebrow::before {
        content: '';
        display: block;
        width: 20px;
        height: 2px;
        background: var(--color-primary);
        border-radius: 2px;
    }
</style>
@endpush

@section('content')

    {{-- ══════════════════════════════════════════
         1. HERO / SWIPER BANNERS
    ══════════════════════════════════════════ --}}
    @include('store.partials.hero', [
        'heroCertifications' => $certifications->flatten(),
    ])

    {{-- ══════════════════════════════════════════
         2. TRUST STRIP
    ══════════════════════════════════════════ --}}
    <section style="background: var(--color-surface); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
        <div class="max-w-7xl mx-auto px-4">

            {{-- hairline-grid: gap-px + parent bg = thin gold-tinted separator lines --}}
            <div class="grid grid-cols-2 lg:grid-cols-4" style="gap: 1px; background: var(--color-border);">

                <div class="trust-item">
                    <div class="trust-icon">
                        <svg class="w-5 h-5" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold leading-tight" style="color: var(--color-text);">Fast Delivery</p>
                        <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">All across Bangladesh</p>
                    </div>
                </div>

                <div class="trust-item">
                    <div class="trust-icon">
                        <svg class="w-5 h-5" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold leading-tight" style="color: var(--color-text);">100% Authentic</p>
                        <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">Source-verified quality</p>
                    </div>
                </div>

                <div class="trust-item">
                    <div class="trust-icon">
                        <svg class="w-5 h-5" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold leading-tight" style="color: var(--color-text);">Secure Payment</p>
                        <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">bKash, Nagad & Cards</p>
                    </div>
                </div>

                <div class="trust-item">
                    <div class="trust-icon">
                        <svg class="w-5 h-5" style="color: var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold leading-tight" style="color: var(--color-text);">Trusted Quality</p>
                        <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">10,000+ happy customers</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════
         3. TRENDING PRODUCTS (Swiper carousel)
    ══════════════════════════════════════════ --}}
    @include('store.partials.trending-products', [
        'products' => $trendingProducts,
    ])

    {{-- ══════════════════════════════════════════
         4. PROMOTIONAL AD BANNERS
    ══════════════════════════════════════════ --}}
    @include('store.partials.ad-promotions')

    {{-- ══════════════════════════════════════════
         5. MID-PAGE PROMO STRIP
    ══════════════════════════════════════════ --}}
    <section class="py-12 md:py-16 px-4" style="background: var(--color-bg);">
        <div class="max-w-7xl mx-auto">
            <div class="promo-mid px-8 py-10 md:py-14 flex flex-col md:flex-row items-center justify-between gap-8">

                <div class="text-center md:text-left">
                    <span class="section-eyebrow">Limited Bundles</span>
                    <h2 class="font-heading text-2xl md:text-3xl font-bold mb-3" style="color: var(--color-text);">
                        Exclusive Combo Packs — <br class="hidden md:block">
                        <span style="color: var(--color-primary);">Save More, Live Better</span>
                    </h2>
                    <p class="text-sm md:text-base max-w-md" style="color: var(--color-text-muted);">
                        Carefully curated bundles of our best-sellers. Pure, natural, and crafted for your family.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 shrink-0">
                    <a href="{{ route('combos.index') }}"
                       class="btn-primary px-6 py-3 text-sm font-bold inline-flex items-center gap-2 transition-all duration-300 active:scale-95">
                        Shop Combos
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14m-7-7 7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="{{ route('shop') }}"
                       class="btn-outline px-6 py-3 text-sm font-bold inline-flex items-center gap-2 transition-all duration-300 active:scale-95">
                        Browse All
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════
         6. SHOP BY CATEGORY + ALL PRODUCTS GRID
    ══════════════════════════════════════════ --}}
    @include('store.partials.product-categories', [
        'categories'       => $categories,
        'categoryProducts' => $categoryProducts,
    ])

    {{-- ══════════════════════════════════════════
         7. EXCLUSIVE COMBO PACKS
    ══════════════════════════════════════════ --}}
    @include('store.partials.combo-products', [
        'combos' => $combos,
    ])

    {{-- ══════════════════════════════════════════
         8. QUALITY CERTIFICATIONS
    ══════════════════════════════════════════ --}}
    @include('store.partials.certifications', [
        'certifications' => $certifications,
    ])

    {{-- ══════════════════════════════════════════
         9. BRAND VIDEO SECTION
    ══════════════════════════════════════════ --}}
    @include('store.partials.video-promotion')

    {{-- ══════════════════════════════════════════
         10. CUSTOMER TESTIMONIALS
    ══════════════════════════════════════════ --}}
    @include('store.partials.testimonial-showcase')

@endsection

