<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth no-scrollbar">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="@yield('meta_description', 'LaLaDia — Premium Honey, Ghee, Pickles & Organic Foods from Bangladesh.')">
    <meta name="keywords" content="@yield('meta_keywords', 'LaLaDia, honey, ghee, pickles, organic food, Sundarbans, Bangladesh')">

    @php
        $pageTitle = config('app.name', 'LaLaDia') . ($__env->hasSection('title') ? ' — ' . $__env->yieldContent('title') : '');
        $pageDesc  = $__env->hasSection('meta_description') ? $__env->yieldContent('meta_description') : 'LaLaDia — Premium Honey, Ghee, Pickles & Organic Foods from Bangladesh.';
        $pageImage = $__env->hasSection('meta_image') ? $__env->yieldContent('meta_image') : asset('favicon.png');
    @endphp

    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:image" content="{{ $pageImage }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    <link rel="icon" href="{{ asset('favicon.png') }}">

    <title>{{ $pageTitle }}</title>

    @include('partials.datalayer')

    @if (config('services.gtm.id'))
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ config('services.gtm.id') }}');
        </script>
    @endif

    @if (config('services.meta.pixel_id'))
        <!-- Meta Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ config('services.meta.pixel_id') }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ config('services.meta.pixel_id') }}&ev=PageView&noscript=1"/></noscript>
        <!-- End Meta Pixel Code -->
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Plus+Jakarta+Sans:wght@200..800&family=Noto+Sans+Bengali:wght@100..900&family=Playfair+Display:ital,opsz,wght@0,42..52,400..900;1,42..52,400..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')


    <style>
        /* ── Mobile bottom nav safe area ── */
        #mobileBottomNav {
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        #mobileBottomNav a {
            transition: color 0.2s ease;
        }
        #mobileBottomNav a:active {
            transform: scale(0.92);
        }
        #mobileCartBadge {
            font-size: 9px;
            font-weight: 900;
            line-height: 1;
            display: none;
        }
    </style>
    @stack('head')
</head>

<body class="antialiased font-sans no-scrollbar" style="background: var(--color-bg); color: var(--color-text);">

    @if (config('services.gtm.id'))
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id={{ config('services.gtm.id') }}" height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
    @endif

    <div class="min-h-screen flex flex-col relative">

        @unless (Route::is('login', 'register', 'password.*'))
            @include('store.partials.header')
            @unless (Route::is('checkout.*', 'order.success'))
                @include('store.partials.cart-drawer')
                @include('store.partials.cart-badge')
            @endunless
        @endunless

        <x-flash-container />

        <main class="flex-1 pb-20 md:pb-0">
            @yield('content')
        </main>

        @unless (Route::is('login', 'register', 'password.*'))
            @include('store.partials.footer')
        @endunless

    </div>

    @unless (Route::is('login', 'register', 'password.*'))

        @php
            $isHome    = request()->routeIs('home');
            $isShop    = request()->routeIs('product.index', 'product.show', 'category.view', 'combos.*') && !request()->has('q');
            $isCart    = request()->routeIs('cart.*');
            $isSearch  = request()->routeIs('product.index', 'category.view') && request()->has('q');
            $isAccount = request()->routeIs('customer.*');
        @endphp

        <nav id="mobileBottomNav"
             class="fixed bottom-0 left-0 right-0 z-50 flex md:hidden items-stretch justify-around"
             style="height: 64px; background: var(--color-surface); border-top: 1px solid var(--color-border);">

            {{-- Home --}}
            <a href="{{ route('home') }}"
               class="flex flex-col items-center justify-center gap-1 flex-1 px-1"
               style="color: {{ $isHome ? 'var(--color-primary)' : 'var(--color-text-muted)' }};">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span class="text-[10px] font-semibold leading-none tracking-wide">Home</span>
            </a>

            {{-- Products --}}
            <a href="{{ route('product.index') }}"
               class="flex flex-col items-center justify-center gap-1 flex-1 px-1"
               style="color: {{ $isShop ? 'var(--color-primary)' : 'var(--color-text-muted)' }};">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                </svg>
                <span class="text-[10px] font-semibold leading-none tracking-wide">Products</span>
            </a>

            {{-- Cart (centre, slightly elevated) --}}
            <a href="{{ route('cart.view') }}"
               class="flex flex-col items-center justify-center gap-1 flex-1 px-1"
               style="color: {{ $isCart ? 'var(--color-primary)' : 'var(--color-text-muted)' }};">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center -mt-5 shadow-lg"
                         style="background: {{ $isCart ? 'var(--color-primary)' : 'var(--color-surface)' }}; border: 2px solid {{ $isCart ? 'var(--color-primary)' : 'var(--color-border)' }};">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="{{ $isCart ? 'white' : 'currentColor' }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <path d="M16 10a4 4 0 01-8 0"/>
                        </svg>
                        <span id="mobileCartBadge"
                              class="absolute -top-1.5 -right-1.5 min-w-4 h-4 rounded-full flex items-center justify-center px-0.5"
                              style="background: var(--color-primary); color: white;">
                            0
                        </span>
                    </div>
                </div>
                <span class="text-[10px] font-semibold leading-none tracking-wide">Cart</span>
            </a>

            {{-- Search --}}
            <a href="{{ route('shop') }}?q="
               onclick="handleMobileSearch(event)"
               class="flex flex-col items-center justify-center gap-1 flex-1 px-1"
               style="color: {{ $isSearch ? 'var(--color-primary)' : 'var(--color-text-muted)' }};">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
                <span class="text-[10px] font-semibold leading-none tracking-wide">Search</span>
            </a>

            {{-- Account --}}
            <a href="{{ route('customer.dashboard') }}"
               class="flex flex-col items-center justify-center gap-1 flex-1 px-1"
               style="color: {{ $isAccount ? 'var(--color-primary)' : 'var(--color-text-muted)' }};">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span class="text-[10px] font-semibold leading-none tracking-wide">Account</span>
            </a>

        </nav>

        <script>
            /* ── Mobile cart badge sync ── */
            document.addEventListener('DOMContentLoaded', function () {
                const source = document.getElementById('cartCountBadge');
                const target = document.getElementById('mobileCartBadge');
                if (!source || !target) return;

                function sync() {
                    const n = parseInt(source.textContent.trim()) || 0;
                    target.textContent = n > 99 ? '99+' : n;
                    target.style.display = n > 0 ? 'flex' : 'none';
                }

                sync();
                new MutationObserver(sync).observe(source, { childList: true, subtree: true, characterData: true });
            });

            /* ── Search tap: focus header search if present, else navigate ── */
            function handleMobileSearch(e) {
                const input = document.querySelector('input[name="q"], #searchInputMobile, [data-search-trigger]');
                if (input) {
                    e.preventDefault();
                    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => input.focus(), 300);
                }
            }
        </script>

    @endunless

    @stack('scripts')

</body>
</html>

