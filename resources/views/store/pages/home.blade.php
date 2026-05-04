@extends('layouts.app')

@section('title', 'Luxary Artisanal Foods')
@section('meta_description',
    'LaLaDia — Pure Sundarbans honey, authentic Bengali pickles, Royal Essence Ghee, premium
    dates and seasonal mangoes. Trusted by 10,000+ customers across Bangladesh.')
@section('meta_keywords',
    'LaLaDia, Sundarbans honey, Bengali pickles, Royal Essence Ghee, Hilsa pickle, organic food
    Bangladesh, premium dates, shutki, Fazli mango')

@section('content')

    {{-- ══════════════════════════════════════════
         1. HERO / SWIPER BANNERS
    ══════════════════════════════════════════ --}}
    @include('store.partials.hero', [
        'heroCertifications' => $certifications->flatten()->concat($certifications->flatten()),
    ])

    {{-- ══════════════════════════════════════════
         2. TRUST STRIP
    ══════════════════════════════════════════ --}}
    @include('store.partials.trust-badge')

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
         6. SHOP BY CATEGORY + ALL PRODUCTS GRID
    ══════════════════════════════════════════ --}}
    @include('store.partials.product-categories', [
        'categories' => $categories,
        'categoryProducts' => $categoryProducts,
    ])

    {{-- ══════════════════════════════════════════
         5. MID-PAGE PROMO STRIP
    ══════════════════════════════════════════ --}}
    <section class="py-12 md:py-16 px-4 bg-ivory">
        <div class="max-w-7xl mx-auto">
            <div
                class="relative overflow-hidden rounded-[20px] bg-cream border border-champagne px-8 py-10 md:py-14 flex flex-col md:flex-row items-center justify-between gap-8">

                {{-- Decorative gradient to replace promo-mid::before --}}
                <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent pointer-events-none"></div>

                <div class="relative z-10 text-center md:text-left">
                    <span
                        class="inline-flex items-center gap-2 text-[12px] font-bold tracking-widest uppercase text-primary mb-3">
                        <span class="w-5 h-[2px] bg-primary rounded-full"></span>
                        Limited Bundles
                    </span>
                    <h2 class="font-heading text-2xl md:text-3xl font-bold mb-3 text-brand">
                        Exclusive Combo Packs — <br class="hidden md:block">
                        <span class="text-primary">Save More, Live Better</span>
                    </h2>
                    <p class="text-sm md:text-base max-w-md text-muted">
                        Carefully curated bundles of our best-sellers. Pure, natural, and crafted for your family.
                    </p>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row items-center gap-3 shrink-0">
                    <a href="{{ route('combos.index') }}"
                        class="btn-primary px-6 py-3 text-sm font-bold inline-flex items-center gap-2 transition-all duration-300 active:scale-95">
                        Shop Combos
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 12h14m-7-7 7 7-7 7" />
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
