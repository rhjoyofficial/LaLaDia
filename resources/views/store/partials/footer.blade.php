<footer style="background: var(--color-surface); border-top: 1px solid var(--color-border);" class="pt-12 pb-6">
    <div class="max-w-8xl mx-auto px-4 md:px-8">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

            {{-- ── Column 1: Brand ── --}}
            <div class="space-y-5 flex flex-col items-center md:items-start justify-center md:justify-start">
                <a href="{{ route('home') }}" class="block">
                    <img src="{{ asset('assets/images/laladia-logo.png') }}" alt="LaLaDia"
                        class="h-16 md:h-20 object-contain"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                    <span class="text-2xl font-heading font-bold hidden"
                        style="color: var(--color-primary);">LaLaDia</span>
                </a>
                <p class="text-sm leading-relaxed" style="color: var(--color-text-muted);">
                    Luxary Artisanal Foods
                </p>
                {{-- Social icons --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest mb-3 text-center md:text-left"
                        style="color: var(--color-text-muted);">
                        Follow Us</p>
                    <div class="flex gap-2">
                        @php
                            $socials = [
                                'facebook' => [
                                    'url' => 'https://www.facebook.com/LaLaDiaGlobal',
                                    'label' => 'Facebook',
                                ],
                                'youtube' => [
                                    'url' => 'https://www.youtube.com/@LaLaDiaGlobal',
                                    'label' => 'YouTube',
                                ],
                                'whatsapp' => ['url' => 'https://wa.me/8801733358158', 'label' => 'WhatsApp'],
                            ];
                        @endphp
                        @foreach ($socials as $key => $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                class="w-9 h-9 flex items-center justify-center rounded-xl transition-all duration-200"
                                style="background: var(--color-bg-soft); border: 1px solid var(--color-border); color: var(--color-text-muted);"
                                onmouseover="this.style.background='var(--color-primary)'; this.style.color='white'; this.style.borderColor='var(--color-primary)'"
                                onmouseout="this.style.background='var(--color-bg-soft)'; this.style.color='var(--color-text-muted)'; this.style.borderColor='var(--color-border)'"
                                aria-label="{{ $social['label'] }}">
                                @if ($key === 'facebook')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 320 512">
                                        <path
                                            d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z" />
                                    </svg>
                                @elseif ($key === 'youtube')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 576 512">
                                        <path
                                            d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z" />
                                    </svg>
                                @elseif ($key === 'whatsapp')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512">
                                        <path
                                            d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── Column 2: Quick Links ── --}}
            <div>
                <h4 class="text-sm font-bold uppercase tracking-widest mb-5" style="color: var(--color-text);">
                    Quick Links
                </h4>
                <ul class="space-y-3">
                    @php
                        $quickLinks = [
                            ['label' => 'Home', 'route' => 'home'],
                            ['label' => 'Products', 'route' => 'product.index'],
                            ['label' => 'Combos', 'route' => 'combos.index'],
                            ['label' => 'Cart', 'route' => 'cart.view'],
                            ['label' => 'About Us', 'route' => 'about'],
                            ['label' => 'Gallery', 'route' => 'gallery'],
                        ];
                    @endphp
                    @foreach ($quickLinks as $link)
                        @if (Route::has($link['route']))
                            <li>
                                <a href="{{ route($link['route']) }}"
                                    class="text-sm flex items-center gap-2 transition-colors duration-200"
                                    style="color: var(--color-text-muted);"
                                    onmouseover="this.style.color='var(--color-primary)'"
                                    onmouseout="this.style.color='var(--color-text-muted)'">
                                    <span class="w-1 h-1 rounded-full flex-shrink-0"
                                        style="background: var(--color-primary);"></span>
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>

            {{-- ── Column 3: Support ── --}}
            <div>
                <h4 class="text-sm font-bold uppercase tracking-widest mb-5" style="color: var(--color-text);">
                    Support
                </h4>
                <ul class="space-y-3">
                    @php
                        $supportLinks = [
                            ['label' => 'Contact Us', 'route' => 'contact'],
                            ['label' => 'FAQ', 'route' => 'faq'],
                            ['label' => 'Blog', 'route' => 'blog'],
                            ['label' => 'Privacy Policy', 'route' => 'privacy'],
                            ['label' => 'Terms & Conditions', 'route' => 'terms'],
                        ];
                    @endphp
                    @foreach ($supportLinks as $link)
                        @if (Route::has($link['route']))
                            <li>
                                <a href="{{ route($link['route']) }}"
                                    class="text-sm flex items-center gap-2 transition-colors duration-200"
                                    style="color: var(--color-text-muted);"
                                    onmouseover="this.style.color='var(--color-primary)'"
                                    onmouseout="this.style.color='var(--color-text-muted)'">
                                    <span class="w-1 h-1 rounded-full flex-shrink-0"
                                        style="background: var(--color-primary);"></span>
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>

            {{-- ── Column 4: Contact ── --}}
            <div>
                <h4 class="text-sm font-bold uppercase tracking-widest mb-5" style="color: var(--color-text);">
                    Contact Us
                </h4>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                            style="background: var(--color-bg-soft); border: 1px solid var(--color-border);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color: var(--color-primary);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-sm" style="color: var(--color-text-muted);">
                            65, Feroza Garden, Shahid Smriti Sarak, Barguna-8700, Bangladesh
                        </span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                            style="background: var(--color-bg-soft); border: 1px solid var(--color-border);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color: var(--color-primary);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <a href="tel:+8801733358158" class="text-sm transition-colors duration-200"
                            style="color: var(--color-text-muted);"
                            onmouseover="this.style.color='var(--color-primary)'"
                            onmouseout="this.style.color='var(--color-text-muted)'">
                            +880 1733-358158
                        </a>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                            style="background: var(--color-bg-soft); border: 1px solid var(--color-border);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color: var(--color-primary);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <a href="mailto:care@laladia.com" class="text-sm transition-colors duration-200"
                            style="color: var(--color-text-muted);"
                            onmouseover="this.style.color='var(--color-primary)'"
                            onmouseout="this.style.color='var(--color-text-muted)'">
                            care@laladia.com
                        </a>
                    </li>
                </ul>

                {{-- Payment badges --}}
                @if (file_exists(public_path('assets/images/footer-bank.png')))
                    <div class="mt-5 text-center overflow-hidden">
                        <img src="{{ asset('assets/images/footer-bank.png') }}" alt="Payment Methods"
                            class="h-12 w-auto object-contain opacity-70">
                    </div>
                @endif
            </div>

        </div>

        {{-- ── Bottom bar ── --}}
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4"
            style="border-top: 1px solid var(--color-border);">
            <p class="text-xs" style="color: var(--color-text-muted);">
                &copy; {{ date('Y') }} LaLaDia. All rights reserved.
            </p>
            <div class="flex items-center gap-5">
                @if (Route::has('terms'))
                    <a href="{{ route('terms') }}" class="text-xs transition-colors duration-200"
                        style="color: var(--color-text-muted);" onmouseover="this.style.color='var(--color-primary)'"
                        onmouseout="this.style.color='var(--color-text-muted)'">Terms</a>
                @endif
                @if (Route::has('privacy'))
                    <a href="{{ route('privacy') }}" class="text-xs transition-colors duration-200"
                        style="color: var(--color-text-muted);" onmouseover="this.style.color='var(--color-primary)'"
                        onmouseout="this.style.color='var(--color-text-muted)'">Privacy</a>
                @endif
                @if (Route::has('disclaimer'))
                    <a href="{{ route('disclaimer') }}" class="text-xs transition-colors duration-200"
                        style="color: var(--color-text-muted);" onmouseover="this.style.color='var(--color-primary)'"
                        onmouseout="this.style.color='var(--color-text-muted)'">Disclaimer</a>
                @endif
            </div>
            <p class="text-xs" style="color: var(--color-text-placeholder);">
                Made with ♥ in Bangladesh
            </p>
        </div>

    </div>
</footer>
