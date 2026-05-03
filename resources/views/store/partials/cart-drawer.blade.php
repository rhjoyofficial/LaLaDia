{{-- CART OVERLAY --}}
<div id="overlay"
     class="fixed inset-0 opacity-0 invisible transition-all duration-300 z-40"
     style="background: rgba(0,0,0,0.45); backdrop-filter: blur(2px);">
</div>

{{-- CART DRAWER --}}
<aside id="cartDrawer"
       class="fixed top-0 right-0 h-full w-full sm:w-96 translate-x-full z-50 flex flex-col"
       style="background: var(--color-surface); box-shadow: -8px 0 40px rgba(0,0,0,0.12); transition: transform 0.3s cubic-bezier(.16,1,.3,1);">

    {{-- HEADER --}}
    <div class="flex items-center justify-between px-5 py-4 sticky top-0 z-10"
         style="border-bottom: 1px solid var(--color-border); background: var(--color-surface);">

        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center"
                 style="background: rgba(var(--color-primary-rgb),0.1);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: var(--color-primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 01-8 0"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-base leading-none" style="color: var(--color-text);">Your Cart</h3>
                <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">LaLaDia</p>
            </div>
        </div>

        <button onclick="Cart.close()"
                class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200 cursor-pointer"
                style="color: var(--color-text-muted);"
                onmouseover="this.style.background='var(--color-bg-soft)'; this.style.color='var(--color-text)'"
                onmouseout="this.style.background='transparent'; this.style.color='var(--color-text-muted)'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- SCROLL BODY --}}
    <div id="cartItems" class="flex-1 overflow-y-auto overscroll-contain no-scrollbar"
         style="background: var(--color-bg);">
        {{-- CartRenderer injects items here --}}
    </div>

    {{-- FOOTER --}}
    <div class="px-5 pt-4 pb-5 sticky bottom-0"
         style="border-top: 1px solid var(--color-border); background: var(--color-surface);">

        {{-- Subtotal row --}}
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs" style="color: var(--color-text-muted);">Subtotal</p>
                <p id="cartSubtotal" class="font-bold text-xl font-bengali"
                   style="color: var(--color-primary);">৳0</p>
            </div>

            <button onclick="window.Cart.clear()"
                    class="flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-lg cursor-pointer transition-all duration-200"
                    style="color: var(--color-danger); background: rgba(239,68,68,0.06);"
                    onmouseover="this.style.background='rgba(239,68,68,0.12)'"
                    onmouseout="this.style.background='rgba(239,68,68,0.06)'">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Clear Cart
            </button>
        </div>

        {{-- CTA Buttons --}}
        <div class="flex gap-3">
            <a href="{{ route('cart.view') }}" class="btn-outline flex-1 py-2.5 text-sm font-semibold text-center rounded-xl">
                View Cart
            </a>
            <button onclick="Cart.checkout()"
                    class="btn-primary flex-1 py-2.5 text-sm font-semibold cursor-pointer active:scale-[.97]">
                Checkout
            </button>
        </div>

        {{-- Mini trust strip --}}
        <div class="flex justify-center gap-4 mt-3">
            <span class="flex items-center gap-1 text-[10px]" style="color: var(--color-text-muted);">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Secure
            </span>
            <span class="flex items-center gap-1 text-[10px]" style="color: var(--color-text-muted);">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Quality Guaranteed
            </span>
            <span class="flex items-center gap-1 text-[10px]" style="color: var(--color-text-muted);">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
                Fast Delivery
            </span>
        </div>
    </div>

</aside>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const overlay = document.getElementById('overlay');

        overlay?.addEventListener('click', () => window.Cart.close());

        window.toggleCart = () => {
            const isOpen = !document.getElementById('cartDrawer').classList.contains('translate-x-full');
            isOpen ? window.Cart.close() : window.Cart.open();
        };

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') window.Cart.close();
        });
    });
</script>
@endpush

