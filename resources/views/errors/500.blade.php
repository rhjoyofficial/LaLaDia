@extends('layouts.app')

@section('title', 'Server Error')
@section('meta_description', 'Something went wrong on our end. Please try again shortly.')

@section('content')
    <section class="min-h-[70vh] flex items-center justify-center px-4 py-20 bg-ivory">
        <div class="text-center max-w-lg">

            {{-- 500 Graphic --}}
            <div class="relative inline-flex items-center justify-center mb-8">
                <span class="text-[9rem] font-extrabold text-primary leading-none select-none">500</span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-20 h-20 rounded-full bg-gold-antique/10 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-primary text-3xl"></i>
                    </div>
                </div>
            </div>

            <h1 class="text-2xl md:text-3xl font-bold text-brand mb-3">
                Something Went Wrong
            </h1>
            <p class="text-muted text-base mb-8 leading-relaxed">
                We ran into an unexpected error. Our team has been notified.
                Please try again in a moment.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gold-antique hover:bg-gold-antique text-white font-semibold text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-house text-xs"></i>
                    Back to Home
                </a>
                <a href="{{ route('products.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-champagne bg-white hover:bg-cream text-brown font-semibold text-sm transition-all duration-200">
                    <i class="fa-solid fa-bag-shopping text-xs"></i>
                    Browse Products
                </a>
            </div>

        </div>
    </section>
@endsection
