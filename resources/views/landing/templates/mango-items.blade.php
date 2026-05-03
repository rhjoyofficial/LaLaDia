@extends('layouts.app')

@section('title', $landing->meta_title ?? $product->name)
@section('meta_description', $landing->meta_description ?? $product->short_description)

@section('content')
    <!-- Hero Section -->
    <section id="hero"
        class="relative w-full min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-[#fbf8ee] via-[#fef4d4] to-[#eaf3e4] px-6 sm:px-12 lg:px-24 pt-20 pb-12">
        <!-- Floating Background Elements (Cinematic Depth) -->
        <div class="absolute top-32 left-10 w-12 h-12 opacity-80 animate-[bounce_6s_infinite]">
            <!-- Placeholder for top left floating mango -->
            <img src="https://placehold.co/100x100/transparent/F59E0B?text=🥭" alt="Floating Mango"
                class="w-full h-full object-contain drop-shadow-lg" loading="lazy" />
        </div>
        <div class="absolute bottom-20 right-10 w-16 h-16 opacity-70 animate-[bounce_7s_infinite_reverse]">
            <!-- Placeholder for bottom right floating leaf/mango -->
            <img src="https://placehold.co/100x100/transparent/22C55E?text=🍃" alt="Floating Leaf"
                class="w-full h-full object-contain drop-shadow-lg" loading="lazy" />
        </div>

        <div
            class="max-w-7xl w-full mx-auto flex flex-col lg:flex-row items-center justify-between gap-8 lg:gap-12 relative z-10">
            <!-- Left Column: Text & CTA -->
            <div
                class="w-full lg:w-1/2 flex flex-col items-start cinematic-element opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <!-- Festival Badge -->
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/60 backdrop-blur-md border border-green-100 shadow-sm mb-8 hover:scale-105 transition-transform cursor-default">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z">
                        </path>
                    </svg>
                    <span class="text-xs md:text-sm font-semibold text-green-800">Summer Mango Festival 2025 —
                        <span class="text-green-600">Now Open</span></span>
                </div>

                <!-- Main Heading -->
                <h1
                    class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold leading-[1.1] tracking-tight mb-6 text-[#2D241C]">
                    Fresh Premium
                    <span class="text-[#D96B08] drop-shadow-sm block">Mangoes</span>
                    Delivered to Your Doorstep
                </h1>

                <!-- Subheading -->
                <p class="text-base md:text-lg text-gray-700 mb-10 max-w-xl leading-relaxed">
                    Naturally ripened, handpicked mangoes directly from the orchards of
                    <span class="font-bold text-gray-900">Rajshahi & Chapai Nawabganj</span>
                    — chemical-free, juicy, unforgettable.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap items-center gap-5 mb-12">
                    <button
                        class="flex items-center gap-2 px-8 py-3.5 bg-[#FDBA21] hover:bg-[#F59E0B] text-[#2D241C] rounded-full font-bold text-lg shadow-[0_8px_20px_-6px_rgba(253,186,33,0.6)] transition-all hover:-translate-y-1 hover:shadow-[0_12px_25px_-6px_rgba(253,186,33,0.8)] duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Shop Now
                    </button>

                    <button
                        class="flex items-center gap-2 px-8 py-3.5 bg-white/80 backdrop-blur-sm hover:bg-white text-gray-800 rounded-full font-bold text-lg shadow-md transition-all hover:-translate-y-1 hover:shadow-lg duration-300">
                        <div class="bg-green-600 rounded-full p-1.5 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"></path>
                            </svg>
                        </div>
                        Watch Farm Story
                    </button>
                </div>

                <!-- Trust Indicator -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="flex -space-x-3">
                        <!-- Avatar Placeholders -->
                        <img class="w-10 h-10 rounded-full border-2 border-[#fef4d4] object-cover"
                            src="https://placehold.co/100x100/FDBA21/ffffff?text=1" alt="Customer" />
                        <img class="w-10 h-10 rounded-full border-2 border-[#fef4d4] object-cover"
                            src="https://placehold.co/100x100/22C55E/ffffff?text=2" alt="Customer" />
                        <img class="w-10 h-10 rounded-full border-2 border-[#fef4d4] object-cover"
                            src="https://placehold.co/100x100/3B82F6/ffffff?text=3" alt="Customer" />
                        <img class="w-10 h-10 rounded-full border-2 border-[#fef4d4] object-cover"
                            src="https://placehold.co/100x100/EF4444/ffffff?text=4" alt="Customer" />
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1 text-[#F59E0B]">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <span class="text-gray-900 font-bold ml-1">4.9</span>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">
                            Trusted by 10,000+ happy customers
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Image & Badges -->
            <div
                class="w-full lg:w-1/2 relative flex justify-center mt-6 lg:mt-0 cinematic-element opacity-0 translate-x-10 transition-all duration-1000 delay-300 ease-out">
                <!-- Main Mango Basket Image -->
                <div class="relative z-10 w-full max-w-lg aspect-[4/3]">
                    <img src="{{ asset('assets/landing/mango-hero.png') }}" alt="Fresh Premium Mangoes in a Basket"
                        class="w-full h-full object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-700 ease-in-out"
                        loading="lazy" />
                </div>

                <!-- Floating Badge 1 (Sweetness) -->
                <div
                    class="absolute top-4 -left-4 md:left-4 z-20 bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-xl flex items-center gap-3 animate-[bounce_5s_infinite]">
                    <div class="bg-[#1E7D53] text-white rounded-full p-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                            Sweetness
                        </p>
                        <p class="text-sm font-extrabold text-gray-900">22° Brix</p>
                    </div>
                </div>

                <!-- Floating Badge 2 (Price) -->
                <div
                    class="absolute bottom-12 -right-2 md:right-4 z-20 bg-white/95 backdrop-blur-md px-5 py-3 rounded-2xl shadow-xl animate-[bounce_6s_infinite_reverse]">
                    <p class="text-xs text-gray-500 font-medium mb-0.5">From only</p>
                    <p class="text-xl font-extrabold text-[#D96B08]">
                        ৳ 180<span class="text-sm font-medium text-gray-500">/kg</span>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cinematic JS Trigger (single consolidated observer) -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.remove(
                                "opacity-0",
                                "translate-y-10",
                                "translate-y-12",
                                "translate-x-10",
                                "translate-x-12",
                                "-translate-x-12"
                            );
                            entry.target.classList.add(
                                "opacity-100",
                                "translate-y-0",
                                "translate-x-0"
                            );
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1
                }
            );
            document.querySelectorAll(".cinematic-element").forEach((el) => {
                observer.observe(el);
            });
        });
    </script>

    <!-- Features Section -->
    <section id="features" class="w-full bg-gradient-to-b from-[#eaf3e4] to-[#fbf8ee] px-6 sm:px-12 lg:px-24 py-10">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <!-- Feature Card 1 -->
                <div
                    class="bg-white/70 backdrop-blur-md rounded-2xl p-6 shadow-sm border border-orange-50 hover:shadow-md hover:-translate-y-1 transition-all duration-300 cinematic-element opacity-0 translate-y-10 ease-out">
                    <div
                        class="w-12 h-12 rounded-full bg-[#FDBA21]/20 flex items-center justify-center mb-5 text-[#D96B08]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#2D241C] mb-2">100% Organic</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Chemical-free, naturally ripened
                    </p>
                </div>

                <!-- Feature Card 2 -->
                <div
                    class="bg-white/70 backdrop-blur-md rounded-2xl p-6 shadow-sm border border-orange-50 hover:shadow-md hover:-translate-y-1 transition-all duration-300 cinematic-element opacity-0 translate-y-10 delay-100 ease-out">
                    <div
                        class="w-12 h-12 rounded-full bg-[#FDBA21]/20 flex items-center justify-center mb-5 text-[#D96B08]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#2D241C] mb-2">Farm Fresh</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Handpicked the same morning
                    </p>
                </div>

                <!-- Feature Card 3 -->
                <div
                    class="bg-white/70 backdrop-blur-md rounded-2xl p-6 shadow-sm border border-orange-50 hover:shadow-md hover:-translate-y-1 transition-all duration-300 cinematic-element opacity-0 translate-y-10 delay-200 ease-out">
                    <div
                        class="w-12 h-12 rounded-full bg-[#FDBA21]/20 flex items-center justify-center mb-5 text-[#D96B08]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#2D241C] mb-2">Fast Delivery</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Anywhere in BD within 48h
                    </p>
                </div>

                <!-- Feature Card 4 -->
                <div
                    class="bg-white/70 backdrop-blur-md rounded-2xl p-6 shadow-sm border border-orange-50 hover:shadow-md hover:-translate-y-1 transition-all duration-300 cinematic-element opacity-0 translate-y-10 delay-300 ease-out">
                    <div
                        class="w-12 h-12 rounded-full bg-[#FDBA21]/20 flex items-center justify-center mb-5 text-[#D96B08]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#2D241C] mb-2">
                        Cash on Delivery
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Pay only when you receive
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Grid Section -->
    <section id="shop" class="w-full bg-[#fbf8ee] px-6 sm:px-12 lg:px-24 py-12">
        <div class="max-w-7xl mx-auto">
            <!-- Section Header -->
            <div
                class="text-center max-w-2xl mx-auto mb-10 cinematic-element opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <div
                    class="inline-flex items-center justify-center gap-2 px-4 py-1.5 rounded-full bg-orange-100/50 text-[#D96B08] text-xs font-bold uppercase tracking-wider mb-4">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    Best Selling Varieties
                </div>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-[#2D241C] mb-4 tracking-tight">
                    The Royal Collection of <br />
                    Bangladeshi Mangoes
                </h2>
                <p class="text-gray-600 text-sm md:text-base">
                    Six legendary varieties — each handpicked at peak ripeness from our
                    partner orchards.
                </p>
            </div>

            <!-- Grid Container -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card 1: Himsagar -->
                <div
                    class="relative bg-white rounded-3xl p-5 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] border border-[#f3ead3] hover:shadow-[0_8px_30px_-10px_rgba(217,107,8,0.15)] transition-all duration-300 group cinematic-element opacity-0 translate-y-10 ease-out">
                    <div
                        class="w-full aspect-square bg-[#f8f5eb] rounded-2xl mb-5 relative overflow-hidden flex items-center justify-center">
                        <div
                            class="absolute top-3 right-3 z-10 bg-white/90 backdrop-blur-sm shadow-sm px-2.5 py-1 rounded-full flex items-center gap-1 text-xs font-bold text-gray-800">
                            <svg class="w-3.5 h-3.5 text-[#FDBA21]" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            4.9
                        </div>
                        <img src="https://placehold.co/400x400/transparent/d97014?text=Himsagar" alt="Himsagar Mango"
                            class="w-3/4 h-3/4 object-contain group-hover:scale-110 transition-transform duration-500 ease-out"
                            loading="lazy" />
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-[#2D241C] mb-1">Himsagar</h4>
                        <p class="text-xs text-gray-500 mb-4">Premium A-grade</p>
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">
                                    Per KG
                                </p>
                                <p class="text-2xl font-extrabold text-[#D96B08]">৳ 280</p>
                            </div>
                            <button
                                class="bg-[#fef4d4] text-[#D96B08] hover:bg-[#FDBA21] hover:text-[#2D241C] font-bold text-sm px-5 py-2.5 rounded-full transition-colors flex items-center gap-1 shadow-sm">
                                + Add
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Langra -->
                <div
                    class="relative bg-white rounded-3xl p-5 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] border border-[#f3ead3] hover:shadow-[0_8px_30px_-10px_rgba(217,107,8,0.15)] transition-all duration-300 group cinematic-element opacity-0 translate-y-10 delay-100 ease-out">
                    <div
                        class="w-full aspect-square bg-[#f8f5eb] rounded-2xl mb-5 relative overflow-hidden flex items-center justify-center">
                        <div
                            class="absolute top-3 right-3 z-10 bg-white/90 backdrop-blur-sm shadow-sm px-2.5 py-1 rounded-full flex items-center gap-1 text-xs font-bold text-gray-800">
                            <svg class="w-3.5 h-3.5 text-[#FDBA21]" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            4.8
                        </div>
                        <img src="https://placehold.co/400x400/transparent/d97014?text=Langra" alt="Langra Mango"
                            class="w-3/4 h-3/4 object-contain group-hover:scale-110 transition-transform duration-500 ease-out"
                            loading="lazy" />
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-[#2D241C] mb-1">Langra</h4>
                        <p class="text-xs text-gray-500 mb-4">Premium A-grade</p>
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">
                                    Per KG
                                </p>
                                <p class="text-2xl font-extrabold text-[#D96B08]">৳ 240</p>
                            </div>
                            <button
                                class="bg-[#fef4d4] text-[#D96B08] hover:bg-[#FDBA21] hover:text-[#2D241C] font-bold text-sm px-5 py-2.5 rounded-full transition-colors flex items-center gap-1 shadow-sm">
                                + Add
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Fazli -->
                <div
                    class="relative bg-white rounded-3xl p-5 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] border border-[#f3ead3] hover:shadow-[0_8px_30px_-10px_rgba(217,107,8,0.15)] transition-all duration-300 group cinematic-element opacity-0 translate-y-10 delay-200 ease-out">
                    <div
                        class="w-full aspect-square bg-[#f8f5eb] rounded-2xl mb-5 relative overflow-hidden flex items-center justify-center">
                        <div
                            class="absolute top-3 right-3 z-10 bg-white/90 backdrop-blur-sm shadow-sm px-2.5 py-1 rounded-full flex items-center gap-1 text-xs font-bold text-gray-800">
                            <svg class="w-3.5 h-3.5 text-[#FDBA21]" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            4.7
                        </div>
                        <img src="https://placehold.co/400x400/transparent/d97014?text=Fazli" alt="Fazli Mango"
                            class="w-3/4 h-3/4 object-contain group-hover:scale-110 transition-transform duration-500 ease-out"
                            loading="lazy" />
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-[#2D241C] mb-1">Fazli</h4>
                        <p class="text-xs text-gray-500 mb-4">Premium A-grade</p>
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">
                                    Per KG
                                </p>
                                <p class="text-2xl font-extrabold text-[#D96B08]">৳ 220</p>
                            </div>
                            <button
                                class="bg-[#fef4d4] text-[#D96B08] hover:bg-[#FDBA21] hover:text-[#2D241C] font-bold text-sm px-5 py-2.5 rounded-full transition-colors flex items-center gap-1 shadow-sm">
                                + Add
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Amrapali -->
                <div
                    class="relative bg-white rounded-3xl p-5 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] border border-[#f3ead3] hover:shadow-[0_8px_30px_-10px_rgba(217,107,8,0.15)] transition-all duration-300 group cinematic-element opacity-0 translate-y-10 delay-300 ease-out">
                    <div
                        class="w-full aspect-square bg-[#f8f5eb] rounded-2xl mb-5 relative overflow-hidden flex items-center justify-center">
                        <div
                            class="absolute top-3 right-3 z-10 bg-white/90 backdrop-blur-sm shadow-sm px-2.5 py-1 rounded-full flex items-center gap-1 text-xs font-bold text-gray-800">
                            <svg class="w-3.5 h-3.5 text-[#FDBA21]" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            4.8
                        </div>
                        <img src="https://placehold.co/400x400/transparent/d97014?text=Amrapali" alt="Amrapali Mango"
                            class="w-3/4 h-3/4 object-contain group-hover:scale-110 transition-transform duration-500 ease-out"
                            loading="lazy" />
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-[#2D241C] mb-1">Amrapali</h4>
                        <p class="text-xs text-gray-500 mb-4">Premium A-grade</p>
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">
                                    Per KG
                                </p>
                                <p class="text-2xl font-extrabold text-[#D96B08]">৳ 260</p>
                            </div>
                            <button
                                class="bg-[#fef4d4] text-[#D96B08] hover:bg-[#FDBA21] hover:text-[#2D241C] font-bold text-sm px-5 py-2.5 rounded-full transition-colors flex items-center gap-1 shadow-sm">
                                + Add
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Khirsapat -->
                <div
                    class="relative bg-white rounded-3xl p-5 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] border border-[#f3ead3] hover:shadow-[0_8px_30px_-10px_rgba(217,107,8,0.15)] transition-all duration-300 group cinematic-element opacity-0 translate-y-10 delay-400 ease-out">
                    <div
                        class="w-full aspect-square bg-[#f8f5eb] rounded-2xl mb-5 relative overflow-hidden flex items-center justify-center">
                        <div
                            class="absolute top-3 right-3 z-10 bg-white/90 backdrop-blur-sm shadow-sm px-2.5 py-1 rounded-full flex items-center gap-1 text-xs font-bold text-gray-800">
                            <svg class="w-3.5 h-3.5 text-[#FDBA21]" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            5.0
                        </div>
                        <img src="https://placehold.co/400x400/transparent/d97014?text=Khirsapat" alt="Khirsapat Mango"
                            class="w-3/4 h-3/4 object-contain group-hover:scale-110 transition-transform duration-500 ease-out"
                            loading="lazy" />
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-[#2D241C] mb-1">Khirsapat</h4>
                        <p class="text-xs text-gray-500 mb-4">Premium A-grade</p>
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">
                                    Per KG
                                </p>
                                <p class="text-2xl font-extrabold text-[#D96B08]">৳ 300</p>
                            </div>
                            <button
                                class="bg-[#fef4d4] text-[#D96B08] hover:bg-[#FDBA21] hover:text-[#2D241C] font-bold text-sm px-5 py-2.5 rounded-full transition-colors flex items-center gap-1 shadow-sm">
                                + Add
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 6: Gopalbhog -->
                <div
                    class="relative bg-white rounded-3xl p-5 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] border border-[#f3ead3] hover:shadow-[0_8px_30px_-10px_rgba(217,107,8,0.15)] transition-all duration-300 group cinematic-element opacity-0 translate-y-10 delay-500 ease-out">
                    <div
                        class="w-full aspect-square bg-[#f8f5eb] rounded-2xl mb-5 relative overflow-hidden flex items-center justify-center">
                        <div
                            class="absolute top-3 right-3 z-10 bg-white/90 backdrop-blur-sm shadow-sm px-2.5 py-1 rounded-full flex items-center gap-1 text-xs font-bold text-gray-800">
                            <svg class="w-3.5 h-3.5 text-[#FDBA21]" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            4.7
                        </div>
                        <img src="https://placehold.co/400x400/transparent/d97014?text=Gopalbhog" alt="Gopalbhog Mango"
                            class="w-3/4 h-3/4 object-contain group-hover:scale-110 transition-transform duration-500 ease-out"
                            loading="lazy" />
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-[#2D241C] mb-1">Gopalbhog</h4>
                        <p class="text-xs text-gray-500 mb-4">Premium A-grade</p>
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">
                                    Per KG
                                </p>
                                <p class="text-2xl font-extrabold text-[#D96B08]">৳ 250</p>
                            </div>
                            <button
                                class="bg-[#fef4d4] text-[#D96B08] hover:bg-[#FDBA21] hover:text-[#2D241C] font-bold text-sm px-5 py-2.5 rounded-full transition-colors flex items-center gap-1 shadow-sm">
                                + Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About Story Section -->
    <section id="about" class="w-full bg-[#fbf8ee] px-6 sm:px-12 lg:px-24 py-12 lg:py-16 overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-10 lg:gap-16">
            <!-- Left Column: Image -->
            <div
                class="w-full lg:w-1/2 relative cinematic-element opacity-0 -translate-x-12 transition-all duration-1000 ease-out">
                <!-- Decorative background blur blob -->
                <div
                    class="absolute inset-0 bg-green-200/50 blur-[100px] rounded-full -z-10 transform scale-90 translate-y-10">
                </div>

                <div
                    class="relative w-full aspect-square rounded-[2.5rem] overflow-hidden shadow-[0_20px_50px_-12px_rgba(0,0,0,0.15)] border-8 border-white group">
                    <!-- Replaced with generic orchard placeholder, using aspect ratio and lazy loading -->
                    <img src="about-mango.jpg" alt="Mango Orchard"
                        class="w-full h-full aspect-square object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out"
                        loading="lazy" />

                    <!-- Overlay gradient for cinematic depth -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
                </div>
            </div>

            <!-- Right Column: Text & Features List -->
            <div
                class="w-full lg:w-1/2 flex flex-col cinematic-element opacity-0 translate-x-12 transition-all duration-1000 delay-200 ease-out">
                <!-- Subheading -->
                <p class="text-xs font-bold uppercase tracking-widest text-[#1E7D53] mb-4">
                    Why MangoMart
                </p>

                <!-- Heading -->
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-medium text-[#2D241C] mb-6 leading-[1.15] tracking-tight">
                    From the orchard <br />
                    <span class="font-extrabold text-[#D96B08]">straight to your table.</span>
                </h2>

                <!-- Description text -->
                <p class="text-base text-gray-600 mb-10 leading-relaxed max-w-lg">
                    We work directly with farmers — no middlemen, no compromise. Every
                    mango is inspected by hand and shipped within hours of being picked.
                </p>

                <!-- Vertical Features List -->
                <div class="flex flex-col gap-6">
                    <!-- List Item 1 -->
                    <div class="flex items-start gap-4 group cursor-default">
                        <div
                            class="w-6 h-6 mt-1 rounded-full bg-[#1E7D53] flex items-center justify-center flex-shrink-0 text-white shadow-md group-hover:scale-110 group-hover:bg-[#D96B08] transition-all duration-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-base font-bold text-[#2D241C] mb-0.5 transition-colors group-hover:text-[#D96B08]">
                                Handpicked Daily
                            </h4>
                            <p class="text-sm text-gray-500">
                                Picked the same morning we ship
                            </p>
                        </div>
                    </div>

                    <!-- List Item 2 -->
                    <div class="flex items-start gap-4 group cursor-default">
                        <div
                            class="w-6 h-6 mt-1 rounded-full bg-[#1E7D53] flex items-center justify-center flex-shrink-0 text-white shadow-md group-hover:scale-110 group-hover:bg-[#D96B08] transition-all duration-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-base font-bold text-[#2D241C] mb-0.5 transition-colors group-hover:text-[#D96B08]">
                                Chemical Free
                            </h4>
                            <p class="text-sm text-gray-500">
                                Zero carbide, naturally tree-ripened
                            </p>
                        </div>
                    </div>

                    <!-- List Item 3 -->
                    <div class="flex items-start gap-4 group cursor-default">
                        <div
                            class="w-6 h-6 mt-1 rounded-full bg-[#1E7D53] flex items-center justify-center flex-shrink-0 text-white shadow-md group-hover:scale-110 group-hover:bg-[#D96B08] transition-all duration-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-base font-bold text-[#2D241C] mb-0.5 transition-colors group-hover:text-[#D96B08]">
                                Premium Packaging
                            </h4>
                            <p class="text-sm text-gray-500">
                                Cushioned eco-boxes prevent bruising
                            </p>
                        </div>
                    </div>

                    <!-- List Item 4 -->
                    <div class="flex items-start gap-4 group cursor-default">
                        <div
                            class="w-6 h-6 mt-1 rounded-full bg-[#1E7D53] flex items-center justify-center flex-shrink-0 text-white shadow-md group-hover:scale-110 group-hover:bg-[#D96B08] transition-all duration-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-base font-bold text-[#2D241C] mb-0.5 transition-colors group-hover:text-[#D96B08]">
                                Nationwide Delivery
                            </h4>
                            <p class="text-sm text-gray-500">
                                Reach all 64 districts within 48h
                            </p>
                        </div>
                    </div>

                    <!-- List Item 5 -->
                    <div class="flex items-start gap-4 group cursor-default">
                        <div
                            class="w-6 h-6 mt-1 rounded-full bg-[#1E7D53] flex items-center justify-center flex-shrink-0 text-white shadow-md group-hover:scale-110 group-hover:bg-[#D96B08] transition-all duration-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-base font-bold text-[#2D241C] mb-0.5 transition-colors group-hover:text-[#D96B08]">
                                Sweetness Guarantee
                            </h4>
                            <p class="text-sm text-gray-500">
                                100% refund if you're not delighted
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Festival Banner Section -->
    <section id="festival" class="w-full bg-[#fbf8ee] px-6 sm:px-12 lg:px-24 py-12">
        <div class="max-w-6xl mx-auto">
            <!-- Banner Card -->
            <div
                class="bg-[#f5f0de] rounded-[2rem] overflow-hidden flex flex-col lg:flex-row shadow-sm hover:shadow-xl transition-shadow duration-500 cinematic-element opacity-0 translate-y-12 ease-out">
                <!-- Left Content Column -->
                <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center relative">
                    <!-- Subtle background pattern/glow (Cinematic touch) -->
                    <div
                        class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-white/40 to-transparent pointer-events-none">
                    </div>

                    <!-- Badge -->
                    <div
                        class="relative flex items-center gap-2 text-[10px] sm:text-xs font-bold uppercase tracking-widest text-[#1E7D53] mb-5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Limited Time Offer
                    </div>

                    <!-- Heading -->
                    <h2
                        class="relative text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#2D241C] mb-4 leading-tight tracking-tight">
                        Summer Mango <br />
                        Festival 🥭
                    </h2>

                    <!-- Subheading -->
                    <p class="relative text-gray-700 font-medium mb-6 text-sm sm:text-base max-w-sm">
                        Buy <span class="text-[#D96B08] font-bold">5 KG</span>, get
                        <span class="text-[#D96B08] font-bold">1 KG FREE</span> — plus
                        free delivery nationwide.
                    </p>

                    <!-- Countdown Timer -->
                    <div class="relative flex items-center gap-6 sm:gap-8 mb-10">
                        <!-- Days -->
                        <div class="flex flex-col items-center min-w-[3rem]">
                            <span
                                class="text-3xl sm:text-4xl font-light text-[#D96B08] border-b-2 border-[#D96B08]/20 pb-1 mb-2 w-full text-center">02</span>
                            <span
                                class="text-[9px] sm:text-[10px] text-gray-500 uppercase tracking-widest font-bold">Days</span>
                        </div>

                        <!-- Hours -->
                        <div class="flex flex-col items-center min-w-[3rem]">
                            <span
                                class="text-3xl sm:text-4xl font-light text-[#D96B08] border-b-2 border-[#D96B08]/20 pb-1 mb-2 w-full text-center">23</span>
                            <span
                                class="text-[9px] sm:text-[10px] text-gray-500 uppercase tracking-widest font-bold">Hours</span>
                        </div>

                        <!-- Minutes -->
                        <div class="flex flex-col items-center min-w-[3rem]">
                            <span
                                class="text-3xl sm:text-4xl font-light text-[#D96B08] border-b-2 border-[#D96B08]/20 pb-1 mb-2 w-full text-center">59</span>
                            <span
                                class="text-[9px] sm:text-[10px] text-gray-500 uppercase tracking-widest font-bold">Min</span>
                        </div>

                        <!-- Seconds -->
                        <div class="flex flex-col items-center min-w-[3rem]">
                            <span
                                class="text-3xl sm:text-4xl font-light text-[#D96B08] border-b-2 border-[#D96B08]/20 pb-1 mb-2 w-full text-center hover:text-[#2D241C] transition-colors duration-300">51</span>
                            <span
                                class="text-[9px] sm:text-[10px] text-gray-500 uppercase tracking-widest font-bold">Sec</span>
                        </div>
                    </div>

                    <!-- Call to Action Button -->
                    <button
                        class="relative bg-[#FDBA21] hover:bg-[#F59E0B] text-[#2D241C] px-8 py-3.5 rounded-full font-bold text-sm sm:text-base w-max transition-all hover:-translate-y-1 hover:shadow-[0_10px_20px_-5px_rgba(253,186,33,0.6)] shadow-md group">
                        <span class="flex items-center gap-2">
                            Claim Offer
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </span>
                    </button>
                </div>

                <!-- Right Image Column -->
                <div class="w-full lg:w-1/2 relative overflow-hidden group">
                    <!-- Replaced with generic dark moody mango placeholder -->
                    <img src="{{ asset('assets/landing/dark-mango.jpg') }}" alt="Premium Sliced Mango"
                        class="w-full h-full aspect-[4/3] object-cover group-hover:scale-110 group-hover:rotate-1 transition-all duration-1000 ease-in-out"
                        loading="lazy" />

                    <!-- Cinematic dark gradient overlay -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-[#f5f0de] via-transparent to-transparent opacity-100 lg:opacity-100 pointer-events-none hidden lg:block">
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-[#f5f0de] via-transparent to-transparent opacity-100 lg:opacity-0 pointer-events-none block lg:hidden">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="w-full bg-[#fbf8ee] px-6 sm:px-12 lg:px-24 py-12 lg:py-16 overflow-hidden">
        <div class="max-w-6xl mx-auto">
            <!-- Section Header -->
            <div class="text-center mb-10 cinematic-element opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <p class="text-xs font-bold uppercase tracking-widest text-[#1E7D53] mb-4">
                    Gallery
                </p>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-medium text-[#2D241C] tracking-tight">
                    A peek into our <br class="block sm:hidden" />
                    <span class="font-extrabold text-[#D96B08]">mango world</span>
                </h2>
            </div>

            <!-- Masonry-style Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                <!-- Image 1: Tall (Left Column) -->
                <div
                    class="md:col-span-1 md:row-span-2 relative rounded-[2rem] overflow-hidden group shadow-sm cinematic-element opacity-0 translate-y-12 ease-out">
                    <div
                        class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors duration-500 z-10 pointer-events-none">
                    </div>
                    <img src="{{ asset('assets/landing/tall-tree.jpg') }}" alt="Mangoes growing on a tree branch"
                        loading="lazy"
                        class="aspect-[2/3] w-full object-cover transform group-hover:scale-110 transition-transform duration-1000 ease-in-out" />
                </div>

                <!-- Image 2: Square (Middle Top) -->
                <div
                    class="md:col-span-1 md:row-span-1 relative rounded-[2rem] overflow-hidden group shadow-sm cinematic-element opacity-0 translate-y-12 delay-100 ease-out">
                    <div
                        class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors duration-500 z-10 pointer-events-none">
                    </div>
                    <img src="{{ asset('assets/landing/harvesting-mango.jpg') }}"
                        alt="Freshly harvested mangoes in a crate"
                        class="aspect-square w-full object-cover transform group-hover:scale-110 transition-transform duration-1000 ease-in-out" />
                </div>

                <!-- Image 3: Square (Right Top) -->
                <div
                    class="md:col-span-1 md:row-span-1 relative rounded-[2rem] overflow-hidden group shadow-sm cinematic-element opacity-0 translate-y-12 delay-200 ease-out">
                    <!-- Floating Delivery Tag -->
                    <div
                        class="absolute bottom-4 right-4 z-20 bg-[#1E7D53] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                        mango delivery
                    </div>
                    <div
                        class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors duration-500 z-10 pointer-events-none">
                    </div>
                    <img src="{{ asset('assets/landing/delivery.jpg') }}"
                        alt="Delivery personnel handing over a mango box"
                        class="aspect-square w-full object-cover transform group-hover:scale-110 transition-transform duration-1000 ease-in-out" />
                </div>

                <!-- Image 4: Wide (Spanning Middle & Right Bottom) -->
                <div
                    class="md:col-span-2 md:row-span-1 relative rounded-[2rem] overflow-hidden group shadow-sm cinematic-element opacity-0 translate-y-12 delay-300 ease-out mt-4 md:mt-0">
                    <div
                        class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors duration-500 z-10 pointer-events-none">
                    </div>
                    <img src="{{ asset('assets/landing/sliced-mango.jpg') }}"
                        alt="Close up of a beautifully sliced juicy mango" loading="lazy"
                        class="aspect-[21/10] w-full object-cover transform group-hover:scale-110 transition-transform duration-1000 ease-in-out" />
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section id="cta" class="w-full bg-[#fbf8ee] px-6 sm:px-12 lg:px-24 pb-16">
        <div class="max-w-6xl mx-auto">
            <div
                class="bg-gradient-to-br from-[#fef8e6] to-[#fef0c3] border border-[#fde68a] rounded-[2rem] p-8 sm:p-12 lg:p-16 flex flex-col lg:flex-row items-center justify-between gap-10 shadow-sm relative overflow-hidden cinematic-element opacity-0 translate-y-12 transition-all duration-700 ease-out">
                <!-- Left Content -->
                <div class="w-full lg:w-3/5 relative z-10">
                    <div
                        class="inline-block text-[10px] font-bold uppercase tracking-widest text-[#D96B08] bg-white/60 backdrop-blur-sm px-3 py-1.5 rounded-full mb-4">
                        Ready when you are
                    </div>
                    <h2
                        class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-[#2D241C] mb-4 leading-tight tracking-tight">
                        Order Premium <br />
                        Mangoes Today
                    </h2>
                    <p class="text-gray-700 text-sm md:text-base mb-8 max-w-md leading-relaxed">
                        Join 10,000+ households across Bangladesh enjoying the freshest,
                        most authentic mangoes — delivered to their doorstep.
                    </p>

                    <div class="flex flex-wrap items-center gap-4">
                        <button
                            class="flex items-center gap-2 px-8 py-3.5 bg-[#FDBA21] hover:bg-[#F59E0B] text-[#2D241C] rounded-full font-bold shadow-[0_8px_20px_-6px_rgba(253,186,33,0.6)] transition-all hover:-translate-y-1 duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            Order Now
                        </button>
                        <button
                            class="flex items-center gap-2 px-8 py-3.5 bg-[#25D366] hover:bg-[#1DA851] text-white rounded-full font-bold shadow-[0_8px_20px_-6px_rgba(37,211,102,0.6)] transition-all hover:-translate-y-1 duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z">
                                </path>
                            </svg>
                            WhatsApp Order
                        </button>
                    </div>
                </div>

                <!-- Right Image -->
                <div class="w-full lg:w-2/5 relative z-10 group">
                    <div
                        class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden shadow-lg border-4 border-white/50">
                        <img src="{{ asset('assets/landing/mango-hero.png') }}" alt="Premium Mangoes Ready to Ship"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out" />
                    </div>
                    <!-- Decorative abstract shape behind image -->
                    <div
                        class="absolute -bottom-6 -right-6 w-32 h-32 bg-[#FDBA21]/20 rounded-full blur-2xl -z-10 pointer-events-none">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
