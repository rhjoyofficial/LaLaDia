@extends('layouts.app')

@section('title', $landing->meta_title ?? $landing->title)
@section('meta_description', $landing->meta_description ?? $landing->content)

@php
    // Group items by product_id — each product becomes one card with multiple weight options
    $productGroups = $salesItems
        ->filter(fn($i) => $i->variant && $i->variant->product)
        ->groupBy(fn($i) => $i->variant->product_id)
        ->map(
            fn($items) => [
                'product' => $items->first()->variant->product,
                'variants' => $items->sortBy(fn($i) => $i->variant->weight_grams)->values(),
            ],
        )
        ->values();

    // JS-friendly grouped items (one entry per product, weights = all variants)
    $jsItems = $productGroups
        ->map(
            fn($g) => [
                'key' => 'prod_' . $g['product']->id,
                'name' => $g['product']->name,
                'image' => $g['product']->image_url ?? '',
                'weights' => $g['variants']
                    ->map(
                        fn($item) => [
                            'item_id' => $item->id,
                            'variant_id' => $item->variant->id,
                            'label' => $item->variant->title,
                            'price' => (float) $item->variant->price,
                        ],
                    )
                    ->values()
                    ->toArray(),
            ],
        )
        ->values()
        ->toArray();

    $jsZones = $zones
        ->map(
            fn($z) => [
                'id' => $z->id,
                'name' => $z->name,
                'charge' => (float) $z->base_charge,
                'free_above' => (float) $z->free_shipping_threshold,
            ],
        )
        ->values()
        ->toArray();
@endphp

@section('content')
    <section class="relative bg-white py-16 md:py-24 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left order-2 lg:order-1">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-50 border border-green-200 text-primary font-medium text-sm mb-6 animate-pulse">
                        <i class="fa-solid fa-leaf"></i>
                        <span>শতভাগ রাসায়নিকমুক্ত ও প্রাকৃতিক</span>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-snug text-dark mb-6 font-hind">
                        সমুদ্র উপকূলের সুস্বাদু <br />
                        <span class="text-primary">নিরাপদ শুঁটকি মাছ!</span>
                    </h1>

                    <p class="text-lg md:text-xl text-gray-600 mb-8 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        বিষাক্ত কেমিক্যালের ভয় ছাড়া, গরম ভাত বা রুটির সাথে উপভোগ করুন
                        সাগরের আসল স্বাদ। নিজস্ব 'গ্রিন মাচা' ও ভ্যাকুয়াম প্যাকেজিংয়ে
                        প্রস্তুতকৃত
                        <strong class="italic">লা লা দিয়া</strong> শুঁটকি আপনার পরিবারের
                        সুস্বাস্থ্যের নিশ্চয়তা।
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="#checkout"
                            class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white bg-primary rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 w-full sm:w-auto animate-shake hover:animate-none"
                            aria-label="অর্ডার করতে চেকআউট সেকশনে যান">
                            <span
                                class="absolute inset-0 w-full h-full -mt-1 rounded-lg opacity-30 bg-linear-to-b from-transparent via-transparent to-black"></span>
                            <span class="relative flex items-center gap-2">
                                <i class="fa-solid fa-cart-shopping group-hover:animate-bounce"></i>
                                এখনই অর্ডার করুন
                            </span>
                        </a>

                        <div class="flex items-center gap-3 text-sm font-medium text-gray-500 mt-4 sm:mt-0">
                            <i class="fa-solid fa-check-circle text-primary text-xl"></i>
                            <span>PSTU ও BRiCM দ্বারা পরীক্ষিত</span>
                        </div>
                    </div>
                </div>

                <div class="order-1 lg:order-2 relative">
                    <div
                        class="absolute inset-0 bg-green-100 rounded-full blur-3xl opacity-50 transform translate-x-10 -translate-y-10">
                    </div>

                    <div
                        class="relative w-full overflow-hidden rounded-2xl shadow-2xl aspect-4/3 bg-gray-200 ring-4 ring-white">
                        <img src="{{ asset('assets/landing/dryfish-hero.jpg') }}" alt="গরম ভাতের সাথে সুস্বাদু শুঁটকি ভুনা"
                            loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-700 hover:scale-105" />
                        <div
                            class="absolute bottom-6 left-6 bg-white/90 backdrop-blur-sm p-4 rounded-xl shadow-lg border border-gray-100 flex items-center gap-3 animate-bounce-slow">
                            <div
                                class="w-12 h-12 bg-secondary rounded-full flex items-center justify-center text-white text-xl">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">
                                    গ্যারান্টি
                                </p>
                                <p class="text-dark font-bold">DDT ও বিষমুক্ত</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 md:py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="w-full lg:w-1/2 order-2 lg:order-1">
                    <div class="relative">
                        <div
                            class="absolute -bottom-6 -left-6 w-full h-full border-2 border-primary/20 rounded-2xl hidden md:block">
                        </div>

                        <div class="relative aspect-video md:aspect-4/3 rounded-2xl overflow-hidden shadow-2xl group">
                            <img src="{{ asset('assets/landing/dryfish-coast.jpg') }}" alt="ল্যালদিয়া সমুদ্র উপকূলের দৃশ্য"
                                loading="lazy"
                                class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                            <div class="absolute inset-0 bg-linear-to-t from-dark/60 to-transparent flex items-end p-6">
                                <p class="text-white font-medium flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-secondary"></i>
                                    পাথরঘাটা, বরগুনা — ল্যালদিয়া উপকূল
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/2 order-1 lg:order-2">
                    <div class="space-y-6">
                        <span
                            class="text-primary font-bold tracking-widest uppercase text-sm border-b-2 border-primary/30 pb-1">আমাদের
                            গল্পের শুরু</span>
                        <h2 class="text-3xl md:text-4xl font-bold text-dark font-noto leading-tight">
                            সরাসরি সাগর থেকে আপনার <br />
                            <span class="text-primary">খাবার টেবিল পর্যন্ত যাত্রা</span>
                        </h2>

                        <p class="text-lg text-gray-600 leading-relaxed">
                            <strong class="italic">"লা লা দিয়া" </strong> শুধু একটি নাম নয়,
                            এটি সমুদ্রের গভীর থেকে আসা সতেজতার প্রতিশ্রুতি। বরগুনার
                            পাথরঘাটার প্রমত্তা সাগরের মোহনায়, যেখানে ল্যালদিয়ার চর জেগে
                            ওঠে, সেখান থেকেই আমাদের প্রতিটি মাছ সংগৃহীত হয়।
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
                            <div
                                class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl hover:bg-green-50 transition-colors">
                                <div
                                    class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-primary">
                                    <i class="fa-solid fa-anchor"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-dark">সরাসরি উৎস</h4>
                                    <p class="text-xs text-gray-500">
                                        জেলেদের থেকে সরাসরি সংগ্রহ
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl hover:bg-green-50 transition-colors">
                                <div
                                    class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-primary">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-dark">দ্রুত প্রক্রিয়াজাত</h4>
                                    <p class="text-xs text-gray-500">
                                        সতেজতা ধরে রাখতে তৎক্ষণাৎ ব্যবস্থা
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6">
                            <a href="#checkout"
                                class="inline-flex items-center gap-3 px-8 py-3 bg-secondary hover:bg-orange-500 text-white font-bold rounded-full shadow-lg shadow-secondary/30 transition-all hover:-translate-y-1 active:scale-95 animate-shake hover:animate-none">
                                <span>সরাসরি সাগরের স্বাদ নিন</span>
                                <i class="fa-solid fa-chevron-right text-sm animate-pulse"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 md:py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-dark font-noto mb-4">
                    সব শুঁটকি কি
                    <span class="text-red-500 underline decoration-wavy">নিরাপদ?</span>
                </h2>
                <p class="text-lg text-gray-600">
                    বাজারে পাওয়া অধিকাংশ শুঁটকি সস্তা হলেও সেগুলো স্বাস্থ্যের জন্য
                    অত্যন্ত ঝুঁকিপূর্ণ। এক নজরে দেখে নিন বাজারের সাধারণ শুঁটকি বনাম
                    <strong class="italic">"লা লা দিয়া" </strong>-র পার্থক্য।
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
                <div class="bg-red-50 border-2 border-red-100 rounded-3xl p-6 md:p-10 transition-all hover:shadow-lg">
                    <div class="flex items-center gap-4 mb-8">
                        <div
                            class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-2xl shrink-0">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-red-800 font-noto">
                            বাজারে প্রচলিত সাধারণ শুঁটকি
                        </h3>
                    </div>

                    <ul class="space-y-6">
                        <li class="flex gap-4">
                            <i class="fa-solid fa-circle-xmark text-red-500 text-xl mt-1 shrink-0"></i>
                            <div>
                                <p class="font-bold text-red-900">নিম্নমানের ও পচা মাছ:</p>
                                <p class="text-red-700 text-sm">
                                    সাধারণত পচা বা নষ্ট হয়ে যাওয়া মাছ দিয়ে শুঁটকি তৈরি করা হয় যা
                                    অস্বাস্থ্যকর।
                                </p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <i class="fa-solid fa-circle-xmark text-red-500 text-xl mt-1 shrink-0"></i>
                            <div>
                                <p class="font-bold text-red-900">
                                    বিষাক্ত কেমিক্যাল (DDT/নগজ):
                                </p>
                                <p class="text-red-700 text-sm">
                                    পোকামাকড় ঠেকাতে DDT বা নগজ ব্যবহার করা হয়, যা মানবদেহে
                                    ক্যান্সারের ঝুঁকি বাড়ায়।
                                </p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <i class="fa-solid fa-circle-xmark text-red-500 text-xl mt-1 shrink-0"></i>
                            <div>
                                <p class="font-bold text-red-900">
                                    নোংরা পরিবেশ ও মাটির স্পর্শ:
                                </p>
                                <p class="text-red-700 text-sm">
                                    সরাসরি মাটির ওপর ফেলে শুকানো হয়, ফলে প্রচুর ধুলোবালি ও
                                    ব্যাকটেরিয়া থাকে।
                                </p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <i class="fa-solid fa-circle-xmark text-red-500 text-xl mt-1 shrink-0"></i>
                            <div>
                                <p class="font-bold text-red-900">অস্বাভাবিক কম দাম:</p>
                                <p class="text-red-700 text-sm">
                                    নিম্নমানের উপাদানের কারণে সস্তা মনে হলেও প্রকৃতপক্ষে এটি
                                    স্বাস্থ্যের জন্য ক্ষতিকর।
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div
                    class="bg-green-50 border-2 border-primary rounded-3xl p-6 md:p-10 relative overflow-hidden transition-all hover:shadow-xl group">
                    <div
                        class="absolute top-0 right-0 bg-primary text-white px-6 py-2 rounded-bl-3xl font-bold text-sm uppercase tracking-widest animate-pulse">
                        Best Quality
                    </div>

                    <div class="flex items-center gap-4 mb-8">
                        <div
                            class="w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center text-2xl shrink-0">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-green-900 font-noto">
                            লা লা দিয়া - প্রিমিয়াম শুঁটকি
                        </h3>
                    </div>

                    <ul class="space-y-6">
                        <li class="flex gap-4">
                            <i class="fa-solid fa-circle-check text-primary text-xl mt-1 shrink-0"></i>
                            <div>
                                <p class="font-bold text-green-950">টাটকা সাগরের মাছ:</p>
                                <p class="text-green-800 text-sm">
                                    সরাসরি সাগর থেকে আসা টাটকা মাছ সংগ্রহ করে তৎক্ষণাৎ
                                    প্রক্রিয়াজাত করা হয়।
                                </p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <i class="fa-solid fa-circle-check text-primary text-xl mt-1 shrink-0"></i>
                            <div>
                                <p class="font-bold text-green-950">
                                    প্রাকৃতিক উপায়ে সংরক্ষণ:
                                </p>
                                <p class="text-green-800 text-sm">
                                    কোনো বিষ ব্যবহার করা হয় না। পরিবর্তে প্রাকৃতিক হলুদ ও মরিচের
                                    গুঁড়া ব্যবহার করা হয়।
                                </p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <i class="fa-solid fa-circle-check text-primary text-xl mt-1 shrink-0"></i>
                            <div>
                                <p class="font-bold text-green-950">
                                    গ্রিন মাচা ও ড্রায়ার প্রযুক্তি:
                                </p>
                                <p class="text-green-800 text-sm">
                                    মাটি থেকে ৮ ফুট উপরে ও নিয়ন্ত্রিত পরিবেশে শুকানো হয়, যা
                                    ধুলোবালি ও মাছিমুক্ত।
                                </p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <i class="fa-solid fa-circle-check text-primary text-xl mt-1 shrink-0"></i>
                            <div>
                                <p class="font-bold text-green-950">
                                    ল্যাব টেস্টেড ও নিরাপদ:
                                </p>
                                <p class="text-green-800 text-sm">
                                    BSTI স্ট্যান্ডার্ড ও ল্যাব পরীক্ষার মাধ্যমে ১০০% বিশুদ্ধতা
                                    নিশ্চিত করা হয়।
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-16 text-center">
                <a href="#checkout"
                    class="group relative inline-flex items-center justify-center px-10 py-4 text-lg font-extrabold text-white bg-primary rounded-full overflow-hidden shadow-xl hover:scale-105 transition-all duration-300 animate-shake hover:animate-none">
                    <span class="relative flex items-center gap-3">
                        নিরাপদ শুঁটকি কিনতে এখানে ক্লিক করুন
                        <i class="fa-solid fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                    </span>
                </a>
                <p class="mt-4 text-sm text-gray-500 font-medium italic">
                    *আপনার স্বাস্থ্য আমাদের অগ্রাধিকার। সস্তা বিষ এড়িয়ে চলুন।
                </p>
            </div>
        </div>

        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-green-50 rounded-full blur-3xl opacity-60"></div>
    </section>

    <section id="natural-promise" class="py-16 md:py-24 bg-amber-50/50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1">
                    <h2 class="text-3xl md:text-4xl font-bold text-dark font-noto mb-6 leading-tight">
                        আমাদের অঙ্গীকার: বিষাক্ত রাসায়নিক নয়,
                        <span class="text-secondary italic">প্রকৃতির ছোঁয়ায় সংরক্ষিত</span>
                    </h2>

                    <p class="text-lg text-gray-700 mb-8 leading-relaxed">
                        আমরা জানি, শুঁটকি মাছে পোকা ঠেকাতে বাজারে সচরাচর বিষাক্ত
                        <strong>DDT</strong> বা <strong>নগজ</strong> ব্যবহার করা হয়।
                        কিন্তু <strong class="italic">"লা লা দিয়া" </strong> আপনার
                        স্বাস্থ্যের সাথে আপস করে না। আমরা ব্যবহার করি পূর্বপুরুষদের সেই
                        প্রাচীন এবং নিরাপদ পদ্ধতি যা এখন আধুনিক গবেষণায় স্বীকৃত।
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4 group">
                            <div
                                class="w-12 h-12 bg-white rounded-2xl shadow-md flex items-center justify-center text-secondary shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-mortar-pestle text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-dark font-noto">
                                    হলুদ ও মরিচের সুরক্ষা
                                </h4>
                                <p class="text-gray-600">
                                    প্রাকৃতিক অ্যান্টিসেপটিক হিসেবে আমরা প্রিমিয়াম কোয়ালিটির
                                    হলুদ ও মরিচের গুঁড়া ব্যবহার করি যা পোকা প্রতিরোধ করে।
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div
                                class="w-12 h-12 bg-white rounded-2xl shadow-md flex items-center justify-center text-secondary shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-jar text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-dark font-noto">
                                    স্বল্প লবণের ব্যবহার
                                </h4>
                                <p class="text-gray-600">
                                    লবণের পরিমাণ সীমিত রাখা হয় যেন মাছের প্রকৃত স্বাদ অটুট থাকে
                                    এবং উচ্চ রক্তচাপের ঝুঁকি না বাড়ে।
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div
                                class="w-12 h-12 bg-white rounded-2xl shadow-md flex items-center justify-center text-red-500 shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-ban text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-dark font-noto">
                                    জিরো কেমিক্যাল গ্যারান্টি
                                </h4>
                                <p class="text-gray-600">
                                    আমাদের প্রক্রিয়াজাতকরণের কোনো ধাপে কোনো ধরনের কৃত্রিম
                                    প্রিজারভেটিভ বা বিষাক্ত কীটনাশক ছোঁয়া হয় না।
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10">
                        <a href="#checkout"
                            class="group relative inline-flex items-center gap-3 px-8 py-4 bg-dark text-white rounded-xl font-bold text-lg hover:bg-black transition-all hover:shadow-[0_10px_20px_rgba(0,0,0,0.2)] active:scale-95 animate-shake hover:animate-none">
                            <span>নিরাপদ খাবার নিশ্চিত করুন</span>
                            <i
                                class="fa-solid fa-circle-arrow-down animate-bounce group-hover:translate-y-1 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <div class="order-1 lg:order-2">
                    <div class="relative group">
                        <div
                            class="absolute -inset-4 bg-linear-to-tr from-secondary to-orange-200 rounded-4xl blur-2xl opacity-30 group-hover:opacity-50 transition-opacity">
                        </div>

                        <div
                            class="relative aspect-video lg:aspect-4/5 rounded-4xl overflow-hidden shadow-2xl border-8 border-white">
                            <img src="{{ asset('assets/landing/dryfish-process.jpg') }}"
                                alt="প্রাকৃতিক মসলা ও শুঁটকি প্রক্রিয়াজাতকরণ" loading="lazy"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" />
                            <div
                                class="absolute bottom-0 inset-x-0 p-6 bg-linear-to-t from-black/80 to-transparent text-white">
                                <p class="text-sm font-medium tracking-widest uppercase mb-1">
                                    Our Process
                                </p>
                                <h3 class="text-2xl font-bold font-noto">
                                    প্রকৃতি থেকেই সুরক্ষা
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="drying-tech" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-dark font-noto mb-4">
                    উন্নত প্রযুক্তি: ধুলোবালি ও
                    <span class="text-primary underline decoration-dotted">মাছিমুক্ত শুঁটকি</span>
                </h2>
                <p class="text-lg text-gray-600">
                    আমরা সনাতন পদ্ধতির নোংরা পরিবেশ ত্যাগ করে সম্পূর্ণ বৈজ্ঞানিক ও
                    স্বাস্থ্যসম্মত উপায়ে মাছ শুকানোর প্রক্রিয়া নিশ্চিত করি।
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div
                    class="group bg-gray-50 rounded-3xl p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 bg-primary/10 text-primary rounded-2xl flex items-center justify-center font-bold text-xl">
                            ০১
                        </div>
                        <h3 class="text-2xl font-bold text-dark font-noto">
                            গ্রিন মাচা (৮ ফুট উচ্চতা)
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        মাটি থেকে অনেক উঁচুতে বিশেষ মাচায় শুকানো হয়। এতো উঁচুতে সাধারণ
                        মাছি পৌঁছাতে পারে না, ফলে ডিমে পোকা হওয়ার ভয় থাকে না এবং ধুলোবালি
                        ও পশু-পাখি থেকে মুক্ত থাকে।
                    </p>
                    <div class="aspect-video rounded-2xl overflow-hidden">
                        <img src="{{ asset('assets/landing/green-macha.jpg') }}" alt="গ্রিন মাচা প্রযুক্তি"
                            loading="lazy"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                            alt="গ্রিন মাচা" />
                    </div>
                </div>

                <div
                    class="group bg-gray-50 rounded-3xl p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 bg-secondary/10 text-secondary rounded-2xl flex items-center justify-center font-bold text-xl">
                            ০২
                        </div>
                        <h3 class="text-2xl font-bold text-dark font-noto">
                            ফিশ ড্রায়ার প্রযুক্তি
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        সূর্যের তাপ কাজে লাগিয়ে নিয়ন্ত্রিত ঘেরা পরিবেশে দ্রুত শুকানো হয়।
                        বাইরের ধুলোবালি, বৃষ্টি বা আর্দ্রতা মাছকে স্পর্শ করতে পারে না। ফলে
                        পুষ্টিগুণ ১০০% বজায় থাকে।
                    </p>
                    <div class="aspect-video rounded-2xl overflow-hidden">
                        <img src="{{ asset('assets/landing/fish-dryer.jpg') }}" alt="ফিশ ড্রায়ার প্রযুক্তি"
                            loading="lazy"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                            alt="ফিশ ড্রায়ার" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="hygiene-packaging" class="py-16 md:py-20 bg-slate-50 overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="order-1 lg:order-2 space-y-6">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-bold text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-shield-virus"></i> হাইজিন ও প্যাকেজিং
                    </div>

                    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 font-noto leading-tight">
                        <span class="text-primary italic">স্বাস্থ্যবিধি ও স্মার্ট ভ্যাকুয়াম প্রযুক্তি</span>
                        ব্যবহার করে আমাদের
                        <span class="italic">লা লা দিয়া'র</span> শুঁটকি প্রস্তুত
                        প্রক্রিয়া
                    </h2>

                    <p class="text-lg text-slate-600 leading-relaxed">
                        নিরাপদ খাবারের জন্য আমরা প্রতিটি ধাপে কঠোর স্বাস্থ্যবিধি মেনে চলি।
                        আমাদের বিশেষ ভ্যাকুয়াম প্যাকেজিং মাছের প্রকৃত স্বাদ ও পুষ্টিগুণ
                        ১২ মাস পর্যন্ত অটুট রাখে।
                    </p>

                    <ul class="space-y-3 pb-4 font-noto">
                        <li class="flex items-center gap-3 text-slate-700 font-medium">
                            <i class="fa-solid fa-circle-check text-primary"></i> গ্লাভস,
                            ক্যাপ ও মাস্কের ব্যবহার
                        </li>
                        <li class="flex items-center gap-3 text-slate-700 font-medium">
                            <i class="fa-solid fa-circle-check text-primary"></i> ১০০%
                            এয়ার-টাইট ভ্যাকুয়াম সিল
                        </li>
                        <li class="flex items-center gap-3 text-slate-700 font-medium">
                            <i class="fa-solid fa-circle-check text-primary"></i> ডাবল
                            লেয়ার সুরক্ষিত প্যাকেজিং
                        </li>
                    </ul>

                    <a href="#checkout"
                        class="inline-flex items-center gap-3 px-10 py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-black transition-all shadow-lg active:scale-95 animate-shake hover:animate-none">
                        অর্ডার করুন <i class="fa-solid fa-arrow-right text-primary"></i>
                    </a>
                </div>
                <div class="order-1 lg:order-2">
                    <div class="relative group">
                        <div data-video data-video-type="youtube" data-video-src="dRgKI0p9a9w" data-video-autoplay="true"
                            data-video-loop="true" data-video-muted="true" data-video-title="Hygiene and Packaging"
                            class="relative z-10 aspect-video w-full rounded-4xl md:rounded-[3rem] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.1)] border-[6px] md:border-12 border-white bg-slate-200">
                        </div>

                        <div class="absolute -top-6 -right-6 w-32 h-32 bg-primary/20 rounded-full blur-3xl z-0"></div>
                        <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-blue-400/10 rounded-full blur-3xl z-0"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="portion-logic" class="py-16 md:py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="w-full lg:w-1/2 relative group">
                    <div
                        class="absolute -inset-4 bg-secondary/10 rounded-[3rem] blur-2xl group-hover:bg-secondary/20 transition-all">
                    </div>
                    <div
                        class="relative aspect-square sm:aspect-video lg:aspect-4/3 rounded-[2.5rem] overflow-hidden shadow-2xl border-4 border-white">
                        <img src="{{ asset('assets/landing/dryfish-portion.jpg') }}"
                            alt="পরিবারের জন্য আদর্শ খাবারের পরিমাণ" loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                        <div
                            class="absolute bottom-8 right-8 bg-white/95 backdrop-blur px-6 py-4 rounded-2xl shadow-xl border-l-4 border-secondary text-center">
                            <p class="text-3xl font-black text-dark leading-none">
                                ~১২৫ গ্রাম
                            </p>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">
                                আদর্শ পরিমাপ
                            </p>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/2">
                    <h2 class="text-3xl md:text-4xl font-bold text-dark font-noto mb-6 leading-tight">
                        কেন ১২৫ গ্রাম প্যাকেট? <br />
                        <span class="text-secondary italic">স্মার্ট পরিবারের স্মার্ট চয়েস</span>
                    </h2>

                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        আমরা শুধু পণ্য বিক্রি করি না, আপনার দৈনন্দিন প্রয়োজনের কথা মাথায়
                        রেখে সঠিক পরিমাণ নির্ধারণ করি। বড় প্যাকেট খুলে বারবার বাতাস
                        ঢোকানোর চেয়ে ছোট প্যাকেটে সতেজতা থাকে একদম অটুট।
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-orange-100 text-secondary rounded-xl flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-people-group text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-dark font-noto">
                                    ৪–৬ জনের পরিবারের জন্য আদর্শ
                                </h4>
                                <p class="text-gray-500 text-sm">
                                    এক বেলার রান্নার জন্য যতটুকু প্রয়োজন, ঠিক ততটুকুই রাখা হয়েছে
                                    প্রতিটি প্যাকেটে।
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-green-100 text-primary rounded-xl flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-leaf text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-dark font-noto">
                                    জিরো ওয়েস্টেজ ও সর্বোচ্চ সতেজতা
                                </h4>
                                <p class="text-gray-500 text-sm">
                                    একবার খুলেই ব্যবহার শেষ করা যায়, ফলে বাইরে রেখে নষ্ট হওয়ার
                                    বা স্বাদ কমে যাওয়ার ভয় নেই।
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-utensils text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-dark font-noto">
                                    Ready to Cook – ঝামেলামুক্ত
                                </h4>
                                <p class="text-gray-500 text-sm">
                                    প্যাকেট খুলেই সরাসরি রান্না করা যায়। আলাদা করে পরিষ্কার বা
                                    ঝাড়ার বাড়তি ঝামেলা নেই।
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10">
                        <a href="#checkout"
                            class="group relative inline-flex items-center justify-center gap-3 px-10 py-4 bg-dark text-white rounded-2xl font-bold text-lg hover:shadow-[0_15px_30px_rgba(0,0,0,0.15)] transition-all hover:-translate-y-1 active:scale-95 animate-shake hover:animate-none">
                            <span class="flex items-center gap-2">
                                <i
                                    class="fa-solid fa-bag-shopping text-secondary group-hover:scale-125 transition-transform"></i>
                                আপনার পছন্দের প্যাকটি বেছে নিন
                            </span>
                        </a>
                        <p class="mt-3 text-xs text-gray-400 italic">
                            গরম ভাত আর শুঁটকির স্বাদ নিন আজই!
                        </p>
                    </div>
                </div>
            </div>
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
                            আপনার পছন্দের পণ্যটি টিক দিন, ওজন ও পরিমাণ নির্বাচন করুন
                        </p>
                    </div>

                    <div class="space-y-4" id="productList">

                        @foreach ($productGroups as $group)
                            @php
                                $product = $group['product'];
                                $variants = $group['variants'];
                                $firstItem = $variants->first();
                                $key = 'prod_' . $product->id;
                                $basePrice = $firstItem->variant->price ?? 0;
                            @endphp
                            <div onclick="autoCheckProduct('{{ $key }}', event)"
                                class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 transition hover:shadow-md hover:border-red-100 cursor-pointer">
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" id="check-{{ $key }}"
                                        onchange="toggleProduct('{{ $key }}')" onclick="event.stopPropagation()"
                                        class="w-5 h-5 accent-red-600 cursor-pointer shrink-0" />
                                    <img src="{{ $product->image_url }}"
                                        class="w-16 h-16 rounded-xl object-cover shrink-0 hover:scale-105 transition-transform duration-300"
                                        alt="{{ $product->name }}" />
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ $product->name }}
                                        </h3>
                                        <p id="price-{{ $key }}" class="text-red-600 font-bold mt-0.5">
                                            ৳{{ number_format($basePrice, 0) }}</p>
                                        <div class="flex gap-2 mt-2 flex-wrap" id="weights-{{ $key }}">
                                            @foreach ($variants as $wi => $item)
                                                <button
                                                    onclick="selectWeight('{{ $key }}', {{ $wi }}, this); event.stopPropagation();"
                                                    {{ $wi === 0 ? 'data-active=true' : '' }}
                                                    class="weight-btn {{ $wi === 0 ? 'active-weight px-2 py-1 text-xs rounded border border-red-400 bg-red-50 text-red-700' : 'px-2 py-1 text-xs rounded border border-gray-200 bg-white text-gray-600 hover:border-red-300' }} font-semibold transition">{{ $item->variant->title }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 bg-red-50 p-2 rounded shrink-0">
                                        <button onclick="updateQty('{{ $key }}', -1); event.stopPropagation();"
                                            class="w-6 h-6 bg-gray-100 hover:bg-red-50 hover:text-red-600 rounded-xl font-bold transition">−</button>
                                        <span id="qty-{{ $key }}"
                                            class="w-6 text-center font-bold text-gray-700">1</span>
                                        <button onclick="updateQty('{{ $key }}', 1); event.stopPropagation();"
                                            class="w-6 h-6 bg-red-600 hover:bg-red-700 text-white rounded font-bold transition">+</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>{{-- #productList --}}


                    <div class="space-y-4 font-noto md:block hidden mt-6">
                        <h4 class="text-xl font-bold text-gray-900">
                            অর্ডারের নিয়মাবলী:
                        </h4>
                        <ul class="space-y-3 text-gray-600">
                            <li class="flex items-start gap-3 border-brand-500 bg-red-50/50">
                                <i class="fas fa-truck text-brand-600 mt-1"></i>
                                <span><strong>(২০০০ টাকা অর্ডার)</strong> করলে সারা বাংলাদেশে
                                    <strong>ডেলিভারি ফ্রি!</strong></span>
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
                                <input type="text" id="custName" placeholder="আপনার নাম *"
                                    class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-1 focus:ring-red-200 outline-none text-sm transition-all" />
                                <input type="tel" id="custPhone" placeholder="মোবাইল নম্বর *"
                                    class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-1 focus:ring-red-200 outline-none text-sm transition-all" />
                            </div>

                            <input type="text" id="custAddress"
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

                            <div id="deliveryZoneSection"
                                class="bg-red-50/50 p-4 rounded-xl border border-red-100 mt-4 transition-all">
                                <p class="text-sm font-bold text-gray-700 mb-1">
                                    ডেলিভারি এলাকা নির্বাচন করুন
                                    <span class="text-red-500">*</span>
                                    <span class="font-normal text-gray-400">(২০০০ টাকার উপর ফ্রি)</span>
                                </p>
                                <p id="zoneError" class="hidden text-xs text-red-600 mb-2 font-medium">
                                    ⚠ অনুগ্রহ করে একটি ডেলিভারি এলাকা নির্বাচন করুন।
                                </p>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach ($zones as $zone)
                                        <label
                                            class="flex flex-col items-center p-2 border rounded-lg cursor-pointer text-center transition-all area-label border-gray-200 bg-white hover:border-red-400">
                                            <input type="radio" name="area" value="{{ $zone->id }}"
                                                class="hidden" onchange="selectArea({{ $zone->id }}, this)" />
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


                            <!-- ORDER SUMMARY BOX -->
                            <div
                                class="p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300 space-y-2 text-sm mt-4">
                                <!-- Per-product breakdown — populated by calculate() -->
                                <div id="cart-breakdown" class="space-y-1 pb-2 border-b border-gray-200 empty:hidden">
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-500">পণ্যের মূল্য:</span>
                                    <span class="font-semibold">৳ <span id="subtotal">০</span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>ডেলিভারি চার্জ:</span>
                                    <span id="shipping-display" class="text-red-600 font-bold">—</span>
                                </div>
                                <div class="flex justify-between border-t pt-2 mt-2 font-black text-lg text-red-600">
                                    <span>সর্বমোট:</span>
                                    <span>৳ <span id="total">০</span></span>
                                </div>
                            </div>

                            <button id="orderBtn" onclick="handleOrder()"
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
                        <h4 class="text-xl font-bold text-gray-900">
                            অর্ডারের নিয়মাবলী:
                        </h4>
                        <ul class="space-y-3 text-gray-600">
                            <li class="flex items-start gap-3 border-brand-500 bg-red-50/50">
                                <i class="fas fa-truck text-brand-600 mt-1"></i>
                                <span><strong>(২০০০ টাকা অর্ডার)</strong> করলে সারা বাংলাদেশে
                                    <strong>ডেলিভারি ফ্রি!</strong></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUCCESS MODAL -->
        <div id="successModal"
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
                <button onclick="window.location.reload()"
                    class="w-full bg-red-600 text-white py-3 rounded-xl font-bold hover:bg-red-700 transition">
                    ঠিক আছে
                </button>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            const ITEMS = {!! json_encode($jsItems) !!};
            const ZONES = {!! json_encode($jsZones) !!};
            const SLUG = '{{ $landing->slug }}';

            // Build state from items
            const state = {
                selected: {},
                qty: {},
                weightIndex: {}
            };
            ITEMS.forEach(item => {
                state.selected[item.key] = false;
                state.qty[item.key] = 1;
                state.weightIndex[item.key] = 0;
            });
            state.zoneId = null;
            state.shippingCost = 0;

            function getPrice(key) {
                const item = ITEMS.find(i => i.key === key);
                if (!item || !item.weights.length) return 0;
                return item.weights[state.weightIndex[key]]?.price ?? 0;
            }

            function autoCheckProduct(key, event) {
                if (event.target.type === 'checkbox') return;
                if (!state.selected[key]) {
                    state.selected[key] = true;
                    const cb = document.getElementById('check-' + key);
                    if (cb) cb.checked = true;
                    calculate();
                }
            }

            function toggleProduct(key) {
                const cb = document.getElementById('check-' + key);
                state.selected[key] = cb ? cb.checked : false;
                calculate();
            }

            function updateQty(key, delta) {
                const next = state.qty[key] + delta;
                if (next < 1 || next > 10) return;
                state.qty[key] = next;
                const el = document.getElementById('qty-' + key);
                if (el) el.innerText = next;
                if (!state.selected[key]) {
                    state.selected[key] = true;
                    const cb = document.getElementById('check-' + key);
                    if (cb) cb.checked = true;
                }
                calculate();
            }

            function selectWeight(key, index, btn) {
                state.weightIndex[key] = index;
                document.getElementById('weights-' + key)
                    ?.querySelectorAll('.weight-btn')
                    .forEach(b => {
                        b.classList.remove('border-red-400', 'bg-red-50', 'text-red-700');
                        b.classList.add('border-gray-200', 'bg-white', 'text-gray-600');
                    });
                btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-600');
                btn.classList.add('border-red-400', 'bg-red-50', 'text-red-700');
                const item = ITEMS.find(i => i.key === key);
                const price = item?.weights[index]?.price ?? 0;
                const priceEl = document.getElementById('price-' + key);
                if (priceEl) priceEl.innerText = '৳' + price;
                if (!state.selected[key]) {
                    state.selected[key] = true;
                    const cb = document.getElementById('check-' + key);
                    if (cb) cb.checked = true;
                }
                calculate();
            }

            function selectArea(zoneId, inputEl) {
                state.zoneId = zoneId;
                document.querySelectorAll('.area-label').forEach(l => {
                    l.classList.remove('border-red-500', 'bg-red-50/50');
                    l.classList.add('border-gray-200', 'bg-white');
                });
                inputEl.closest('.area-label')?.classList.remove('border-gray-200', 'bg-white');
                inputEl.closest('.area-label')?.classList.add('border-red-500', 'bg-red-50/50');
                document.getElementById('zoneError')?.classList.add('hidden');
                document.getElementById('deliveryZoneSection')?.classList.remove('ring-2', 'ring-red-400');
                calculate();
            }

            function calculate() {
                let subtotal = 0;
                let breakdown = '';
                ITEMS.forEach(item => {
                    if (!state.selected[item.key]) return;
                    const qty = state.qty[item.key];
                    const price = getPrice(item.key);
                    const lineTotal = qty * price;
                    subtotal += lineTotal;
                    breakdown +=
                        `<div class="flex justify-between text-xs text-gray-600"><span class="truncate pr-2">${item.name} × ${qty}</span><span class="font-semibold text-gray-700 whitespace-nowrap">৳ ${lineTotal}</span></div>`;
                });
                const breakdownEl = document.getElementById('cart-breakdown');
                if (breakdownEl) breakdownEl.innerHTML = breakdown;

                let shipping = 0;
                if (state.zoneId !== null) {
                    const zone = ZONES.find(z => z.id === state.zoneId);
                    shipping = (zone && zone.free_above > 0 && subtotal >= zone.free_above) ? 0 : (zone?.charge ?? 0);
                }
                state.shippingCost = shipping;

                document.getElementById('subtotal').innerText = subtotal;
                if (state.zoneId === null) {
                    document.getElementById('shipping-display').innerText = '—';
                    document.getElementById('total').innerText = subtotal;
                } else if (shipping === 0 && subtotal > 0) {
                    document.getElementById('shipping-display').innerText = 'ফ্রি';
                    document.getElementById('total').innerText = subtotal;
                } else {
                    document.getElementById('shipping-display').innerText = '৳ ' + shipping;
                    document.getElementById('total').innerText = subtotal + shipping;
                }
            }

            calculate();

            async function handleOrder() {
                const btn = document.getElementById('orderBtn');
                const name = document.getElementById('custName').value.trim();
                const phone = document.getElementById('custPhone').value.trim();
                const address = document.getElementById('custAddress').value.trim();

                const selectedKeys = ITEMS.map(i => i.key).filter(k => state.selected[k]);
                if (!selectedKeys.length) {
                    alert('অনুগ্রহ করে অন্তত একটি পণ্য নির্বাচন করুন।');
                    return;
                }
                if (!name || !address) {
                    alert('অনুগ্রহ করে নাম ও ঠিকানা দিন।');
                    return;
                }
                if (!/^01[3-9]\d{8}$/.test(phone)) {
                    alert('সঠিক মোবাইল নম্বর দিন।');
                    return;
                }
                if (state.zoneId === null) {
                    document.getElementById('zoneError')?.classList.remove('hidden');
                    document.getElementById('deliveryZoneSection')?.classList.add('ring-2', 'ring-red-400');
                    return;
                }

                const items = selectedKeys.map(key => {
                    const item = ITEMS.find(i => i.key === key);
                    const wi = state.weightIndex[key] ?? 0;
                    const variantId = item?.weights[wi]?.variant_id;
                    return {
                        variant_id: variantId,
                        quantity: state.qty[key]
                    };
                }).filter(i => i.variant_id);

                const originalHTML = btn.innerHTML;
                btn.innerHTML =
                    '<svg class="animate-spin h-6 w-6 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> প্রসেসিং...';
                btn.disabled = true;

                try {
                    const res = await fetch(`/api/v1/landing/${SLUG}/checkout`, {
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
                            zone_id: state.zoneId,
                            payment_method: 'cod',
                            items: items,
                        })
                    });
                    const json = await res.json();
                    if (json.success) {
                        // Fire GTM purchase event
                        window.dataLayer = window.dataLayer || [];
                        window.dataLayer.push({
                            event: 'purchase',
                            value: parseInt(document.getElementById('total').innerText) || 0,
                            currency: 'BDT',
                            content_category: 'dry_fish'
                        });
                        document.getElementById('successModal').classList.remove('hidden');
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
        </script>
    @endpush
@endsection
