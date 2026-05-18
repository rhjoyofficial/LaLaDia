<header class="sticky top-0 z-40 transition-all duration-300"
    style="background: var(--color-surface); border-bottom: 1px solid var(--color-border);">

    {{-- ── Thin gold accent line ── --}}
    <div class="h-0.5 w-full"
        style="background: linear-gradient(to right, transparent, var(--color-primary), transparent);"></div>

    {{-- ══ MAIN HEADER ROW ══ --}}
    <div class="max-w-8xl mx-auto px-4 flex items-center justify-between gap-4" style="height: 64px;">

        {{-- LEFT: Logo --}}
        <a href="{{ route('home') }}" class="shrink-0 flex items-center gap-2.5 group">
            <img src="{{ asset('assets/images/laladia-logo.png') }}" alt="{{ config('app.name', 'LaLaDia') }}"
                class="h-9 md:h-12 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                fetchpriority="high" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <span class="hidden items-center font-heading font-bold text-xl tracking-tight"
                style="color: var(--color-primary);">
                LaLaDia
            </span>
        </a>


        {{-- CENTER: Navigation (desktop only) --}}
        <nav class="hidden md:flex items-center gap-1 flex-1 justify-center">

            @php
                $navLink = fn($label, $routeName, array $patterns = []) => [
                    'label' => $label,
                    'href' => route($routeName),
                    'active' => request()->routeIs($routeName, ...$patterns),
                ];
                $links = [
                    $navLink('Home', 'home'),
                    $navLink('Products', 'product.index', ['catalog', 'product.show']),
                    $navLink('Combos', 'combos.index', ['combos.*']),
                ];
            @endphp

            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                    class="relative px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
                    style="color: {{ $link['active'] ? 'var(--color-primary)' : 'var(--color-text-muted)' }};"
                    onmouseover="if(!{{ $link['active'] ? 'true' : 'false' }}) this.style.color='var(--color-text)'; this.style.background='var(--color-bg)';"
                    onmouseout="if(!{{ $link['active'] ? 'true' : 'false' }}) this.style.color='var(--color-text-muted)'; this.style.background='transparent';">
                    {{ $link['label'] }}
                    @if ($link['active'])
                        <span class="absolute bottom-0.5 left-4 right-4 h-0.5 rounded-full"
                            style="background: var(--color-primary);"></span>
                    @endif
                </a>
            @endforeach

            {{-- Categories dropdown --}}
            <div class="relative group/cat">
                <button id="categoriesButton"
                    class="flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 cursor-pointer"
                    style="color: var(--color-text-muted);"
                    onmouseover="this.style.color='var(--color-text)'; this.style.background='var(--color-bg)';"
                    onmouseout="this.style.color='var(--color-text-muted)'; this.style.background='transparent';">
                    Categories
                    <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover/cat:rotate-180" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="categoriesDropdown"
                    class="absolute left-0 top-full pt-2 w-56 opacity-0 invisible group-hover/cat:opacity-100 group-hover/cat:visible transition-all duration-200 z-50">
                    <div class="rounded-xl overflow-hidden shadow-xl py-1"
                        style="background: var(--color-surface); border: 1px solid var(--color-border);">
                        @foreach ($globalCategories as $category)
                            <a href="{{ $category->category_page }}"
                                class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition-all duration-150"
                                style="color: var(--color-text-muted);"
                                onmouseover="this.style.color='var(--color-primary)'; this.style.background='var(--color-bg)';"
                                onmouseout="this.style.color='var(--color-text-muted)'; this.style.background='transparent';">
                                <span class="w-1.5 h-1.5 rounded-full shrink-0"
                                    style="background: var(--color-border-soft);"></span>
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

        </nav>

        {{-- RIGHT: Actions --}}
        <div class="flex items-center gap-1 shrink-0">

            {{-- Search toggle (desktop) --}}
            <button id="headerSearchToggle"
                class="hidden md:flex items-center justify-center w-9 h-9 rounded-xl transition-all duration-200 cursor-pointer"
                style="color: var(--color-text-muted);"
                onmouseover="this.style.color='var(--color-primary)'; this.style.background='var(--color-bg)';"
                onmouseout="this.style.color='var(--color-text-muted)'; this.style.background='transparent';"
                aria-label="Search">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>

            {{-- Divider --}}
            <span class="hidden md:block w-px h-5 mx-1" style="background: var(--color-border);"></span>

            {{-- Cart --}}
            <button onclick="toggleCart()"
                class="flex items-center gap-2 px-3 py-2 rounded-xl transition-all duration-200 group/cart cursor-pointer relative"
                onmouseover="this.style.background='var(--color-bg)';"
                onmouseout="this.style.background='transparent';">
                <div class="relative">
                    <svg class="w-5 h-5 transition-colors duration-200" style="color: var(--color-text-muted);"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span id="cartCount"
                        class="absolute -top-2 -right-2 min-w-4.5 h-4.5 px-1 rounded-full flex items-center justify-center text-[10px] font-black border-2 leading-none"
                        style="background: var(--color-primary); color: white; border-color: var(--color-surface);">
                        0
                    </span>
                </div>
                <span class="text-sm font-semibold hidden sm:block transition-colors duration-200"
                    style="color: var(--color-text-muted);">
                    Cart
                </span>
            </button>

            {{-- Divider --}}
            <span class="w-px h-5 mx-1" style="background: var(--color-border);"></span>

            {{-- Account --}}
            @auth
                <div class="relative group/acc">
                    <button
                        class="flex items-center gap-2 px-2 py-1.5 rounded-xl transition-all duration-200 cursor-pointer"
                        onmouseover="this.style.background='var(--color-bg)';"
                        onmouseout="this.style.background='transparent';">
                        <span
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                            style="background: var(--color-primary);">
                            {{ collect(explode(' ', auth()->user()->name))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('') }}
                        </span>
                        <span class="text-sm font-semibold hidden md:block max-w-24 truncate"
                            style="color: var(--color-text-secondary);">
                            {{ auth()->user()->name }}
                        </span>
                        <svg class="w-3.5 h-3.5 hidden md:block transition-transform duration-200 group-hover/acc:rotate-180"
                            style="color: var(--color-text-muted);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Account dropdown --}}
                    <div
                        class="absolute right-0 top-full pt-2 w-72 opacity-0 invisible group-hover/acc:opacity-100 group-hover/acc:visible transition-all duration-200 z-50">
                        <div class="rounded-2xl shadow-xl overflow-hidden"
                            style="background: var(--color-surface); border: 1px solid var(--color-border);">

                            {{-- User info header --}}
                            <div class="p-4"
                                style="background: var(--color-bg); border-bottom: 1px solid var(--color-border);">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold text-white shrink-0"
                                        style="background: var(--color-primary);">
                                        {{ collect(explode(' ', auth()->user()->name))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('') }}
                                    </span>
                                    <div class="overflow-hidden">
                                        <p class="font-bold text-sm truncate" style="color: var(--color-text-secondary);">
                                            {{ auth()->user()->name }}
                                        </p>
                                        <p class="text-xs truncate" style="color: var(--color-text-muted);">
                                            {{ auth()->user()->email }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Menu items --}}
                            <div class="p-2 space-y-0.5">
                                <a href="{{ route('customer.dashboard') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150"
                                    style="color: var(--color-text-muted);"
                                    onmouseover="this.style.background='var(--color-bg)'; this.style.color='var(--color-text)';"
                                    onmouseout="this.style.background='transparent'; this.style.color='var(--color-text-muted)';">
                                    <i class="fa-regular fa-circle-user w-4 text-center"
                                        style="color: var(--color-text-placeholder);"></i>
                                    My Account
                                </a>

                                <a href="{{ route('customer.orders') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150"
                                    style="color: var(--color-text-muted);"
                                    onmouseover="this.style.background='var(--color-bg)'; this.style.color='var(--color-text)';"
                                    onmouseout="this.style.background='transparent'; this.style.color='var(--color-text-muted)';">
                                    <i class="fa-solid fa-bag-shopping w-4 text-center"
                                        style="color: var(--color-text-placeholder);"></i>
                                    My Orders
                                </a>

                                <div class="my-1" style="border-top: 1px solid var(--color-border);"></div>

                                <button id="logoutBtn"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 cursor-pointer text-left"
                                    style="color: var(--color-text-muted);"
                                    onmouseover="this.style.background='rgba(239,68,68,0.06)'; this.style.color='var(--color-danger)';"
                                    onmouseout="this.style.background='transparent'; this.style.color='var(--color-text-muted)';">
                                    <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                    Logout
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            @endauth

            @guest
                <a href="{{ route('login') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold transition-all duration-200"
                    style="color: var(--color-text-muted);"
                    onmouseover="this.style.background='var(--color-bg)'; this.style.color='var(--color-primary)';"
                    onmouseout="this.style.background='transparent'; this.style.color='var(--color-text-muted)';">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="hidden sm:block">Sign In</span>
                </a>
            @endguest

        </div>
    </div>

    {{-- ══ DESKTOP SEARCH PANEL (toggleable) ══ --}}
    <div id="headerSearchPanel" class="hidden border-t"
        style="border-color: var(--color-border); background: var(--color-bg);">
        <div class="max-w-xl mx-auto px-4 py-3">
            <div class="flex items-center gap-0 rounded-xl overflow-visible relative shadow-sm"
                style="background: var(--color-surface); border: 1px solid var(--color-border);">

                {{-- Categories dropdown inside search --}}
                <div class="relative group/srchcat shrink-0">
                    <button
                        class="flex items-center gap-2 px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors duration-150 cursor-pointer"
                        style="color: var(--color-text-muted); border-right: 1px solid var(--color-border);">
                        All Categories
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover/srchcat:rotate-180"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        class="absolute left-0 top-full pt-1 w-56 opacity-0 invisible group-hover/srchcat:opacity-100 group-hover/srchcat:visible transition-all duration-200 z-50">
                        <div class="rounded-xl overflow-hidden shadow-xl py-1"
                            style="background: var(--color-surface); border: 1px solid var(--color-border);">
                            @foreach ($globalCategories as $category)
                                <a href="{{ $category->category_page }}"
                                    class="block px-4 py-2.5 text-sm transition-colors duration-150"
                                    style="color: var(--color-text-muted);"
                                    onmouseover="this.style.color='var(--color-primary)'; this.style.background='var(--color-bg)';"
                                    onmouseout="this.style.color='var(--color-text-muted)'; this.style.background='transparent';">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Search input --}}
                <div class="flex-1 relative">
                    <input type="text" id="searchInput" placeholder="Search products, categories..."
                        class="w-full px-5 py-3 text-sm outline-none bg-transparent"
                        style="color: var(--color-text); caret-color: var(--color-primary);" autocomplete="off">
                    <div id="searchSuggestions"
                        class="absolute left-0 top-full mt-1 w-full rounded-xl shadow-xl hidden z-50 max-h-96 overflow-y-auto no-scrollbar"
                        style="background: var(--color-surface); border: 1px solid var(--color-border);">
                    </div>
                </div>

                {{-- Search button --}}
                <button id="searchButton"
                    class="flex items-center gap-2 m-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-all duration-200 active:scale-95 cursor-pointer shrink-0"
                    style="background: var(--color-primary);"
                    onmouseover="this.style.background='var(--color-primary-hover)';"
                    onmouseout="this.style.background='var(--color-primary)';">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search
                </button>

            </div>
        </div>
    </div>

    {{-- ══ MOBILE SEARCH BAR ══ --}}
    <div class="md:hidden px-4 py-2.5"
        style="border-top: 1px solid var(--color-border); background: var(--color-bg);">
        <div class="flex items-center gap-0 rounded-xl overflow-hidden relative shadow-sm"
            style="background: var(--color-surface); border: 1px solid var(--color-border);">
            <input id="searchInputMobile" class="flex-1 px-4 py-2.5 text-sm outline-none bg-transparent"
                style="color: var(--color-text); caret-color: var(--color-primary);"
                placeholder="Search products, categories..." autocomplete="off">
            <div id="searchSuggestionsMobile"
                class="absolute left-0 top-full mt-1 w-full rounded-xl shadow-xl hidden z-50 max-h-80 overflow-y-auto no-scrollbar"
                style="background: var(--color-surface); border: 1px solid var(--color-border);">
            </div>
            <button id="searchButtonMobile"
                class="px-5 py-2.5 text-sm font-bold text-white shrink-0 transition-all duration-200 active:scale-95 cursor-pointer"
                style="background: var(--color-primary);"
                onmouseover="this.style.background='var(--color-primary-hover)';"
                onmouseout="this.style.background='var(--color-primary)';">
                Go
            </button>
        </div>
    </div>

    {{-- ══ MOBILE CATEGORIES PANEL ══ --}}
    <div id="mobileDropdown" class="hidden md:hidden"
        style="border-top: 1px solid var(--color-border); background: var(--color-surface);">
        <div class="max-h-[55vh] overflow-y-auto no-scrollbar">
            @foreach ($globalCategories as $category)
                <a href="{{ $category->category_page }}"
                    class="flex items-center gap-3 px-5 py-3 text-sm transition-colors duration-150"
                    style="color: var(--color-text-muted); border-bottom: 1px solid var(--color-border);"
                    onmouseover="this.style.color='var(--color-primary)'; this.style.background='var(--color-bg)';"
                    onmouseout="this.style.color='var(--color-text-muted)'; this.style.background='transparent';">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0"
                        style="background: var(--color-border-soft);"></span>
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <script>
        (function() {
            const toggleBtn = document.getElementById('headerSearchToggle');
            const panel = document.getElementById('headerSearchPanel');
            const input = document.getElementById('searchInput');

            if (!toggleBtn || !panel) return;

            toggleBtn.addEventListener('click', function() {
                const isHidden = panel.classList.contains('hidden');
                panel.classList.toggle('hidden', !isHidden);
                if (isHidden) {
                    setTimeout(() => input?.focus(), 50);
                    toggleBtn.style.color = 'var(--color-primary)';
                    toggleBtn.style.background = 'var(--color-bg)';
                } else {
                    toggleBtn.style.color = 'var(--color-text-muted)';
                    toggleBtn.style.background = 'transparent';
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !panel.classList.contains('hidden')) {
                    panel.classList.add('hidden');
                    toggleBtn.style.color = 'var(--color-text-muted)';
                    toggleBtn.style.background = 'transparent';
                }
            });
        })();
    </script>

</header>
