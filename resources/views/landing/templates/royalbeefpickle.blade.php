@extends('layouts.app')

@section('title', $landing->meta_title ?? $product->name)
@section('meta_description', $landing->meta_description ?? $product->short_description)

@php
    $defaultVariant = $product->variants->first();
    $certifications = $product->certifications->sortBy('sort_order')->values();

    $pickleVariants = $product->variants
        ->map(
            fn($variant) => [
                'id' => $variant->id,
                'title' => $variant->title,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'final_price' => (float) $variant->final_price,
                'discount_percent' => $variant->discount_percent,
                'tiers' => $variant->tierPrices
                    ->map(
                        fn($tier) => [
                            'min_quantity' => (int) $tier->min_quantity,
                            'discount_type' => $tier->discount_type,
                            'discount_value' => (float) $tier->discount_value,
                            'has_free_delivery' => (bool) $tier->has_free_delivery,
                        ],
                    )
                    ->values(),
            ],
        )
        ->values();

    $jsZones = $zones
        ->map(
            fn($z) => [
                'id' => $z->id,
                'name' => $z->name,
                'charge' => (float) $z->base_charge,
                'free_above' => (float) $z->free_shipping_threshold,
            ],
        )
        ->values();
@endphp

@section('content')

    {{-- ═══════════════════════════════════════════════════════════════
     HERO — Bold editorial, near-black with crimson & mustard gold
═══════════════════════════════════════════════════════════════ --}}
    <section class="relative flex items-center overflow-hidden" style="background:#0D0201; min-height:100svh;">

        {{-- Background radial glows --}}
        <div class="absolute inset-0 pointer-events-none"
            style="background:radial-gradient(ellipse 70% 55% at 75% 55%, rgba(139,26,26,0.20) 0%, transparent 65%), radial-gradient(ellipse 50% 70% at 5% 85%, rgba(200,134,10,0.07) 0%, transparent 60%)">
        </div>

        {{-- Giant ghost text --}}
        <div class="absolute inset-0 flex items-center justify-end pr-8 pointer-events-none overflow-hidden select-none">
            <span
                class="font-heading font-black text-[18vw] leading-none uppercase opacity-[0.05] text-[#C8860A] tracking-tighter">BEEF<br>PICKLE</span>
        </div>

        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 relative z-10 w-full">
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-4 items-center">

                {{-- ── Left: Typography ── --}}
                <div class="lg:col-span-6 xl:col-span-7">

                    {{-- Eyebrow --}}
                    <div class="flex items-center gap-3 mb-8">
                        <span class="block w-10 h-px" style="background:#C8860A"></span>
                        <span class="text-xs font-bold tracking-[0.32em] uppercase font-heading"
                            style="color:#C8860A">LaLaDia · Premium Artisan Pickle</span>
                    </div>

                    {{-- Stacked headline — "বিফ" gets outlined treatment --}}
                    <div class="mb-8" style="line-height:1.0">
                        <p class="block font-black font-heading text-white tracking-tight"
                            style="font-size:clamp(3.2rem,8.5vw,6.75rem)">রয়্যাল</p>
                        <p class="block font-black font-heading tracking-tight"
                            style="font-size:clamp(3.2rem,8.5vw,6.75rem); -webkit-text-stroke:2px #C8860A; color:transparent; text-shadow:0 0 80px rgba(139,26,26,0.55)">
                            গরুর মাংসের</p>
                        <p class="block font-black font-heading text-white tracking-tight"
                            style="font-size:clamp(3.2rem,8.5vw,6.75rem)">আচার</p>
                    </div>

                    {{-- Horizontal rule --}}
                    <div class="flex items-center gap-4 mb-8">
                        <div class="h-px" style="width:40px; background:#C8860A; opacity:0.7"></div>
                        <span class="text-[10px] font-bold tracking-[0.45em] uppercase font-heading"
                            style="color:rgba(255,255,255,0.22)">খাঁটি উপাদান · ঐতিহ্যবাহী রেসিপি</span>
                        <div class="flex-1 h-px"
                            style="background:linear-gradient(90deg, rgba(200,134,10,0.4), transparent)"></div>
                    </div>

                    {{-- Description --}}
                    <p class="text-base md:text-lg leading-relaxed font-hind max-w-lg mb-8"
                        style="color:rgba(255,255,255,0.58)">
                        প্রিমিয়াম গরুর মাংস, খাঁটি কোল্ড-প্রেসড সরিষার তেল ও ১০+ হাতে বাটা দেশীয় মশলায় তৈরি।
                        কোনো কৃত্রিম রং, MSG বা প্রিজারভেটিভ নেই — কেবল আসল স্বাদ।
                    </p>

                    {{-- Key micro-stats --}}
                    <div class="flex flex-wrap gap-3 mb-10">
                        @foreach ([['num' => '১০+', 'label' => 'দেশীয় মশলা'], ['num' => '০%', 'label' => 'কেমিক্যাল'], ['num' => '৪০০গ্রা', 'label' => 'নেট ওজন'], ['num' => '১২ মাস', 'label' => 'শেলফ লাইফ']] as $s)
                            <div class="flex flex-col items-center px-4 py-2.5 rounded-xl"
                                style="border:1px solid rgba(200,134,10,0.28); background:rgba(200,134,10,0.07)">
                                <span class="font-black font-heading text-base"
                                    style="color:#C8860A">{{ $s['num'] }}</span>
                                <span class="text-[10px] font-semibold tracking-wider uppercase mt-0.5"
                                    style="color:rgba(255,255,255,0.32)">{{ $s['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- CTAs --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#checkout"
                            class="inline-flex items-center justify-center gap-2.5 px-8 py-4 font-bold text-base text-white rounded-full transition-all hover:scale-105"
                            style="background:#8B1A1A; box-shadow:0 8px 32px rgba(139,26,26,0.50)">
                            এখনই অর্ডার করুন
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="#story"
                            class="inline-flex items-center justify-center gap-2 px-8 py-4 font-medium text-base rounded-full transition-all"
                            style="border:1px solid rgba(255,255,255,0.18); color:rgba(255,255,255,0.60);"
                            onmouseover="this.style.borderColor='rgba(255,255,255,0.45)';this.style.color='white'"
                            onmouseout="this.style.borderColor='rgba(255,255,255,0.18)';this.style.color='rgba(255,255,255,0.60)'">
                            আমাদের গল্প ↓
                        </a>
                    </div>

                </div>

                {{-- ── Right: Hero image placeholder ── --}}
                <div class="lg:col-span-6 xl:col-span-5 flex justify-center items-center relative">

                    {{-- Soft glow --}}
                    <div class="absolute inset-0 rounded-full blur-3xl pointer-events-none"
                        style="background:radial-gradient(circle at 55% 50%, rgba(139,26,26,0.30) 0%, transparent 65%)">
                    </div>

                    {{-- Floating spice tags --}}
                    <div class="absolute top-6 -left-1 sm:-left-4 z-20 px-3 py-1.5 rounded-full text-xs font-bold font-hind text-white shadow-xl hidden sm:flex items-center gap-1.5"
                        style="background:#C8860A; transform:rotate(-6deg)">
                        🫙 কোল্ড-প্রেসড তেল
                    </div>
                    <div class="absolute bottom-24 -right-1 sm:-right-5 z-20 px-3 py-1.5 rounded-full text-xs font-bold font-hind shadow-xl hidden sm:block"
                        style="background:white; color:#8B1A1A; transform:rotate(5deg)">
                        ✓ প্রিজারভেটিভমুক্ত
                    </div>
                    <div class="absolute top-[45%] -right-2 sm:-right-8 z-20 px-3 py-1.5 rounded-full text-xs font-bold font-hind text-white shadow-xl hidden lg:block"
                        style="background:#8B1A1A; transform:rotate(9deg)">
                        🌶️ হাতে বাটা মশলা
                    </div>

                    {{-- Image placeholder --}}
                    <div class="relative w-full max-w-xs sm:max-w-sm lg:max-w-none">
                        <div class="aspect-3/4 w-full rounded-3xl flex flex-col items-center justify-center gap-3 shadow-2xl overflow-hidden"
                            style="background:#1C0A04; border:1px solid rgba(139,26,26,0.32)">
                            <img src="{{ asset('assets/landing/beef-pickle-hero.jpg') }}" alt="Beef Pickle Jar"
                                class="w-full h-full object-cover hover:scale-125 transition-all duration-300">
                        </div>
                        {{-- Weight badge --}}
                        <div class="absolute bottom-5 left-4 flex items-center gap-3 px-4 py-2.5 rounded-xl shadow-xl"
                            style="background:rgba(255,255,255,0.96); border:1px solid rgba(200,134,10,0.2); backdrop-filter:blur(8px)">
                            <span class="text-xl">🌶️</span>
                            <div class="font-hind">
                                <p class="text-[9px] font-bold uppercase tracking-wider" style="color:#C8860A">নেট ওজন</p>
                                <p class="font-black text-sm" style="color:#1C0A04">৪০০ গ্রাম</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Scroll cue --}}
        <div
            class="absolute bottom-7 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1.5 animate-bounce pointer-events-none">
            <span class="text-[9px] tracking-[0.35em] uppercase font-heading" style="color:rgba(255,255,255,0.18)">স্ক্রোল
                করুন</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                style="color:rgba(255,255,255,0.18)">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
     INGREDIENT MARQUEE — Scrolling ingredient strip
═══════════════════════════════════════════════════════════════ --}}
    <div class="overflow-hidden py-3.5 relative"
        style="background:#FEF9F2; border-top:1px solid #F0DCC8; border-bottom:1px solid #F0DCC8;">
        <div class="pointer-events-none absolute left-0 top-0 h-full w-20 z-10"
            style="background:linear-gradient(90deg,#FEF9F2,transparent)"></div>
        <div class="pointer-events-none absolute right-0 top-0 h-full w-20 z-10"
            style="background:linear-gradient(270deg,#FEF9F2,transparent)"></div>

        <div class="pickle-marquee-track flex whitespace-nowrap">
            @php
                $marqueeItems = [
                    ['e' => '🥩', 'n' => 'প্রিমিয়াম গরুর মাংস'],
                    ['e' => '🫙', 'n' => 'কোল্ড-প্রেসড সরিষার তেল'],
                    ['e' => '🌶️', 'n' => 'শুকনো লাল মরিচ'],
                    ['e' => '🌿', 'n' => 'ধনিয়া'],
                    ['e' => '⭐', 'n' => 'জিরা'],
                    ['e' => '🍃', 'n' => 'তেজপাতা'],
                    ['e' => '🟡', 'n' => 'হলুদ'],
                    ['e' => '🧄', 'n' => 'রসুন'],
                    ['e' => '🌱', 'n' => 'মেথি'],
                    ['e' => '🍶', 'n' => 'প্রাকৃতিক ভিনেগার'],
                    ['e' => '🌑', 'n' => 'কালো সরিষা'],
                    ['e' => '🫛', 'n' => 'আদা'],
                ];
            @endphp
            @foreach (array_merge($marqueeItems, $marqueeItems) as $item)
                <span class="inline-flex items-center gap-2 px-5 text-sm font-semibold font-hind shrink-0"
                    style="color:#3B1208">
                    <span>{{ $item['e'] }}</span>
                    <span>{{ $item['n'] }}</span>
                    <span class="ml-3 text-xs" style="color:#C8860A">✦</span>
                </span>
            @endforeach
        </div>

        <style>
            .pickle-marquee-track {
                animation: pickleMarquee 38s linear infinite;
                will-change: transform;
            }

            .pickle-marquee-track:hover {
                animation-play-state: paused;
            }

            @keyframes pickleMarquee {
                from {
                    transform: translateX(0);
                }

                to {
                    transform: translateX(-50%);
                }
            }
        </style>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
     BRAND STORY — Editorial split layout
═══════════════════════════════════════════════════════════════ --}}
    <section id="story" class="py-16 md:py-24" style="background:#FEF9F2">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Section label --}}
            <div class="flex items-center gap-5 mb-14">
                <div class="h-px flex-1" style="background:linear-gradient(90deg, transparent, #E0C8B0)"></div>
                <span class="text-xs font-bold tracking-[0.32em] uppercase font-heading px-2" style="color:#8B1A1A">আমাদের
                    গল্প</span>
                <div class="h-px flex-1" style="background:linear-gradient(270deg, transparent, #E0C8B0)"></div>
            </div>

            <div class="grid lg:grid-cols-12 gap-10 lg:gap-16 items-start">

                {{-- Image column --}}
                <div class="lg:col-span-6">
                    <div class="relative">
                        <div class="absolute -bottom-4 -right-4 w-full h-full rounded-3xl pointer-events-none hidden md:block"
                            style="background:#F0DCC8; border-radius:1.5rem; z-index:0"></div>
                        <div id="pickleStoryVideo" data-video data-video-type="youtube" data-video-src="QpDolHdo_u8"
                            data-video-thumbnail="{{ asset('assets/landing/beef-pickle-story-thumb.jpg') }}"
                            data-video-autoplay="true" data-video-lazy="false" data-video-badge="রয়্যাল বিফ আচার"
                            data-video-title="আমাদের তৈরির গল্প"
                            class="shadow-lg rounded-3xl border-2 border-[#8B1A1A] overflow-hidden cursor-pointer relative z-10">
                        </div>
                    </div>

                    {{-- Inline stat pills --}}
                    <div class="grid grid-cols-3 gap-3 mt-8">
                        @foreach ([['num' => '৫০০+', 'label' => 'সন্তুষ্ট পরিবার'], ['num' => '🚚', 'label' => 'সমগ্র বাংলাদেশ ডেলিভারি'], ['num' => '২০২১', 'label' => 'থেকে তৈরি']] as $s)
                            <div class="text-center py-4 rounded-2xl"
                                style="background:#F5E8D8; border:1px solid #E8D5BE">
                                <p class=" font-inter font-black text-lg" style="color:#8B1A1A">
                                    {{ $s['num'] }}</p>
                                <p class="text-xs font-hind mt-0.5" style="color:#6B4030">{{ $s['label'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Text column --}}
                <div class="lg:col-span-6">
                    <h2 class="font-black font-heading leading-tight mb-6"
                        style="font-size:clamp(1.9rem,3.8vw,3.1rem); color:#1C0A04">
                        ঐতিহ্যবাহী রেসিপি,<br>
                        <em style="color:#8B1A1A; font-style:italic">আধুনিক বিশুদ্ধতার মানদণ্ডে</em>
                    </h2>

                    {{-- Pull quote --}}
                    <blockquote class="border-l-4 pl-6 py-1 my-7" style="border-color:#8B1A1A">
                        <p class="text-lg font-bold font-hind leading-relaxed" style="color:#3B1208; font-style:italic">
                            "প্রতিটি জারে আছে প্রজন্মের স্বাদ, বাংলাদেশের মাটির গন্ধ, আর একটি পরিবারের ভালোবাসার রেসিপি।"
                        </p>
                    </blockquote>

                    <p class="text-base leading-relaxed font-hind mb-8" style="color:#5A3020">
                        রয়্যাল বিফ আচারের জন্ম সেই বিশ্বাস থেকে — প্রকৃত স্বাদের জন্য কোনো কৃত্রিম উপাদানের প্রয়োজন নেই।
                        আমরা প্রতিটি ব্যাচে ব্যবহার করি সতেজ, হাতে বাছাই করা গরুর মাংস, খাঁটি কোল্ড-প্রেসড সরিষার তেল এবং
                        হাতে বাটা তাজা মশলা। ছোট ব্যাচে তৈরি — কারণ মানের সঙ্গে আপোষ আমাদের স্বভাবে নেই।
                    </p>

                    {{-- Feature grid --}}
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach ([['emoji' => '🥩', 'title' => 'প্রিমিয়াম বিফ', 'desc' => 'হাতে বাছাই করা তাজা মাংস, প্রতিটি টুকরো সমান মান ও আকারের।'], ['emoji' => '🫙', 'title' => 'কোল্ড-প্রেসড তেল', 'desc' => 'রাসায়নিক পরিশোধন ছাড়া — ওমেগা-৩ ও ভিটামিন E অক্ষুণ্ণ।'], ['emoji' => '🌿', 'title' => 'হাতে বাটা মশলা', 'desc' => '১০+ তাজা দেশীয় মশলার নিখুঁত মিশ্রণ, কোনো প্রিমিক্স নয়।'], ['emoji' => '🏺', 'title' => 'প্রাকৃতিক সংরক্ষণ', 'desc' => 'ভিনেগার ও লবণের স্বাভাবিক ক্রিয়ায় ১২ মাস পর্যন্ত তাজা।']] as $item)
                            <div class="flex gap-4 p-4 rounded-2xl transition-all hover:-translate-y-0.5 hover:shadow-md"
                                style="background:white; border:1px solid #F0DCC8; box-shadow:0 2px 10px rgba(139,26,26,0.04)">
                                <span class="text-2xl leading-none mt-0.5 shrink-0">{{ $item['emoji'] }}</span>
                                <div>
                                    <span class="font-bold text-sm mb-1 font-hind" style="color:#1C0A04">
                                        {{ $item['title'] }}</span>
                                    <p class="text-xs leading-relaxed font-hind" style="color:#7A5040">
                                        {{ $item['desc'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        <a href="#checkout"
                            class="inline-flex items-center gap-3 px-8 py-4 font-bold text-white rounded-full transition-all hover:scale-105"
                            style="background:#8B1A1A; box-shadow:0 6px 24px rgba(139,26,26,0.35)">
                            এখনই অর্ডার করুন
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
     STATS STRIP — Bold numbers on dark
═══════════════════════════════════════════════════════════════ --}}
    <section class="py-12 md:py-14" style="background:#1C0A04">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-0">
                @php
                    $stats = [
                        ['num' => '১০+', 'unit' => 'প্রকার', 'label' => 'দেশীয় মশলা'],
                        ['num' => '০%', 'unit' => '', 'label' => 'কৃত্রিম উপাদান'],
                        ['num' => '৪০০', 'unit' => 'গ্রাম', 'label' => 'নেট ওজন প্রতি জার'],
                        ['num' => '১২', 'unit' => 'মাস', 'label' => 'শেলফ লাইফ'],
                    ];
                @endphp
                @foreach ($stats as $i => $stat)
                    <div class="text-center md:px-6 {{ $i > 0 ? 'md:border-l' : '' }}"
                        style="border-color:rgba(255,255,255,0.10)">
                        <div class="flex items-end justify-center gap-1 mb-1.5">
                            <span class="font-black leading-none font-heading"
                                style="font-size:clamp(2.6rem,5.5vw,4.2rem); color:#C8860A">{{ $stat['num'] }}</span>
                            @if ($stat['unit'])
                                <span class="font-bold text-base mb-1.5 font-hind"
                                    style="color:rgba(200,134,10,0.65)">{{ $stat['unit'] }}</span>
                            @endif
                        </div>
                        <p class="text-xs font-semibold tracking-widest uppercase font-hind"
                            style="color:rgba(255,255,255,0.32)">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
     PURITY & INGREDIENTS — Magazine split layout
═══════════════════════════════════════════════════════════════ --}}
    <section class="py-16 md:py-24" style="background:#FFFFFF; border-top:1px solid #F7EDE0">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Section heading --}}
            <div class="mb-12 md:mb-16">
                <p class="text-xs font-bold tracking-[0.32em] uppercase mb-3 font-heading" style="color:#8B1A1A">উপাদান ও
                    প্রতিশ্রুতি</p>
                <h2 class="font-black font-heading leading-tight max-w-xl"
                    style="font-size:clamp(1.8rem,4vw,3rem); color:#0D0201">
                    আমরা যা দিই —<br>
                    <span style="color:#8B1A1A">এবং যা দিই না।</span>
                </h2>
            </div>

            <div class="grid lg:grid-cols-12 gap-10 lg:gap-14">

                {{-- Left: Key ingredient cards (7 cols) --}}
                <div class="lg:col-span-7">
                    <h3 class="text-xs font-bold tracking-[0.28em] uppercase mb-7 flex items-center gap-3 font-heading"
                        style="color:#8B1A1A">
                        <span class="w-6 h-px" style="background:#8B1A1A"></span>
                        মূল উপাদান
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach ([['emoji' => '🥩', 'title' => 'প্রিমিয়াম গরুর মাংস', 'tag' => 'প্রধান উপাদান', 'desc' => 'উন্নত মানের তাজা গরুর মাংস — প্রোটিন, আয়রন, জিঙ্ক ও ভিটামিন B12-সমৃদ্ধ। প্রতিটি টুকরো হাতে বাছাই করা।'], ['emoji' => '🫙', 'title' => 'কোল্ড-প্রেসড সরিষার তেল', 'tag' => 'সংরক্ষণ মাধ্যম', 'desc' => 'রাসায়নিক পরিশোধন ছাড়া খাঁটি তেল। ওমেগা-৩, ভিটামিন E এবং প্রাকৃতিক অ্যান্টিঅক্সিডেন্টে ভরপুর।'], ['emoji' => '🌶️', 'title' => 'হাতে বাটা মশলার মিশ্রণ', 'tag' => 'স্বাদের উৎস', 'desc' => 'জিরা, ধনিয়া, মেথি, তেজপাতা, শুকনো মরিচ, হলুদ, আদা, রসুন, কালো সরিষা সহ ১০+ তাজা মশলা।'], ['emoji' => '🍶', 'title' => 'প্রাকৃতিক ভিনেগার ও লবণ', 'tag' => 'প্রাকৃতিক সংরক্ষক', 'desc' => 'অ্যাসিটিক অ্যাসিডের প্রাকৃতিক গুণে ব্যাকটেরিয়া প্রতিরোধ — কোনো রাসায়নিক সংরক্ষক ছাড়াই।']] as $item)
                            <div class="relative p-5 rounded-2xl group transition-all hover:-translate-y-1 hover:shadow-lg cursor-default"
                                style="background:#FEF9F2; border:1px solid #F0DCC8">
                                <div class="absolute top-3 right-3 px-2 py-0.5 rounded-full text-[9px] font-bold tracking-wide uppercase font-heading"
                                    style="background:#F5E8D8; color:#8B1A1A">{{ $item['tag'] }}</div>
                                <span class="text-3xl block mb-3">{{ $item['emoji'] }}</span>
                                <h4 class="font-bold font-hind text-sm mb-2" style="color:#1C0A04">{{ $item['title'] }}
                                </h4>
                                <p class="text-xs leading-relaxed font-hind" style="color:#7A5040">{{ $item['desc'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Zero claims (5 cols) --}}
                <div class="lg:col-span-5">
                    <div class="lg:sticky lg:top-24 space-y-4">
                        <h3 class="text-xs font-bold tracking-[0.28em] uppercase mb-7 flex items-center gap-3 font-heading"
                            style="color:#8B1A1A">
                            <span class="w-6 h-px" style="background:#8B1A1A"></span>
                            আমরা কখনো যোগ করি না
                        </h3>

                        {{-- Dark promise card --}}
                        <div class="p-6 rounded-3xl relative overflow-hidden" style="background:#0D0201">
                            <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full blur-3xl pointer-events-none"
                                style="background:rgba(139,26,26,0.25)"></div>
                            <p class="text-[10px] font-bold tracking-[0.32em] uppercase mb-5 font-heading"
                                style="color:rgba(200,134,10,0.85)">Zero Compromise</p>
                            <ul class="space-y-3.5">
                                @foreach (['কৃত্রিম রং ও সিনথেটিক ফ্লেভার', 'MSG ও মনো-সোডিয়াম গ্লুটামেট', 'রিফাইন্ড বা পুনর্ব্যবহৃত তেল', 'সোডিয়াম বেনজোয়েট প্রিজারভেটিভ', 'কৃত্রিম ঘন করার এজেন্ট', 'রেডিমেড মশলা পাউডার'] as $no)
                                    <li class="flex items-center gap-3">
                                        <span
                                            class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 text-[10px] font-bold"
                                            style="background:rgba(139,26,26,0.40); color:#FF7070">✕</span>
                                        <span class="text-sm font-hind line-through"
                                            style="color:rgba(255,255,255,0.38)">{{ $no }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Shelf life pill --}}
                        <div class="flex items-center gap-4 p-5 rounded-2xl"
                            style="background:#FEF9F2; border:1px solid #F0DCC8">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 text-2xl"
                                style="background:#F5E8D8">🏺</div>
                            <div>
                                <p class="font-bold text-sm" style="color:#1C0A04">১২ মাস পর্যন্ত তাজা</p>
                                <p class="text-xs font-hind mt-0.5" style="color:#7A5040">প্রাকৃতিক উপায়ে — কোনো কৃত্রিম
                                    প্রিজারভেটিভ ছাড়া</p>
                            </div>
                        </div>

                        <a href="#checkout"
                            class="block w-full text-center py-4 font-bold text-white rounded-2xl transition-all hover:scale-[1.02] hover:shadow-xl"
                            style="background:#8B1A1A; box-shadow:0 4px 18px rgba(139,26,26,0.30)">
                            এখনই অর্ডার করুন →
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
     HOW IT'S MADE — Process steps with image
═══════════════════════════════════════════════════════════════ --}}
    <section class="py-16 md:py-24" style="background:#FEF9F2; border-top:1px solid #F0DCC8">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                {{-- Process image placeholder --}}
                <div class="relative flex justify-center order-2 lg:order-1">
                    <div class="absolute -top-4 -left-4 w-full h-full rounded-3xl pointer-events-none hidden md:block"
                        style="background:#F0DCC8; border-radius:1.5rem; transform:rotate(-2deg); z-index:0"></div>
                    <div class="relative w-full aspect-4/5 rounded-3xl flex flex-col items-center justify-center gap-3 shadow-xl overflow-hidden"
                        style="background:#F5E8D8; border:1px solid #E0C8B0; z-index:1">
                        <img src="{{ asset('assets/landing/beef-pickle-process.jpg') }}"
                            alt="Process of making royal beef pickle"
                            class="w-full h-full object-cover rounded-3xl hover:scale-110 transition-all duration-300">
                    </div>
                </div>

                {{-- Steps --}}
                <div class="order-1 lg:order-2">
                    <p class="text-xs font-bold tracking-[0.32em] uppercase mb-3 font-heading" style="color:#8B1A1A">তৈরির
                        প্রক্রিয়া</p>
                    <h2 class="font-black font-heading leading-tight mb-8"
                        style="font-size:clamp(1.9rem,3.8vw,2.9rem); color:#0D0201">
                        ঐতিহ্যের পদ্ধতিতে,<br>
                        <em style="color:#8B1A1A">কোনো শর্টকাট নেই</em>
                    </h2>

                    <div class="relative">
                        {{-- Vertical connector line --}}
                        <div class="absolute left-6 top-6 bottom-6 w-px hidden md:block pointer-events-none"
                            style="background:linear-gradient(to bottom, #C8860A 0%, rgba(200,134,10,0.15) 100%)"></div>

                        <div class="space-y-0">
                            @foreach ([['num' => '০১', 'title' => 'মাংস নির্বাচন ও মেরিনেশন', 'desc' => 'তাজা, উচ্চমানের গরুর মাংস হাতে বাছাই করে সঠিক আকারে কাটা হয়। প্রাকৃতিক উপাদানে কমপক্ষে ৮ ঘন্টা মেরিনেট।'], ['num' => '০২', 'title' => 'মশলা বাটা ও বিশেষ মিশ্রণ', 'desc' => '১০+ তাজা দেশীয় মশলা পাটায় হাতে বেটে তৈরি হয় বিশেষ মসলার পেস্ট। কোনো রেডিমেড পাউডার ব্যবহার হয় না।'], ['num' => '০৩', 'title' => 'কোল্ড-প্রেসড তেলে ধীর রান্না', 'desc' => 'খাঁটি সরিষার তেলে সঠিক তাপমাত্রায় ধীরে রান্না হয় — মশলার সুবাস পুরোপুরি মাংসে মিশে যাওয়া পর্যন্ত।'], ['num' => '০৪', 'title' => 'ঠান্ডা করে এয়ারটাইট প্যাকেজিং', 'desc' => 'প্রাকৃতিক ভিনেগার মিশিয়ে ঠান্ডা করা হয়, তারপর পরিষ্কার গ্লাস জারে ভরে সিল করা হয়।']] as $step)
                                <div class="flex gap-5 pb-7 last:pb-0 group">
                                    <div class="relative shrink-0 z-10">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-black text-sm font-heading transition-all group-hover:scale-110"
                                            style="background:#8B1A1A; color:white; box-shadow:0 4px 16px rgba(139,26,26,0.40)">
                                            {{ $step['num'] }}
                                        </div>
                                    </div>
                                    <div class="pt-1">
                                        <h4 class="font-bold text-base mb-1.5" style="color:#1C0A04">{{ $step['title'] }}
                                        </h4>
                                        <p class="text-sm leading-relaxed font-hind" style="color:#7A5040">
                                            {{ $step['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="#checkout"
                            class="inline-flex items-center gap-3 px-8 py-4 font-bold text-white rounded-full transition-all hover:scale-105"
                            style="background:#1C0A04; box-shadow:0 6px 20px rgba(28,10,4,0.35)">
                            আচার অর্ডার করুন
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24" style="color:#C8860A">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
     SERVING SUGGESTIONS — Dark, elegant
═══════════════════════════════════════════════════════════════ --}}
    <section class="py-16 md:py-24 relative overflow-hidden" style="background:#0D0201">
        <div class="absolute inset-0 pointer-events-none"
            style="background:radial-gradient(ellipse 65% 55% at 15% 55%, rgba(139,26,26,0.18) 0%, transparent 70%)"></div>

        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-16 items-center">

                {{-- Left: Image --}}
                <div class="lg:col-span-5 flex justify-center order-1">
                    <div class="w-full max-w-md aspect-4/3 rounded-3xl flex flex-col items-center justify-center gap-3 shadow-2xl"
                        style="background:#1C0A04; border:1px solid rgba(139,26,26,0.22)">
                        <img src="{{ asset('assets/landing/beef-pickle-serving.jpg') }}"
                            alt="Beef pickle served with rice and paratha"
                            class="w-full h-full object-cover rounded-3xl hover:rotate-3 hover:scale-110 transition-all duration-300">
                    </div>
                </div>

                {{-- Right: Content --}}
                <div class="lg:col-span-7 order-2">
                    <p class="text-xs font-bold tracking-[0.32em] uppercase mb-3 font-heading" style="color:#C8860A">
                        পরিবেশনের পরামর্শ</p>
                    <h2 class="font-black font-heading leading-tight mb-4 text-white"
                        style="font-size:clamp(1.9rem,3.8vw,2.9rem)">
                        কোন খাবারের সাথে<br>
                        <span style="color:#C8860A">সবচেয়ে মানানসই?</span>
                    </h2>
                    <div class="w-14 h-px mb-8" style="background:rgba(200,134,10,0.55)"></div>

                    <div class="grid sm:grid-cols-2 gap-3 mb-8">
                        @foreach ([['emoji' => '🍚', 'title' => 'গরম ভাতের সাথে', 'desc' => 'বাংলাদেশের সেরা সমন্বয়। গরম সাদা ভাত + একটুকরো রয়্যাল বিফ আচার = অতুলনীয় তৃপ্তি।'], ['emoji' => '🫓', 'title' => 'পরোটা ও রুটিতে', 'desc' => 'সকালের নাস্তাকে দিন নতুন মাত্রা। পরোটার ঘিয়ে ভাজা স্বাদে আচারের ঝাঁজ অনন্য।'], ['emoji' => '🍛', 'title' => 'বিরিয়ানি ও খিচুড়িতে', 'desc' => 'উৎসবের পাতে সাইড ডিশ হিসেবে। বিশেষ অনুষ্ঠানে বিরিয়ানির পাশে পরিপূর্ণতা আনে।'], ['emoji' => '🎁', 'title' => 'প্রিমিয়াম উপহার হিসেবে', 'desc' => 'ঈদ, বিবাহ বা যেকোনো উপলক্ষে প্রিয়জনকে দেওয়ার জন্য আদর্শ — উন্নত প্যাকেজিংয়ে।']] as $item)
                            <div class="flex gap-4 p-4 rounded-2xl transition-all hover:-translate-y-0.5"
                                style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08)">
                                <span class="text-2xl leading-none mt-0.5 shrink-0">{{ $item['emoji'] }}</span>
                                <div>
                                    <h4 class="font-bold text-sm mb-1 text-white">{{ $item['title'] }}</h4>
                                    <p class="text-xs leading-relaxed font-hind" style="color:rgba(255,255,255,0.48)">
                                        {{ $item['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a href="#checkout"
                        class="inline-flex items-center gap-3 px-8 py-4 font-bold text-white rounded-full transition-all hover:scale-105"
                        style="background:#8B1A1A; box-shadow:0 6px 28px rgba(139,26,26,0.45)">
                        এখনই অর্ডার করুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
     CERTIFICATIONS (conditional)
═══════════════════════════════════════════════════════════════ --}}
    @if ($certifications->isNotEmpty())
        <section class="py-12 md:py-16" style="background:#FEF9F2; border-top:1px solid #F0DCC8">
            <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <span class="text-xs font-bold tracking-[0.32em] uppercase font-heading" style="color:#8B1A1A">Quality
                        Assurance</span>
                    <h2 class="text-2xl md:text-3xl font-black font-heading mt-2" style="color:#1C0A04">আন্তর্জাতিক মানের
                        নিশ্চয়তা</h2>
                    <div class="w-14 h-1.5 mx-auto rounded-full mt-3" style="background:#8B1A1A"></div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($certifications as $cert)
                        <div class="flex flex-col items-center text-center rounded-2xl p-5 transition-all hover:-translate-y-1 hover:shadow-md"
                            style="background:white; border:1px solid #F0DCC8">
                            <div class="h-16 w-16 rounded-2xl flex items-center justify-center mb-3"
                                style="background:#FEF9F2; border:1px solid #F0DCC8">
                                @if ($cert->logo_path)
                                    <img src="{{ $cert->logo_url }}" alt="{{ $cert->name }}"
                                        class="max-h-full max-w-full object-contain" loading="lazy" />
                                @else
                                    <i class="fas fa-certificate text-2xl" style="color:#8B1A1A"></i>
                                @endif
                            </div>
                            <span class="text-sm font-bold" style="color:#1C0A04">{{ $cert->name }}</span>
                            @if ($cert->category)
                                <span class="text-xs mt-0.5" style="color:#7A5040">{{ $cert->category }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         CHECKOUT
    ═══════════════════════════════════════════════════════════════ --}}
    <section id="checkout" class="py-16 bg-gray-50 font-noto relative min-h-screen">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-10">

                {{-- ── Left: product card ── --}}
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 mb-2">পণ্য নির্বাচন করুন</h2>
                        <p class="text-gray-500">পরিমাণ নির্বাচন করুন, তারপর সঠিক তথ্য দিয়ে অর্ডার কনফার্ম করুন।</p>
                    </div>

                    <div
                        class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-amber-100 transition">
                        <div class="flex items-center gap-4">

                            {{-- Product thumbnail placeholder --}}
                            <div
                                class="w-16 h-16 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center shrink-0 text-amber-300 overflow-hidden">
                                <img src="{{ asset('assets/landing/beef-pickle.png') }}" alt="Beef Pickle Jar"
                                    class="w-full h-full object-cover">
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ $product->name }}</h3>
                                <p id="picklePrice" class="text-amber-700 font-bold mt-0.5">৳০</p>

                                @if ($product->variants->count() > 1)
                                    <div class="flex gap-2 mt-2 flex-wrap">
                                        @foreach ($product->variants as $variant)
                                            <button type="button" data-pickle-variant-id="{{ $variant->id }}"
                                                class="pickle-variant-btn {{ $loop->first ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-gray-200 bg-white text-gray-600 hover:border-amber-300' }} px-2 py-1 text-xs rounded border font-semibold transition">
                                                {{ $variant->title }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Tier hints — populated by JS --}}
                                <div id="pickleTierHints" class="flex gap-2 mt-2 flex-wrap"></div>
                            </div>

                            <div class="flex items-center gap-2 bg-amber-50 p-2 rounded shrink-0">
                                <button id="pickleQtyMinus" type="button"
                                    class="w-6 h-6 bg-gray-100 hover:bg-amber-100 hover:text-amber-700 rounded-xl font-bold transition">−</button>
                                <span id="pickleQty" class="w-6 text-center font-bold text-gray-700">1</span>
                                <button id="pickleQtyPlus" type="button"
                                    class="w-6 h-6 bg-amber-700 hover:bg-amber-800 text-white rounded font-bold transition">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 md:block hidden mt-2">
                        <h4 class="text-base font-bold text-gray-900">অর্ডারের নিয়মাবলী:</h4>
                        <ul class="space-y-2 text-gray-600 text-sm">
                            <li class="flex items-start gap-3 bg-amber-50/60 px-3 py-2 rounded-lg">
                                <i class="fas fa-truck text-amber-700 mt-0.5 shrink-0"></i>
                                <span id="pickleTierRuleText">ডেলিভারি চার্জ এলাকা অনুযায়ী প্রযোজ্য।</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- ── Right: order form ── --}}
                <div class="w-full">
                    <div class="bg-white border border-amber-100 rounded-3xl p-6 md:p-10 shadow-xl shadow-amber-100/40">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-gray-900">অর্ডার কনফার্ম করুন</h2>
                            <p class="text-amber-700 text-sm italic mt-2">সঠিক তথ্য দিয়ে নিচের ফর্মটি পূরণ করুন।</p>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" id="pickleCustName" placeholder="আপনার নাম *"
                                    class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-200 outline-none text-sm transition-all" />
                                <input type="tel" id="pickleCustPhone" placeholder="মোবাইল নম্বর *"
                                    class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-200 outline-none text-sm transition-all" />
                            </div>

                            <input type="text" id="pickleCustAddress"
                                placeholder="পূর্ণ ঠিকানা (বাসা নম্বর, রোড, এলাকা, জেলা) *"
                                class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-200 outline-none text-sm transition-all" />

                            <div
                                class="p-4 bg-orange-50 border border-dashed border-orange-300 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-3 w-3 relative">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                                    </span>
                                    <p class="text-xs md:text-sm text-amber-900">আরও বেশি বা পাইকারি নিতে চান?</p>
                                </div>
                                <a href="tel:01334943783"
                                    class="text-sm md:text-base font-black text-pink-700 hover:underline">কল করুন:
                                    01334 943783</a>
                            </div>

                            {{-- Delivery zone --}}
                            <div id="pickleDeliveryZoneSection"
                                class="bg-amber-50/50 p-4 rounded-xl border border-amber-100 transition-all">
                                <p class="text-sm font-bold text-gray-700 mb-1">
                                    ডেলিভারি এলাকা নির্বাচন করুন
                                    <span class="text-red-500">*</span>
                                </p>
                                <p id="pickleZoneError" class="hidden text-xs text-red-600 mb-2 font-medium">
                                    অনুগ্রহ করে একটি ডেলিভারি এলাকা নির্বাচন করুন।
                                </p>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach ($zones as $zone)
                                        <label
                                            class="flex flex-col items-center p-2 border rounded-lg cursor-pointer text-center transition-all pickle-area-label border-gray-200 bg-white hover:border-amber-400">
                                            <input type="radio" name="pickle_area" value="{{ $zone->id }}"
                                                class="hidden" />
                                            <span class="text-xs font-bold">{{ $zone->name }}</span>
                                            @if ($zone->base_charge == 0)
                                                <span class="text-xs text-green-600">ফ্রি</span>
                                            @else
                                                <span
                                                    class="text-xs text-red-600">৳{{ number_format($zone->base_charge, 0) }}</span>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Order summary --}}
                            <div class="p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300 space-y-2 text-sm">
                                <div id="pickleBreakdown" class="space-y-1 pb-2 border-b border-gray-200 empty:hidden">
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">পণ্যের মূল্য:</span>
                                    <span class="font-semibold">৳ <span id="pickleSubtotal">০</span></span>
                                </div>
                                <div id="pickleTierDiscountRow" class="hidden justify-between">
                                    <span class="text-gray-500">পরিমাণভিত্তিক ছাড়:</span>
                                    <span class="font-semibold text-green-600">-৳ <span
                                            id="pickleTierDiscount">০</span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>ডেলিভারি চার্জ:</span>
                                    <span id="pickleShippingDisplay" class="text-amber-700 font-bold">—</span>
                                </div>
                                <div class="flex justify-between border-t pt-2 mt-1 font-black text-lg text-amber-700">
                                    <span>সর্বমোট:</span>
                                    <span>৳ <span id="pickleTotal">০</span></span>
                                </div>
                            </div>

                            <button id="pickleOrderBtn"
                                class="w-full mt-2 py-5 bg-amber-700 hover:bg-amber-800 text-white font-bold text-xl rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-3">
                                অর্ডার কনফার্ম করুন
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3 block md:hidden mt-5">
                        <h4 class="text-base font-bold text-gray-900">অর্ডারের নিয়মাবলী:</h4>
                        <ul class="text-gray-600 text-sm">
                            <li class="flex items-start gap-3 bg-amber-50/60 px-3 py-2 rounded-lg">
                                <i class="fas fa-truck text-amber-700 mt-0.5 shrink-0"></i>
                                <span id="pickleTierRuleTextMobile">ডেলিভারি চার্জ এলাকা অনুযায়ী প্রযোজ্য।</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        {{-- Success modal --}}
        <div id="pickleSuccessModal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 px-4">
            <div class="bg-white p-8 rounded-3xl max-w-sm w-full text-center shadow-2xl border border-amber-100">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 mb-2">অর্ডার সফল হয়েছে!</h2>
                <p class="text-gray-500 mb-6">আমাদের প্রতিনিধি দ্রুতই আপনার সাথে যোগাযোগ করবেন।</p>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            const PICKLE_VARIANTS = @json($pickleVariants);
            const PICKLE_ZONES = @json($jsZones);
            const PICKLE_SLUG = '{{ $landing->slug }}';

            const pickleState = {
                variantId: {{ $defaultVariant?->id ?? 'null' }},
                qty: 1,
                zoneId: null,
                shippingCost: 0,
            };

            /* ── Helpers ── */
            function pickleText(id, value) {
                const el = document.getElementById(id);
                if (el) el.innerText = value;
            }

            function selectedPickleVariant() {
                return PICKLE_VARIANTS.find(v => Number(v.id) === Number(pickleState.variantId)) ??
                    PICKLE_VARIANTS[0];
            }

            function activePickleTier() {
                const tiers = selectedPickleVariant()?.tiers ?? [];
                return [...tiers]
                    .filter(t => pickleState.qty >= Number(t.min_quantity))
                    .sort((a, b) => Number(b.min_quantity) - Number(a.min_quantity))[0] ?? null;
            }

            function pickleUnitPrice() {
                const variant = selectedPickleVariant();
                const tier = activePickleTier();
                const base = Number(variant?.final_price ?? variant?.price ?? 0);
                if (!tier || Number(tier.discount_value) <= 0) return base;
                if (tier.discount_type === 'percentage')
                    return Math.max(0, base - base * Number(tier.discount_value) / 100);
                return Math.max(0, base - Number(tier.discount_value));
            }

            function pickleRegularUnitPrice() {
                const v = selectedPickleVariant();
                return Number(v?.final_price ?? v?.price ?? 0);
            }

            function pickleTierDiscountAmt() {
                return Math.max(0, (pickleRegularUnitPrice() - pickleUnitPrice()) * pickleState.qty);
            }

            function pickleSubtotalAmt() {
                return pickleUnitPrice() * pickleState.qty;
            }

            function pickleShippingAmt() {
                if (pickleState.zoneId === null) return 0;
                const tier = activePickleTier();
                if (tier?.has_free_delivery) return 0;
                const zone = PICKLE_ZONES.find(z => Number(z.id) === Number(pickleState.zoneId));
                if (!zone) return 0;
                return (zone.free_above > 0 && pickleSubtotalAmt() >= zone.free_above) ?
                    0 : Number(zone.charge ?? 0);
            }

            /* ── Render tier hint badges ── */
            function renderPickleTierHints() {
                const tiers = selectedPickleVariant()?.tiers ?? [];
                const hints = document.getElementById('pickleTierHints');
                if (!hints) return;

                hints.innerHTML = tiers.map(tier => {
                    const parts = [];
                    if (Number(tier.discount_value) > 0) {
                        parts.push(tier.discount_type === 'percentage' ?
                            `${Number(tier.discount_value).toFixed(0)}% ছাড়` :
                            `৳${Number(tier.discount_value).toFixed(0)} ছাড়`);
                    }
                    if (tier.has_free_delivery) parts.push('ফ্রি ডেলিভারি');
                    if (!parts.length) parts.push('স্পেশাল অফার');

                    const active = pickleState.qty >= Number(tier.min_quantity);
                    return `<span class="text-xs rounded-full px-2 py-1 font-semibold border
                        ${active
                            ? 'bg-green-100 text-green-700 border-green-300'
                            : 'bg-amber-50 text-amber-700 border-amber-200'
                        }">${tier.min_quantity}+ নিলে ${parts.join(' + ')}</span>`;
                }).join('');

                /* Update rule text */
                const freeDeliveryTier = tiers.find(t => t.has_free_delivery);
                const rule = freeDeliveryTier ?
                    `${freeDeliveryTier.min_quantity}+ জার অর্ডার করলে ডেলিভারি ফ্রি!` :
                    'ডেলিভারি চার্জ এলাকা অনুযায়ী প্রযোজ্য।';
                pickleText('pickleTierRuleText', rule);
                pickleText('pickleTierRuleTextMobile', rule);
            }

            /* ── Recalculate & paint summary ── */
            function calculatePickle() {
                const variant = selectedPickleVariant();
                const unit = pickleUnitPrice();
                const regular = pickleRegularUnitPrice();
                const subtotal = pickleSubtotalAmt();
                const discount = pickleTierDiscountAmt();
                const shipping = pickleShippingAmt();
                pickleState.shippingCost = shipping;

                pickleText('pickleQty', pickleState.qty);
                pickleText('picklePrice',
                    `৳${unit.toFixed(0)}${regular > unit ? ` (৳${regular.toFixed(0)} থেকে)` : ''}`);
                pickleText('pickleSubtotal', subtotal.toFixed(0));
                pickleText('pickleTierDiscount', discount.toFixed(0));
                pickleText('pickleShippingDisplay',
                    pickleState.zoneId === null ? '—' : (shipping === 0 ? 'ফ্রি' : `৳ ${shipping.toFixed(0)}`));
                pickleText('pickleTotal',
                    (subtotal + (pickleState.zoneId === null ? 0 : shipping)).toFixed(0));

                const tierRow = document.getElementById('pickleTierDiscountRow');
                tierRow?.classList.toggle('hidden', discount <= 0);
                tierRow?.classList.toggle('flex', discount > 0);

                const breakdown = document.getElementById('pickleBreakdown');
                if (breakdown) {
                    breakdown.innerHTML = `<div class="flex justify-between text-xs text-gray-600">
                        <span class="truncate pr-2">${variant?.title ?? 'Pickle'} × ${pickleState.qty}</span>
                        <span class="font-semibold text-gray-700 whitespace-nowrap">৳ ${subtotal.toFixed(0)}</span>
                    </div>`;
                }
            }

            /* ── Variant switcher ── */
            function setActivePickleVariant(btn) {
                document.querySelectorAll('.pickle-variant-btn').forEach(b => {
                    b.classList.remove('border-amber-500', 'bg-amber-50', 'text-amber-700');
                    b.classList.add('border-gray-200', 'bg-white', 'text-gray-600');
                });
                btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-600');
                btn.classList.add('border-amber-500', 'bg-amber-50', 'text-amber-700');
            }

            /* ── Zone selector ── */
            function selectPickleArea(zoneId, inputEl) {
                pickleState.zoneId = zoneId;
                document.querySelectorAll('.pickle-area-label').forEach(l => {
                    l.classList.remove('border-amber-500', 'bg-amber-50/50');
                    l.classList.add('border-gray-200', 'bg-white');
                });
                inputEl.closest('.pickle-area-label')
                    ?.classList.replace('border-gray-200', 'border-amber-500');
                inputEl.closest('.pickle-area-label')
                    ?.classList.replace('bg-white', 'bg-amber-50/50');
                document.getElementById('pickleZoneError')?.classList.add('hidden');
                document.getElementById('pickleDeliveryZoneSection')
                    ?.classList.remove('ring-2', 'ring-amber-400');
                calculatePickle();
            }

            /* ── GA4 helpers ── */
            function getPickleGaClientId() {
                const m = document.cookie.match(/_ga=GA\d+\.\d+\.(.+?)(?:;|$)/);
                return m ? m[1] : null;
            }

            function selectedPickleGa4Items() {
                const v = selectedPickleVariant();
                return [{
                    item_id: String(v?.sku ?? v?.id ?? ''),
                    item_name: @json($product->name),
                    item_variant: v?.title ?? null,
                    item_category: @json($product->category?->name ?? 'Pickle'),
                    price: Number(pickleUnitPrice()),
                    quantity: Number(pickleState.qty),
                    index: 0,
                }];
            }

            function pushPickleGa4(event, extra = {}) {
                const items = selectedPickleGa4Items();
                const value = items.reduce((s, i) => s + i.price * i.quantity, 0);
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    ecommerce: null
                });
                window.dataLayer.push({
                    event,
                    ecommerce: {
                        currency: 'BDT',
                        value,
                        items,
                        ...extra
                    }
                });
            }

            /* ── Submit ── */
            async function handlePickleOrder() {
                const btn = document.getElementById('pickleOrderBtn');
                const name = document.getElementById('pickleCustName').value.trim();
                const phone = document.getElementById('pickleCustPhone').value.trim();
                const address = document.getElementById('pickleCustAddress').value.trim();

                if (!name || !address) {
                    alert('অনুগ্রহ করে নাম ও ঠিকানা দিন।');
                    return;
                }
                if (!/^01[3-9]\d{8}$/.test(phone)) {
                    alert('সঠিক মোবাইল নম্বর দিন।');
                    return;
                }
                if (pickleState.zoneId === null) {
                    document.getElementById('pickleZoneError')?.classList.remove('hidden');
                    document.getElementById('pickleDeliveryZoneSection')
                        ?.classList.add('ring-2', 'ring-amber-400');
                    return;
                }

                pushPickleGa4('begin_checkout');
                pushPickleGa4('add_shipping_info', {
                    shipping_tier: String(pickleState.zoneId)
                });

                const orig = btn.innerHTML;
                btn.innerHTML =
                    '<svg class="animate-spin h-6 w-6 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> প্রসেসিং...';
                btn.disabled = true;

                try {
                    const res = await fetch(`/api/v1/landing/${PICKLE_SLUG}/checkout`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            customer_name: name,
                            customer_phone: phone,
                            address_line: address,
                            zone_id: pickleState.zoneId,
                            payment_method: 'cod',
                            items: [{
                                variant_id: pickleState.variantId,
                                quantity: pickleState.qty
                            }],
                            ga_client_id: getPickleGaClientId(),
                        }),
                    });
                    const json = await res.json();
                    if (json.success) {
                        document.getElementById('pickleSuccessModal').classList.remove('hidden');
                        setTimeout(() => {
                            window.location.href = json.data?.redirect_url || window.location.href;
                        }, 2500);
                    } else {
                        alert(json.message || 'অর্ডার সম্পন্ন করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                        btn.innerHTML = orig;
                        btn.disabled = false;
                    }
                } catch {
                    alert('নেটওয়ার্কজনিত সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                    btn.innerHTML = orig;
                    btn.disabled = false;
                }
            }

            /* ── Event wiring ── */
            document.querySelectorAll('.pickle-variant-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    pickleState.variantId = Number(btn.dataset.pickleVariantId);
                    setActivePickleVariant(btn);
                    renderPickleTierHints();
                    calculatePickle();
                });
            });

            document.getElementById('pickleQtyMinus')?.addEventListener('click', () => {
                pickleState.qty = Math.max(1, pickleState.qty - 1);
                renderPickleTierHints();
                calculatePickle();
            });
            document.getElementById('pickleQtyPlus')?.addEventListener('click', () => {
                pickleState.qty = Math.min(20, pickleState.qty + 1);
                renderPickleTierHints();
                calculatePickle();
            });
            document.querySelectorAll('input[name="pickle_area"]').forEach(input => {
                input.addEventListener('change', () =>
                    selectPickleArea(Number(input.value), input));
            });
            document.getElementById('pickleOrderBtn')
                ?.addEventListener('click', handlePickleOrder);

            /* ── Init ── */
            renderPickleTierHints();
            calculatePickle();
        </script>
    @endpush

@endsection
