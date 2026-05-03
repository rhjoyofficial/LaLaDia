{{-- Floating Cart Button — desktop only (mobile uses bottom nav) --}}
<button id="floatingCartButton"
        onclick="toggleCart()"
        class="fixed hidden md:flex items-center justify-center z-30 cursor-pointer transition-all duration-300 hover:scale-110 active:scale-95"
        style="bottom: 6rem; right: 1.25rem; width: 3.5rem; height: 3.5rem; border-radius: 50%; background: var(--color-primary); color: white; box-shadow: 0 4px 20px rgba(var(--color-primary-rgb),0.4); border: 2px solid rgba(255,255,255,0.2);"
        onmouseover="this.style.background='var(--color-primary-hover)'; this.style.boxShadow='0 6px 28px rgba(var(--color-primary-rgb),0.55)'"
        onmouseout="this.style.background='var(--color-primary)'; this.style.boxShadow='0 4px 20px rgba(var(--color-primary-rgb),0.4)'">

    <div class="relative">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 01-8 0"/>
        </svg>

        <span id="cartCountBadge"
              class="absolute -top-2 -right-2.5 min-w-[1.1rem] h-[1.1rem] flex items-center justify-center rounded-full text-[9px] font-black"
              style="background: white; color: var(--color-primary); box-shadow: 0 0 0 2px var(--color-primary);">
            0
        </span>
    </div>

</button>

