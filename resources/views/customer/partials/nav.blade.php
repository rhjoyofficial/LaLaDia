<aside class="card p-5 h-fit">
    <p class="text-xs font-bold uppercase tracking-widest mb-4" style="color: var(--color-text-muted);">My Account</p>
    <nav class="space-y-0.5">
        @php
            $navLinks = [
                ['label' => 'Dashboard', 'route' => 'customer.dashboard', 'match' => ['customer.dashboard']],
                ['label' => 'My Orders', 'route' => 'customer.orders',    'match' => ['customer.orders', 'customer.order-details']],
                ['label' => 'Profile',   'route' => 'customer.profile',   'match' => ['customer.profile']],
            ];
        @endphp

        @foreach ($navLinks as $link)
            @php $active = request()->routeIs(...$link['match']); @endphp
            <a href="{{ route($link['route']) }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
               style="{{ $active
                    ? 'background: rgba(var(--color-primary-rgb),0.12); color: var(--color-primary); font-weight: 600;'
                    : 'color: var(--color-text-muted);' }}"
               @if (!$active)
                   onmouseover="this.style.background='var(--color-bg-soft)'; this.style.color='var(--color-text)'"
                   onmouseout="this.style.background='transparent'; this.style.color='var(--color-text-muted)'"
               @endif>
                {{ $link['label'] }}
                @if ($active)
                    <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background: var(--color-primary);"></span>
                @endif
            </a>
        @endforeach

        <div class="pt-3 mt-3" style="border-top: 1px solid var(--color-border);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 cursor-pointer text-left"
                        style="color: var(--color-danger);"
                        onmouseover="this.style.background='rgba(239,68,68,0.07)'"
                        onmouseout="this.style.background='transparent'">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </nav>
</aside>

