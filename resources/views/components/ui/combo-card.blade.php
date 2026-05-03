@props(['combo'])

<div class="card overflow-hidden flex flex-col h-full group transition-all duration-300 hover:shadow-lg hover:scale-[1.02]">

    {{-- Image --}}
    <div class="relative aspect-square overflow-hidden" style="background: var(--color-bg-soft);">
        <img src="{{ $combo->image_url ?? asset('images/placeholder.png') }}"
             alt="{{ $combo->title }}"
             loading="lazy"
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">

        @if ($combo->total_savings > 0)
            <div class="absolute top-2 left-2">
                <span class="badge-primary text-xs font-bold font-bengali px-2 py-0.5 rounded-md">
                    Save ৳{{ number_format($combo->total_savings) }}
                </span>
            </div>
        @endif

        <div class="absolute top-2 right-2">
            <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-md"
                  style="background: rgba(255,255,255,0.92); color: var(--color-primary); backdrop-filter: blur(4px);">
                Combo
            </span>
        </div>
    </div>

    {{-- Body --}}
    <div class="flex flex-col flex-1 p-4">

        <a href="{{ route('combos.show', $combo->slug) }}"
           class="font-semibold text-sm leading-snug line-clamp-2 mb-1 transition-colors duration-200"
           style="color: var(--color-text);"
           onmouseover="this.style.color='var(--color-primary)'"
           onmouseout="this.style.color='var(--color-text)'">
            {{ $combo->title }}
        </a>

        <p class="text-xs mb-3 truncate" style="color: var(--color-text-muted);"
           title="{{ $combo->items->map(fn($i) => $i->variant?->product?->name)->filter()->implode(' • ') }}">
            {{ $combo->items->map(fn($i) => $i->variant?->product?->name)->filter()->implode(' • ') }}
        </p>

        <div class="flex items-baseline gap-2 mt-auto mb-4 font-bengali">
            <span class="text-lg font-bold" style="color: var(--color-primary);">
                ৳{{ number_format($combo->final_price) }}
            </span>
            @if ($combo->pricing_mode === 'manual' || $combo->discount_value > 0)
                <span class="text-sm line-through" style="color: var(--color-text-muted);">
                    ৳{{ number_format($combo->auto_price) }}
                </span>
            @endif
        </div>

        <button class="addComboBtn btn-primary w-full py-2 text-sm font-semibold cursor-pointer"
                data-combo="{{ $combo->id }}">
            Add to Cart
        </button>

    </div>

</div>

