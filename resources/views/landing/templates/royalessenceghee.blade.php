@extends('layouts.app')

@section('title', $landing->meta_title ?? $product->name)
@section('meta_description', $landing->meta_description ?? $product->short_description)

@php
    $defaultVariant = $product->variants->first();
    $certifications = $product->certifications->sortBy('sort_order')->values();
    $gheeVariants = $product->variants
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
            fn($zone) => [
                'id' => $zone->id,
                'name' => $zone->name,
                'charge' => (float) $zone->base_charge,
                'free_above' => (float) $zone->free_shipping_threshold,
            ],
        )
        ->values();
@endphp

@section('content')
    <section class="bg-[#fff8ea] border-b border-amber-100">
        <div class="max-w-8xl mx-auto px-4 py-14 md:py-20">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 items-center">
                <div class="md:col-span-7 text-center md:text-left">
                    <span
                        class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-100 text-amber-800 text-sm font-semibold mb-5 border border-amber-200">
                        <i class="fas fa-certificate text-amber-600"></i>
                        প্রিমিয়াম কোয়ালিটি | খাঁটি দেশি ঘি
                    </span>

                    <h1 class="text-4xl md:text-6xl font-black text-gray-950 leading-tight mb-6 font-hind">
                        রয়্যাল এসেন্স <span class="text-amber-700">ঘি</span> — আভিজাত্য ও
                        স্বাস্থ্যের এক অনন্য মিশেল!
                    </h1>

                    <p class="text-lg md:text-xl text-gray-700 mb-8 leading-relaxed font-hind">
                        দেশি মাঠের ঘাস খাওয়া গরুর দুধ থেকে তৈরি। অতুলনীয় ঘ্রাণ, ঘনত্ব ও
                        খাঁটি স্বাদ—যা আপনার প্রতিদিনের খাবারকে করবে আরও সুস্বাদু ও
                        পুষ্টিকর।
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="#checkout"
                            class="px-8 py-4 bg-amber-700 text-white font-bold rounded-xl shadow-lg shadow-amber-900/10 hover:bg-amber-800 transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            এখনই অর্ডার করুন
                        </a>
                        <a href="#about"
                            class="px-8 py-4 bg-white text-amber-800 font-bold rounded-xl border border-amber-200 hover:bg-amber-50 transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            বিস্তারিত জানুন
                        </a>
                    </div>
                </div>

                <div class="md:col-span-5 flex justify-center items-center">
                    <img src="{{ asset('assets/landing/ghee-jar.jpg') }}" alt="Royal Essence Ghee"
                        class="w-full max-w-sm aspect-4/5 object-cover rounded-2xl shadow-2xl shadow-amber-900/10 border-4 border-white" />
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="py-16 md:py-24 bg-white">
        <div class="max-w-8xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4 font-hind">
                    রয়্যাল এসেন্স ঘি: বিশুদ্ধতার অনন্য নিদর্শন
                </h2>
                <div class="w-20 h-1.5 bg-amber-600 mx-auto rounded-full mb-6"></div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto font-hind">
                    প্রতিটি ফোঁটায় মিশে আছে প্রকৃতির ছোঁয়া এবং সর্বোচ্চ গুণমান নিশ্চিত
                    করার নিশ্চয়তা।
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="flex justify-center">
                    <img src="{{ asset('assets/landing/ghee-texture.jpg') }}" alt="ঘি এর গঠন"
                        class="rounded-2xl shadow-lg w-full aspect-7/5 object-cover" />
                </div>

                <div class="space-y-6">
                    <div class="flex gap-4 rounded-2xl border border-amber-100 bg-amber-50/60 p-5">
                        <i class="fas fa-check-circle text-amber-700 text-2xl mt-1"></i>
                        <div>
                            <h4 class="text-lg font-bold text-gray-950 font-hind">
                                ১০০% খাঁটি ও প্রাকৃতিক
                            </h4>
                            <p class="text-gray-600 font-hind">
                                দেশি মাঠে বিচরণ করা গরুর দুধের ক্রিম থেকে সরাসরি প্রস্তুত।
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4 rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                        <i class="fas fa-check-circle text-amber-700 text-2xl mt-1"></i>
                        <div>
                            <h4 class="text-lg font-bold text-gray-950 font-hind">
                                প্রিমিয়াম মান নিশ্চিত
                            </h4>
                            <p class="text-gray-600 font-hind">
                                উৎপাদন সীমিত (প্রতিবারে সর্বোচ্চ ১০ কেজি), যাতে মানের কোনো আপস
                                না হয়।
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4 rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                        <i class="fas fa-check-circle text-amber-700 text-2xl mt-1"></i>
                        <div>
                            <h4 class="text-lg font-bold text-gray-950 font-hind">
                                কোনো কৃত্রিম উপাদান নেই
                            </h4>
                            <p class="text-gray-600 font-hind">
                                MSG মুক্ত, প্রিজারভেটিভ বিহীন এবং পুরোপুরি প্রাকৃতিক স্বাদ ও
                                ঘ্রাণ।
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4 rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                        <i class="fas fa-weight-hanging text-amber-700 text-2xl mt-1"></i>
                        <div>
                            <h4 class="text-lg font-bold text-gray-950 font-hind">
                                নিট ওজন
                            </h4>
                            <p class="text-gray-600 font-hind">৩৫০ গ্রাম (প্রায়)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="benefits" class="py-16 md:py-24 bg-[#fff8ea]">
        <div class="max-w-8xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4 font-hind">
                    স্বাস্থ্য উপকারিতা
                </h2>
                <div class="w-20 h-1.5 bg-amber-600 mx-auto rounded-full mb-6"></div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto font-hind">
                    প্রতিদিন রয়্যাল এসেন্স ঘি গ্রহণ করলে আপনার শরীরের সামগ্রিক পুষ্টি
                    নিশ্চিত হয় এবং নানাবিধ রোগ থেকে সুরক্ষা পাওয়া যায়।
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-amber-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        হজম ও রোগ প্রতিরোধ
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        পাচনতন্ত্র সুস্থ রাখে এবং শরীরের বিষাক্ত পদার্থ বের করে লিভার
                        পরিষ্কার রাখে।
                    </p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-amber-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-bone"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        হাড়ের শক্তি
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        ভিটামিন K2 ক্যালসিয়াম শোষণে সহায়তা করে, যা হাড়কে শক্তিশালী করে।
                    </p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-amber-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        মানসিক কার্যক্ষমতা
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        মস্তিষ্কের কার্যক্ষমতা বৃদ্ধি করে এবং মানসিক চাপ কমাতে সাহায্য
                        করে।
                    </p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-amber-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        ত্বক ও চুলের যত্ন
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        ত্বককে মসৃণ ও উজ্জ্বল করে এবং চুল পড়া কমিয়ে পুষ্টি জোগায়।
                    </p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-amber-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-burn"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        ওজন নিয়ন্ত্রণ
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        শরীরের কোলেস্টেরল বার্ন করতে সাহায্য করে এবং অতিরিক্ত চর্বি জমা
                        রোধ করে।
                    </p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-amber-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        সামগ্রিক পুষ্টি
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        ভিটামিন A, D, E ও K-এর ভালো উৎস যা সামগ্রিক পুষ্টি নিশ্চিত করে।
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="usage" class="py-16 md:py-24 bg-white">
        <div class="max-w-8xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4 font-hind">
                    ব্যবহারের নিয়মাবলী
                </h2>
                <div class="w-20 h-1.5 bg-amber-600 mx-auto rounded-full mb-6"></div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto font-hind">
                    রয়্যাল এসেন্স ঘি আপনার প্রতিদিনের খাদ্যতালিকায় অন্তর্ভুক্ত করার কিছু
                    সহজ উপায়।
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="flex gap-4 bg-amber-50 p-6 rounded-2xl border border-amber-100">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-amber-700 text-white rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-sun"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-950 mb-1 font-hind">
                            সকাল শুরু করুন সতেজতায়
                        </h4>
                        <p class="text-gray-700 font-hind">
                            প্রতিদিন সকালে এক গ্লাস গরম পানির সাথে এক চামচ ঘি মিশিয়ে খেলে
                            চুল পড়া সমস্যা ধীরে ধীরে কমে যায় এবং চর্বি জমা রোধ করে।
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 bg-amber-50 p-6 rounded-2xl border border-amber-100">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-amber-700 text-white rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-mug-hot"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-950 mb-1 font-hind">
                            সুস্বাস্থ্য ও শক্তি
                        </h4>
                        <p class="text-gray-700 font-hind">
                            এক গ্লাস গরম দুধের সাথে এক চামচ ঘি মিশিয়ে খেলে শরীরের শক্তি
                            বাড়ে এবং হজম ক্ষমতা উন্নত হয়।
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 bg-amber-50 p-6 rounded-2xl border border-amber-100">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-amber-700 text-white rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-cookie-bite"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-950 mb-1 font-hind">
                            খাবারের স্বাদ বাড়াতে
                        </h4>
                        <p class="text-gray-700 font-hind">
                            তরকারি রান্নায় অথবা রুটি বা পরোটার সাথে ঘি মিশিয়ে খেলে খাবারের
                            স্বাদ অতুলনীয় হয়ে ওঠে।
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 bg-amber-50 p-6 rounded-2xl border border-amber-100">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-amber-700 text-white rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-wind"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-950 mb-1 font-hind">
                            শীতকালীন সুরক্ষা
                        </h4>
                        <p class="text-gray-700 font-hind">
                            শীতকালে ঘি এর সাথে সামান্য গোল মরিচ মিশিয়ে খেলে ঠাণ্ডাজনিত
                            সমস্যা দূর হয়।
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="why-us" class="py-16 md:py-24 bg-amber-900 text-white">
        <div class="max-w-8xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="flex justify-center">
                    <img src="{{ asset('assets/landing/farm-cows.jpg') }}" alt="ঘি তৈরির প্রক্রিয়া"
                        class="rounded-2xl shadow-2xl w-full aspect-7/5 object-cover border-4 border-white/10" />
                </div>

                <div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 font-hind">
                        কেন বেছে নেবেন রয়্যাল এসেন্স ঘি?
                    </h2>
                    <div class="w-20 h-1.5 bg-amber-300 rounded-full mb-8"></div>

                    <p class="text-lg leading-relaxed mb-6 font-hind text-white/90">
                        আমাদের এই ঘি তৈরি করা হয় দেশি মাঠে বিচরণ করা, কাঁচা ঘাস খাওয়া,
                        রোদে পোড়া গরুর দুধ থেকে এবং সেই দুধ ক্রিম তৈরি করে ঘি বানানো হয়।
                    </p>

                    <div class="bg-white/10 p-6 rounded-2xl border border-white/20 backdrop-blur-sm">
                        <p class="text-lg font-semibold mb-3 font-hind text-amber-200">
                            <i class="fas fa-check-circle mr-2"></i>সীমাবদ্ধ উৎপাদন,
                            সর্বোচ্চ গুণমান
                        </p>
                        <p class="text-white/80 font-hind">
                            আমরা একসাথে দশ কেজির বেশি ঘি তৈরি করতে পারি না। প্রোডাকশন সীমিত
                            রাখি যেন প্রতিটি জারে ঘি এর মান ও বিশুদ্ধতা অটুট থাকে।
                            বাংলাদেশের সবচেয়ে প্রিমিয়াম কোয়ালিটি আমরাই দিচ্ছি।
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="certifications" class="py-16 md:py-24 bg-white">
        <div class="max-w-8xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="text-amber-700 font-bold tracking-[0.2em] text-xs uppercase">Quality Assurance</span>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mt-3 mb-4 font-hind">
                    আন্তর্জাতিক মানের নিশ্চয়তা
                </h2>
                <div class="w-20 h-1.5 bg-amber-600 mx-auto rounded-full mb-6"></div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto font-hind">
                    রয়্যাল এসেন্স ঘি কঠোর মাননিয়ন্ত্রণ প্রক্রিয়া পার হয়ে আপনার কাছে
                    পৌঁছায়। আমাদের বিশ্বাসযোগ্যতার সনদসমূহ:
                </p>
            </div>

            @if ($certifications->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                    @foreach ($certifications as $cert)
                        <div
                            class="group flex flex-col items-center text-center rounded-2xl border border-amber-100 bg-[#fffaf0] p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <div class="h-20 w-20 rounded-2xl bg-white border border-amber-100 p-3 flex items-center justify-center">
                                @if ($cert->logo_path)
                                    <img src="{{ $cert->logo_url }}" alt="{{ $cert->name }}"
                                        class="max-h-full max-w-full object-contain transition-transform duration-300 group-hover:scale-105"
                                        loading="lazy" />
                                @else
                                    <i class="fas fa-certificate text-3xl text-amber-600"></i>
                                @endif
                            </div>
                            <span class="mt-4 text-sm font-bold text-gray-900">{{ $cert->name }}</span>
                            @if ($cert->category)
                                <span class="mt-1 text-xs text-gray-500">{{ $cert->category }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-amber-100 bg-[#fffaf0] p-8 text-center">
                    <i class="fas fa-certificate text-3xl text-amber-600 mb-3"></i>
                    <p class="font-semibold text-gray-800">এই পণ্যের সার্টিফিকেশন শীঘ্রই যুক্ত হবে।</p>
                </div>
            @endif
        </div>
    </section>

    <section id="checkout" class="py-16 bg-gray-50 font-noto relative min-h-screen">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-10">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 mb-2">
                            পণ্য নির্বাচন করুন
                        </h2>
                        <p class="text-gray-500">
                            পরিমাণ নির্বাচন করুন, তারপর সঠিক তথ্য দিয়ে অর্ডার কনফার্ম করুন।
                        </p>
                    </div>

                    <div
                        class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 transition hover:shadow-md hover:border-red-100">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('assets/landing/ghee-jar.jpg') }}"
                                class="w-16 h-16 rounded-xl object-cover shrink-0 hover:scale-105 transition-transform duration-300"
                                alt="{{ $product->name }}" />

                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ $product->name }}</h3>
                                <p id="gheePrice" class="text-red-600 font-bold mt-0.5">৳০</p>

                                @if ($product->variants->count() > 1)
                                    <div class="flex gap-2 mt-2 flex-wrap">
                                        @foreach ($product->variants as $variant)
                                            <button type="button"
                                                data-ghee-variant-id="{{ $variant->id }}"
                                                class="ghee-variant-btn {{ $loop->first ? 'border-red-400 bg-red-50 text-red-700' : 'border-gray-200 bg-white text-gray-600 hover:border-red-300' }} px-2 py-1 text-xs rounded border font-semibold transition">
                                                {{ $variant->title }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                <div id="gheeTierHints" class="flex gap-2 mt-2 flex-wrap"></div>
                            </div>

                            <div class="flex items-center gap-2 bg-red-50 p-2 rounded shrink-0">
                                <button id="gheeQtyMinus" type="button"
                                    class="w-6 h-6 bg-gray-100 hover:bg-red-50 hover:text-red-600 rounded-xl font-bold transition">−</button>
                                <span id="gheeQty" class="w-6 text-center font-bold text-gray-700">1</span>
                                <button id="gheeQtyPlus" type="button"
                                    class="w-6 h-6 bg-red-600 hover:bg-red-700 text-white rounded font-bold transition">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 font-noto md:block hidden mt-6">
                        <h4 class="text-xl font-bold text-gray-900">অর্ডারের নিয়মাবলী:</h4>
                        <ul class="space-y-3 text-gray-600">
                            <li class="flex items-start gap-3 border-brand-500 bg-red-50/50">
                                <i class="fas fa-truck text-brand-600 mt-1"></i>
                                <span id="gheeTierRuleText">ডেলিভারি চার্জ এলাকা অনুযায়ী প্রযোজ্য।</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="w-full">
                    <div class="bg-white border border-red-100 rounded-3xl p-6 md:p-10 shadow-xl shadow-red-100/50">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-gray-900">
                                অর্ডার কনফার্ম করুন
                            </h2>
                            <p class="text-red-600 text-sm italic mt-2">
                                সঠিক তথ্য দিয়ে নিচের ফর্মটি পূরণ করুন।
                            </p>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" id="gheeCustName" placeholder="আপনার নাম *"
                                    class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-1 focus:ring-red-200 outline-none text-sm transition-all" />
                                <input type="tel" id="gheeCustPhone" placeholder="মোবাইল নম্বর *"
                                    class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-1 focus:ring-red-200 outline-none text-sm transition-all" />
                            </div>

                            <input type="text" id="gheeCustAddress"
                                placeholder="পূর্ণ ঠিকানা (বাসা নম্বর, রোড, এলাকা, জেলা) *"
                                class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-1 focus:ring-red-200 outline-none text-sm transition-all" />

                            <div
                                class="p-4 bg-orange-50 border border-dashed border-orange-300 rounded-xl flex items-center justify-between mt-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-3 w-3 relative">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                                    </span>
                                    <p class="text-xs md:text-sm text-amber-900">
                                        আরও বেশি বা পাইকারি নিতে চান?
                                    </p>
                                </div>
                                <a href="tel:01334943783"
                                    class="text-sm md:text-base font-black text-pink-700 hover:underline">কল করুন: 01334
                                    943783</a>
                            </div>

                            <div id="gheeDeliveryZoneSection"
                                class="bg-red-50/50 p-4 rounded-xl border border-red-100 mt-4 transition-all">
                                <p class="text-sm font-bold text-gray-700 mb-1">
                                    ডেলিভারি এলাকা নির্বাচন করুন
                                    <span class="text-red-500">*</span>
                                </p>
                                <p id="gheeZoneError" class="hidden text-xs text-red-600 mb-2 font-medium">
                                    অনুগ্রহ করে একটি ডেলিভারি এলাকা নির্বাচন করুন।
                                </p>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach ($zones as $zone)
                                        <label
                                            class="flex flex-col items-center p-2 border rounded-lg cursor-pointer text-center transition-all ghee-area-label border-gray-200 bg-white hover:border-red-400">
                                            <input type="radio" name="ghee_area" value="{{ $zone->id }}"
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

                            <div
                                class="p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300 space-y-2 text-sm mt-4">
                                <div id="gheeBreakdown" class="space-y-1 pb-2 border-b border-gray-200"></div>

                                <div class="flex justify-between">
                                    <span class="text-gray-500">পণ্যের মূল্য:</span>
                                    <span class="font-semibold">৳ <span id="gheeSubtotal">০</span></span>
                                </div>
                                <div id="gheeTierDiscountRow" class="hidden justify-between">
                                    <span class="text-gray-500">পরিমাণভিত্তিক ছাড়:</span>
                                    <span class="font-semibold text-green-600">-৳ <span
                                            id="gheeTierDiscount">০</span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>ডেলিভারি চার্জ:</span>
                                    <span id="gheeShippingDisplay" class="text-red-600 font-bold">—</span>
                                </div>
                                <div class="flex justify-between border-t pt-2 mt-2 font-black text-lg text-red-600">
                                    <span>সর্বমোট:</span>
                                    <span>৳ <span id="gheeTotal">০</span></span>
                                </div>
                            </div>

                            <button id="gheeOrderBtn"
                                class="w-full mt-4 py-5 bg-red-600 hover:bg-red-700 text-white font-bold text-xl rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-3">
                                অর্ডার কনফার্ম করুন
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4 font-noto block md:hidden mt-6">
                        <h4 class="text-xl font-bold text-gray-900">অর্ডারের নিয়মাবলী:</h4>
                        <ul class="space-y-3 text-gray-600">
                            <li class="flex items-start gap-3 border-brand-500 bg-red-50/50">
                                <i class="fas fa-truck text-brand-600 mt-1"></i>
                                <span id="gheeTierRuleTextMobile">ডেলিভারি চার্জ এলাকা অনুযায়ী প্রযোজ্য।</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="gheeSuccessModal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 px-4">
            <div
                class="bg-white p-8 rounded-3xl max-w-sm w-full text-center shadow-2xl transform transition-all border border-red-100">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 mb-2">
                    অর্ডার সফল হয়েছে!
                </h2>
                <p class="text-gray-500 mb-6">
                    আমাদের প্রতিনিধি দ্রুতই আপনার সাথে যোগাযোগ করবেন।
                </p>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            const GHEE_VARIANTS = @json($gheeVariants);
            const GHEE_ZONES = @json($jsZones);
            const GHEE_SLUG = '{{ $landing->slug }}';
            const gheeState = {
                variantId: {{ $defaultVariant?->id ?? 'null' }},
                qty: 1,
                zoneId: null,
                shippingCost: 0,
            };

            function gheeText(id, value) {
                const el = document.getElementById(id);
                if (el) el.innerText = value;
            }

            function selectedGheeVariant() {
                return GHEE_VARIANTS.find(v => Number(v.id) === Number(gheeState.variantId)) ?? GHEE_VARIANTS[0];
            }

            function activeGheeTier() {
                const variant = selectedGheeVariant();
                return [...(variant?.tiers ?? [])]
                    .filter(tier => gheeState.qty >= Number(tier.min_quantity))
                    .sort((a, b) => Number(b.min_quantity) - Number(a.min_quantity))[0] ?? null;
            }

            function gheeUnitPrice() {
                const variant = selectedGheeVariant();
                const tier = activeGheeTier();
                const base = Number(variant?.final_price ?? variant?.price ?? 0);
                if (!tier || Number(tier.discount_value) <= 0) return base;
                if (tier.discount_type === 'percentage') return Math.max(0, base - (base * Number(tier.discount_value) / 100));
                return Math.max(0, base - Number(tier.discount_value));
            }

            function gheeRegularUnitPrice() {
                const variant = selectedGheeVariant();
                return Number(variant?.final_price ?? variant?.price ?? 0);
            }

            function gheeTierDiscount() {
                return Math.max(0, (gheeRegularUnitPrice() - gheeUnitPrice()) * gheeState.qty);
            }

            function gheeSubtotal() {
                return gheeUnitPrice() * gheeState.qty;
            }

            function gheeShipping() {
                if (gheeState.zoneId === null) return 0;
                const tier = activeGheeTier();
                if (tier?.has_free_delivery) return 0;
                const zone = GHEE_ZONES.find(z => Number(z.id) === Number(gheeState.zoneId));
                if (!zone) return 0;
                return zone.free_above > 0 && gheeSubtotal() >= zone.free_above ? 0 : Number(zone.charge ?? 0);
            }

            function renderGheeTierHints() {
                const variant = selectedGheeVariant();
                const hints = document.getElementById('gheeTierHints');
                if (!hints) return;
                const tiers = variant?.tiers ?? [];
                hints.innerHTML = tiers.map(tier => {
                    const parts = [];
                    if (Number(tier.discount_value) > 0) {
                        const label = tier.discount_type === 'percentage'
                            ? `${Number(tier.discount_value).toFixed(0)}% ছাড়`
                            : `৳${Number(tier.discount_value).toFixed(0)} ছাড়`;
                        parts.push(label);
                    }
                    if (tier.has_free_delivery) parts.push('ফ্রি ডেলিভারি');
                    if (!parts.length) parts.push('স্পেশাল অফার');
                    return `<span class="text-xs bg-red-50 text-red-700 border border-red-200 rounded-full px-2 py-1 font-semibold">${tier.min_quantity}+ নিলে ${parts.join(' + ')}</span>`;
                }).join('');

                const firstFreeDeliveryTier = tiers.find(tier => tier.has_free_delivery);
                const rule = firstFreeDeliveryTier
                    ? `${firstFreeDeliveryTier.min_quantity}+ জার অর্ডার করলে ডেলিভারি ফ্রি!`
                    : 'ডেলিভারি চার্জ এলাকা অনুযায়ী প্রযোজ্য।';
                gheeText('gheeTierRuleText', rule);
                gheeText('gheeTierRuleTextMobile', rule);
            }

            function calculateGhee() {
                const variant = selectedGheeVariant();
                const unit = gheeUnitPrice();
                const regular = gheeRegularUnitPrice();
                const subtotal = gheeSubtotal();
                const discount = gheeTierDiscount();
                const shipping = gheeShipping();
                gheeState.shippingCost = shipping;

                gheeText('gheeQty', gheeState.qty);
                gheeText('gheePrice', `৳${unit.toFixed(0)}${regular > unit ? ` (৳${regular.toFixed(0)} থেকে)` : ''}`);
                gheeText('gheeSubtotal', subtotal.toFixed(0));
                gheeText('gheeTierDiscount', discount.toFixed(0));
                gheeText('gheeShippingDisplay', gheeState.zoneId === null ? '—' : (shipping === 0 ? 'ফ্রি' : `৳ ${shipping.toFixed(0)}`));
                gheeText('gheeTotal', (subtotal + (gheeState.zoneId === null ? 0 : shipping)).toFixed(0));

                const tierDiscountRow = document.getElementById('gheeTierDiscountRow');
                tierDiscountRow?.classList.toggle('hidden', discount <= 0);
                tierDiscountRow?.classList.toggle('flex', discount > 0);

                const breakdown = document.getElementById('gheeBreakdown');
                if (breakdown) {
                    breakdown.innerHTML =
                        `<div class="flex justify-between text-xs text-gray-600"><span class="truncate pr-2">${variant?.title ?? 'Ghee'} × ${gheeState.qty}</span><span class="font-semibold text-gray-700 whitespace-nowrap">৳ ${subtotal.toFixed(0)}</span></div>`;
                }
            }

            function setActiveGheeVariant(button) {
                document.querySelectorAll('.ghee-variant-btn').forEach(btn => {
                    btn.classList.remove('border-red-400', 'bg-red-50', 'text-red-700');
                    btn.classList.add('border-gray-200', 'bg-white', 'text-gray-600');
                });
                button.classList.remove('border-gray-200', 'bg-white', 'text-gray-600');
                button.classList.add('border-red-400', 'bg-red-50', 'text-red-700');
            }

            function selectGheeArea(zoneId, inputEl) {
                gheeState.zoneId = zoneId;
                document.querySelectorAll('.ghee-area-label').forEach(label => {
                    label.classList.remove('border-red-500', 'bg-red-50/50');
                    label.classList.add('border-gray-200', 'bg-white');
                });
                inputEl.closest('.ghee-area-label')?.classList.remove('border-gray-200', 'bg-white');
                inputEl.closest('.ghee-area-label')?.classList.add('border-red-500', 'bg-red-50/50');
                document.getElementById('gheeZoneError')?.classList.add('hidden');
                document.getElementById('gheeDeliveryZoneSection')?.classList.remove('ring-2', 'ring-red-400');
                calculateGhee();
            }

            function getGheeGaClientId() {
                const gaMatch = document.cookie.match(/_ga=GA\d+\.\d+\.(.+?)(?:;|$)/);
                return gaMatch ? gaMatch[1] : null;
            }

            function selectedGheeGa4Items() {
                const variant = selectedGheeVariant();
                return [{
                    item_id: String(variant?.sku ?? variant?.id ?? ''),
                    item_name: @json($product->name),
                    item_variant: variant?.title ?? null,
                    item_category: @json($product->category?->name ?? 'Ghee'),
                    price: Number(gheeUnitPrice()),
                    quantity: Number(gheeState.qty),
                    index: 0,
                }];
            }

            function pushGheeGa4(event, extra = {}) {
                const items = selectedGheeGa4Items();
                const value = items.reduce((sum, item) => sum + (Number(item.price) * Number(item.quantity)), 0);
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({ ecommerce: null });
                window.dataLayer.push({
                    event,
                    ecommerce: {
                        currency: 'BDT',
                        value,
                        items,
                        ...extra,
                    },
                });
            }

            async function handleGheeOrder() {
                const btn = document.getElementById('gheeOrderBtn');
                const name = document.getElementById('gheeCustName').value.trim();
                const phone = document.getElementById('gheeCustPhone').value.trim();
                const address = document.getElementById('gheeCustAddress').value.trim();

                if (!name || !address) {
                    alert('অনুগ্রহ করে নাম ও ঠিকানা দিন।');
                    return;
                }
                if (!/^01[3-9]\d{8}$/.test(phone)) {
                    alert('সঠিক মোবাইল নম্বর দিন।');
                    return;
                }
                if (gheeState.zoneId === null) {
                    document.getElementById('gheeZoneError')?.classList.remove('hidden');
                    document.getElementById('gheeDeliveryZoneSection')?.classList.add('ring-2', 'ring-red-400');
                    return;
                }

                pushGheeGa4('begin_checkout');
                pushGheeGa4('add_shipping_info', {
                    shipping_tier: String(gheeState.zoneId),
                });

                const originalHTML = btn.innerHTML;
                btn.innerHTML =
                    '<svg class="animate-spin h-6 w-6 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> প্রসেসিং...';
                btn.disabled = true;

                try {
                    const res = await fetch(`/api/v1/landing/${GHEE_SLUG}/checkout`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            customer_name: name,
                            customer_phone: phone,
                            address_line: address,
                            zone_id: gheeState.zoneId,
                            payment_method: 'cod',
                            items: [{
                                variant_id: gheeState.variantId,
                                quantity: gheeState.qty,
                            }],
                            ga_client_id: getGheeGaClientId(),
                        })
                    });
                    const json = await res.json();
                    if (json.success) {
                        document.getElementById('gheeSuccessModal').classList.remove('hidden');
                        setTimeout(() => {
                            window.location.href = json.data?.redirect_url || window.location.href;
                        }, 2500);
                    } else {
                        alert(json.message || 'অর্ডার সম্পন্ন করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                    }
                } catch (e) {
                    alert('নেটওয়ার্কজনিত সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            }

            document.querySelectorAll('.ghee-variant-btn').forEach(button => {
                button.addEventListener('click', () => {
                    gheeState.variantId = Number(button.dataset.gheeVariantId);
                    setActiveGheeVariant(button);
                    renderGheeTierHints();
                    calculateGhee();
                });
            });

            document.getElementById('gheeQtyMinus')?.addEventListener('click', () => {
                gheeState.qty = Math.max(1, gheeState.qty - 1);
                calculateGhee();
            });
            document.getElementById('gheeQtyPlus')?.addEventListener('click', () => {
                gheeState.qty = Math.min(10, gheeState.qty + 1);
                calculateGhee();
            });
            document.querySelectorAll('input[name="ghee_area"]').forEach(input => {
                input.addEventListener('change', () => selectGheeArea(Number(input.value), input));
            });
            document.getElementById('gheeOrderBtn')?.addEventListener('click', handleGheeOrder);

            renderGheeTierHints();
            calculateGhee();
        </script>
    @endpush
@endsection
