@extends('layouts.app')

@section('title', $data['title'])

@section('content')
    <div class="bg-ivory text-brand overflow-hidden">

        {{-- ── HERO ─────────────────────────────────────────────────────────── --}}
        <section class="relative px-4 lg:px-8 pt-10 pb-12 md:pt-14 md:pb-16 overflow-hidden">
            <div class="max-w-8xl mx-auto">

                {{-- Three-panel grid: [accent squares] | [text] | [tall portrait] --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:h-165">

                    {{-- ── Panel 1: Two accent squares (desktop left, mobile bottom) ── --}}
                    <div class="hidden lg:flex lg:col-span-3 flex-col gap-5">

                        {{-- Square accent 1 --}}
                        <div
                            class="flex-1 rounded-3xl border border-champagne bg-cream flex flex-col items-center justify-center gap-2.5 group hover:border-primary/30 transition-colors">
                            <svg class="w-7 h-7 text-black-ghost group-hover:text-primary/50 transition-colors" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                            <p class="font-mono text-[11px] text-muted">about-accent-1.jpg</p>
                            <p class="text-[10px] text-taupe tracking-widest font-semibold">1 : 1</p>
                        </div>

                        {{-- Square accent 2 --}}
                        <div
                            class="flex-1 rounded-3xl border border-champagne bg-cream flex flex-col items-center justify-center gap-2.5 group hover:border-primary/30 transition-colors">
                            <svg class="w-7 h-7 text-black-ghost group-hover:text-primary/50 transition-colors"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                            <p class="font-mono text-[11px] text-muted">about-accent-2.jpg</p>
                            <p class="text-[10px] text-taupe tracking-widest font-semibold">1 : 1</p>
                        </div>

                    </div>

                    {{-- ── Panel 2: Text center ── --}}
                    <div
                        class="lg:col-span-5 flex flex-col justify-center text-center lg:text-left px-0 lg:px-8 py-6 lg:py-0">

                        {{-- Label chip --}}
                        <div
                            class="inline-flex self-center lg:self-start items-center gap-2.5 px-4 py-1.5 rounded-full border border-champagne bg-white mb-7">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            <span class="text-xs font-bold uppercase tracking-[0.35em] text-taupe">Luxury Artisanal
                                Foods</span>
                        </div>

                        {{-- Headline --}}
                        <h1
                            class="font-heading text-5xl sm:text-6xl lg:text-[58px] xl:text-[68px] text-brand leading-none tracking-tight mb-5">
                            Taste the<br>Finest of<br>
                            <span class="text-primary">Bangladesh</span>
                        </h1>

                        {{-- Divider --}}
                        <div class="flex items-center gap-3 justify-center lg:justify-start mb-5">
                            <div class="h-px w-10 bg-champagne"></div>
                            <span class="text-primary text-xs">✦</span>
                            <div class="h-px w-10 bg-champagne"></div>
                        </div>

                        {{-- Sub-copy --}}
                        <p class="text-base text-muted font-light leading-relaxed mb-7 max-w-xs mx-auto lg:mx-0">
                            Small-batch, chef-grade, beautifully presented. LaLaDia celebrates rare ingredients and
                            time-honored methods of Bangladesh.
                        </p>

                        {{-- Pills --}}
                        <div class="flex flex-wrap gap-2 justify-center lg:justify-start mb-8">
                            @foreach (['Affordable Premium', 'Toxin-Free', 'Nature-Driven'] as $pill)
                                <span
                                    class="px-4 py-1.5 rounded-full bg-cream border border-champagne text-xs font-bold uppercase tracking-widest text-brown">
                                    {{ $pill }}
                                </span>
                            @endforeach
                        </div>

                        {{-- CTAs --}}
                        <div class="flex items-center gap-4 justify-center lg:justify-start mb-8">
                            <a href="{{ route('product.index') }}"
                                class="inline-flex items-center gap-2.5 bg-primary text-white font-bold px-7 py-3.5 rounded-full hover:bg-secondary transition-colors shadow-lg shadow-primary/20 text-sm">
                                Shop All Products
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                            <a href="#our-story"
                                class="text-sm font-semibold text-muted hover:text-primary transition-colors flex items-center gap-1.5">
                                Our Story
                                <i class="fa-solid fa-arrow-down text-xs"></i>
                            </a>
                        </div>

                        {{-- Pillars tagline --}}
                        <div class="flex items-center gap-3 justify-center lg:justify-start">
                            <div class="h-px w-6 bg-champagne shrink-0"></div>
                            <p class="text-[9px] font-bold uppercase tracking-[0.25em] text-taupe">
                                Heritage · Craftsmanship · Provenance · Integrity
                            </p>
                        </div>
                    </div>

                    {{-- ── Panel 3: Tall portrait (desktop right, mobile top) ── --}}
                    <div class="lg:col-span-4 order-first lg:order-0">

                        {{-- Mobile: fixed aspect so it doesn't go full-screen-tall --}}
                        <div
                            class="aspect-3/4 lg:aspect-auto lg:h-full rounded-3xl border border-champagne bg-cream flex flex-col items-center justify-center gap-2.5 group hover:border-primary/30 transition-colors">
                            <img src="{{ asset('assets/about/about-hero.jpg') }}" alt="About Us"
                                class="w-full h-full object-cover rounded-3xl">
                        </div>

                    </div>

                </div>

                {{-- Mobile only: two accent squares side by side below text --}}
                <div class="grid grid-cols-2 gap-4 mt-4 lg:hidden">
                    <div
                        class="aspect-square rounded-3xl border border-champagne bg-cream flex flex-col items-center justify-center gap-2">
                        <p class="font-mono text-[11px] text-muted">about-accent-1.jpg</p>
                        <p class="text-[10px] text-taupe tracking-widest font-semibold">1 : 1</p>
                    </div>
                    <div
                        class="aspect-square rounded-3xl border border-champagne bg-cream flex flex-col items-center justify-center gap-2">
                        <p class="font-mono text-[11px] text-muted">about-accent-2.jpg</p>
                        <p class="text-[10px] text-taupe tracking-widest font-semibold">1 : 1</p>
                    </div>
                </div>

            </div>
        </section>

        {{-- ── BRAND STORY ──────────────────────────────────────────────────── --}}
        <section id="our-story" class="py-16 md:py-24 px-4 lg:px-8">
            <div class="max-w-8xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                    {{-- Left: narrative --}}
                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.35em] text-primary mb-4 block">Who We
                            Are</span>
                        <h2 class="font-heading text-3xl md:text-4xl text-brand leading-tight mb-6">
                            Luxury Artisanal Foods,<br>Rooted in Tradition
                        </h2>
                        <div class="space-y-4 text-muted font-light leading-relaxed text-base">
                            <p>
                                LaLaDia was born from a simple belief: the finest foods deserve the finest ingredients,
                                prepared with patience and deep respect for tradition. We bring you the rare, the authentic,
                                and the extraordinary — sourced directly from the best origins across Bangladesh.
                            </p>
                            <p>
                                From the wild heart of the Sundarbans to coastal drying yards and family kitchen traditions,
                                every LaLaDia product carries a story of place, people, and craft. Our small-batch approach
                                ensures nothing is rushed and nothing is compromised.
                            </p>
                            <p>
                                Designed for discerning customers and premium gifting, LaLaDia elevates everyday staples
                                into
                                curated experiences — free from toxins, rich in provenance, and presented with pride.
                            </p>
                        </div>
                    </div>

                    {{-- Right: stat cards --}}
                    <div class="grid grid-cols-2 gap-4">
                        @php
                            $stats = [
                                [
                                    'value' => '100%',
                                    'label' => 'Natural Ingredients',
                                    'sub' => 'No artificial additives',
                                ],
                                ['value' => 'Small', 'label' => 'Batch Crafted', 'sub' => 'Hands-on, never rushed'],
                                [
                                    'value' => '6+',
                                    'label' => 'Product Categories',
                                    'sub' => 'Honey, Ghee, Pickle & more',
                                ],
                                ['value' => 'Zero', 'label' => 'Artificial Additives', 'sub' => 'Clean label, always'],
                            ];
                        @endphp
                        @foreach ($stats as $stat)
                            <div
                                class="bg-white rounded-3xl p-6 border border-champagne hover:border-primary/40 hover:shadow-md transition-all duration-300">
                                <p class="font-heading text-3xl font-bold text-primary mb-1">{{ $stat['value'] }}</p>
                                <p class="text-sm font-semibold text-brand mb-0.5">{{ $stat['label'] }}</p>
                                <p class="text-xs text-taupe font-light">{{ $stat['sub'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ── WHAT WE OFFER ────────────────────────────────────────────────── --}}
        <section class="py-16 md:py-24 px-4 lg:px-8 bg-cream">
            <div class="max-w-8xl mx-auto">

                {{-- Section header --}}
                <div class="text-center mb-14">
                    <span class="text-xs font-bold uppercase tracking-[0.35em] text-primary mb-3 block">What We Offer</span>
                    <h2 class="font-heading text-3xl md:text-4xl text-brand">The LaLaDia Artisanal Range</h2>
                </div>

                {{-- 3-column offer cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php
                        $offerings = [
                            [
                                'icon' => '🍯',
                                'title' => 'Artisanal Range',
                                'description' =>
                                    'Our core collection spans the finest natural foods — each product selected for its cultural depth and flavour integrity.',
                                'items' => [
                                    'Single-Origin Honeys',
                                    'Clarified Ghee',
                                    'Heritage Pickles',
                                    'Premium Dry Fish',
                                    'Limited Editions',
                                ],
                            ],
                            [
                                'icon' => '🌾',
                                'title' => 'Craft & Provenance',
                                'description' =>
                                    'Made using time-honored methods, sourced from trusted origins, and finished with the careful attention it deserves.',
                                'items' => [
                                    'Family-style recipes',
                                    'Careful sourcing',
                                    'Refined finishing',
                                    'Small-batch production',
                                    'Zero artificial shortcuts',
                                ],
                            ],
                            [
                                'icon' => '🎁',
                                'title' => 'Premium Presentation',
                                'description' =>
                                    'Packaging that honours what\'s inside. Suitable for gifting, premium retail, and making every delivery feel like an occasion.',
                                'items' => [
                                    'Elevated packaging',
                                    'Gift-ready finishes',
                                    'Premium retail quality',
                                    'Thoughtful design',
                                    'Brand integrity',
                                ],
                            ],
                        ];
                    @endphp
                    @foreach ($offerings as $offer)
                        <div
                            class="bg-white rounded-3xl p-8 border border-champagne group hover:shadow-xl hover:border-primary/30 transition-all duration-300 flex flex-col">
                            <div class="text-4xl mb-6">{{ $offer['icon'] }}</div>
                            <h3 class="font-heading text-xl text-brand mb-2">{{ $offer['title'] }}</h3>
                            <p class="text-sm text-muted font-light leading-relaxed mb-6">{{ $offer['description'] }}</p>
                            <ul class="space-y-2.5 mt-auto">
                                @foreach ($offer['items'] as $item)
                                    <li class="flex items-center gap-2.5 text-sm text-brown">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>
                                        {{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── OUR FOUR PILLARS ─────────────────────────────────────────────── --}}
        <section class="py-16 md:py-24 px-4 lg:px-8">
            <div class="max-w-8xl mx-auto">
                <div class="text-center mb-14">
                    <span class="text-xs font-bold uppercase tracking-[0.35em] text-primary mb-3 block">What We Stand
                        For</span>
                    <h2 class="font-heading text-3xl md:text-4xl text-brand">Our Four Pillars</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        $pillars = [
                            [
                                'number' => '01',
                                'title' => 'Heritage',
                                'description' =>
                                    'Rooted in Bangladeshi culinary tradition. Every recipe honours generations of flavour wisdom passed down through families.',
                            ],
                            [
                                'number' => '02',
                                'title' => 'Craftsmanship',
                                'description' =>
                                    'Produced in small batches with hands-on care. No mass production lines, no shortcuts, no compromise on quality.',
                            ],
                            [
                                'number' => '03',
                                'title' => 'Provenance',
                                'description' =>
                                    'We know exactly where every ingredient comes from — the Sundarbans, coastal yards, heritage farms and trusted growers.',
                            ],
                            [
                                'number' => '04',
                                'title' => 'Indulgence with Integrity',
                                'description' =>
                                    'Premium without compromise. Toxin-free, nature-driven, and honest about every single ingredient in every single product.',
                            ],
                        ];
                    @endphp
                    @foreach ($pillars as $pillar)
                        <div
                            class="relative bg-white rounded-3xl p-8 border border-champagne group hover:border-primary/40 hover:shadow-lg transition-all duration-300 overflow-hidden">
                            {{-- Ghost background number --}}
                            <span
                                class="absolute -bottom-2 -right-1 font-heading text-8xl font-bold leading-none select-none text-primary/6">
                                {{ $pillar['number'] }}
                            </span>
                            {{-- Gold accent line --}}
                            <div class="w-8 h-0.5 bg-primary mb-6 group-hover:w-16 transition-all duration-500"></div>
                            <p class="text-xs font-bold uppercase tracking-[0.3em] text-taupe mb-2">{{ $pillar['number'] }}
                            </p>
                            <h3 class="font-heading text-xl text-brand mb-3">{{ $pillar['title'] }}</h3>
                            <p class="text-sm text-muted font-light leading-relaxed relative z-10">
                                {{ $pillar['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── PRODUCT SPOTLIGHT ────────────────────────────────────────────── --}}
        <section class="py-16 md:py-24 px-4 lg:px-8 bg-cream">
            <div class="max-w-8xl mx-auto">
                <div class="text-center mb-14">
                    <span class="text-xs font-bold uppercase tracking-[0.35em] text-primary mb-3 block">Our Range</span>
                    <h2 class="font-heading text-3xl md:text-4xl text-brand">Crafted for Every Table</h2>
                    <p class="mt-3 text-muted font-light max-w-xl mx-auto text-sm leading-relaxed">
                        From the Sundarbans to your kitchen — every category is a tribute to Bangladesh's richest food
                        traditions.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @php
                        $spotlight = [
                            [
                                'icon' => '🍯',
                                'name' => 'Single-Origin Honey',
                                'origin' => 'Sundarbans Mangrove Forest',
                                'note' =>
                                    'Raw, unfiltered, and wild-harvested from the world\'s largest mangrove. No heat, no additives — pure nature.',
                                'tag' => 'Bestseller',
                            ],
                            [
                                'icon' => '🥛',
                                'name' => 'Clarified Ghee',
                                'origin' => 'Traditional Slow-Cook Method',
                                'note' =>
                                    'Pure cow milk cream rendered in small batches for deep aroma. Free from MSG and artificial additives.',
                                'tag' => 'Heritage',
                            ],
                            [
                                'icon' => '🫙',
                                'name' => 'Heritage Pickles',
                                'origin' => 'Cold-Pressed Mustard Oil',
                                'note' =>
                                    'Hilsa fish and premium beef — bold authentic spices, naturally preserved without chemical shortcuts.',
                                'tag' => 'Artisanal',
                            ],
                            [
                                'icon' => '🐟',
                                'name' => 'Premium Dry Fish',
                                'origin' => 'Coastal Drying Yards',
                                'note' =>
                                    'Sun-dried naturally. Loitta, Churi, Modhu Faisa, Mowrala Kachki — pure coastal provenance.',
                                'tag' => 'Traditional',
                            ],
                            [
                                'icon' => '🥭',
                                'name' => 'Heritage Mangoes',
                                'origin' => 'Satkhira, Rajshahi & Rangpur',
                                'note' =>
                                    'Carbide-free Himsagar, Harivanga, Langra, Amrapali, Banana Mango, and Gourmati varieties.',
                                'tag' => 'Seasonal',
                            ],
                            [
                                'icon' => '✦',
                                'name' => 'Limited Editions',
                                'origin' => 'Special Occasions & Gifting',
                                'note' =>
                                    'Curated seasonal releases and premium gift collections — for moments that deserve something extraordinary.',
                                'tag' => 'Exclusive',
                            ],
                        ];
                    @endphp
                    @foreach ($spotlight as $cat)
                        <div
                            class="bg-white rounded-3xl p-6 border border-champagne hover:border-primary/40 hover:shadow-md transition-all duration-300">
                            <div class="flex items-start justify-between mb-5">
                                <span class="text-3xl">{{ $cat['icon'] }}</span>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest text-primary bg-primary/10 px-3 py-1 rounded-full">
                                    {{ $cat['tag'] }}
                                </span>
                            </div>
                            <h3 class="font-heading text-lg text-brand mb-1">{{ $cat['name'] }}</h3>
                            <p class="text-[11px] font-semibold text-taupe uppercase tracking-wider mb-3">
                                {{ $cat['origin'] }}</p>
                            <p class="text-sm text-muted font-light leading-relaxed">{{ $cat['note'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── OUR PROMISE ──────────────────────────────────────────────────── --}}
        <section class="py-16 md:py-24 px-4 lg:px-8">
            <div class="max-w-8xl mx-auto">
                <div class="bg-white rounded-3xl border border-champagne p-10 md:p-16">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                        {{-- Left: promise copy --}}
                        <div>
                            <span class="text-xs font-bold uppercase tracking-[0.35em] text-primary mb-3 block">Our
                                Promise</span>
                            <h2 class="font-heading text-3xl md:text-4xl text-brand mb-5 leading-tight">
                                Premium, without<br>the Compromise
                            </h2>
                            <p class="text-muted font-light leading-relaxed text-base">
                                At LaLaDia, luxury is not about price alone — it's about integrity.
                                Every product is free from artificial additives, MSG, and hidden chemicals.
                                We believe you deserve to know exactly what you're eating and exactly where it came from.
                            </p>
                        </div>

                        {{-- Right: commitment list --}}
                        <div class="space-y-4">
                            @php
                                $commitments = [
                                    [
                                        'label' => 'Affordable Premium',
                                        'detail' =>
                                            'Chef-grade quality at honest prices — luxury should never be exclusive.',
                                        'icon' => '💎',
                                    ],
                                    [
                                        'label' => 'Toxin-Free',
                                        'detail' =>
                                            'No preservatives, MSG, artificial colours, or hidden additives — ever.',
                                        'icon' => '🛡️',
                                    ],
                                    [
                                        'label' => 'Nature-Driven',
                                        'detail' =>
                                            'Sourced from natural habitats and processed as minimally as possible.',
                                        'icon' => '🌿',
                                    ],
                                ];
                            @endphp
                            @foreach ($commitments as $c)
                                <div
                                    class="flex items-start gap-4 p-5 rounded-2xl bg-cream border border-champagne hover:border-primary/30 transition-colors">
                                    <span class="text-2xl shrink-0 mt-0.5">{{ $c['icon'] }}</span>
                                    <div>
                                        <p class="text-sm font-bold text-brand mb-1">{{ $c['label'] }}</p>
                                        <p class="text-xs text-muted font-light leading-relaxed">{{ $c['detail'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </section>

        {{-- ── CTA ──────────────────────────────────────────────────────────── --}}
        <section class="py-16 md:py-24 px-4 lg:px-8 bg-cream">
            <div class="max-w-8xl mx-auto">
                <div class="relative bg-primary rounded-3xl p-12 md:p-20 text-center overflow-hidden">
                    {{-- Background orbs --}}
                    <div class="absolute -top-12 -right-12 w-64 h-64 rounded-full bg-white/5 pointer-events-none"></div>
                    <div class="absolute -bottom-12 -left-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>

                    <div class="relative z-10">
                        <span class="text-[10px] font-bold uppercase tracking-[0.5em] text-white/60 mb-5 block">
                            Discover LaLaDia
                        </span>
                        <h2 class="font-heading text-4xl md:text-5xl lg:text-6xl text-white mb-4 leading-tight">
                            Taste the Heritage
                        </h2>
                        <p class="text-white/75 font-light max-w-md mx-auto mb-10 leading-relaxed">
                            Explore our full range of artisanal foods — crafted with care, delivered with pride.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('product.index') }}"
                                class="inline-flex items-center justify-center gap-2.5 bg-white text-primary font-bold px-8 py-4 rounded-full hover:bg-ivory transition-colors shadow-lg text-sm">
                                Shop All Products
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                            <a href="{{ route('home') }}"
                                class="inline-flex items-center justify-center gap-2.5 bg-transparent text-white font-semibold px-8 py-4 rounded-full border border-white/40 hover:bg-white/10 transition-colors text-sm">
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection
