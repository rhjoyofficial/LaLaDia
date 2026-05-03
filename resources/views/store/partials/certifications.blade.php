@php
    $allCerts = $certifications->flatten()->values();
@endphp

@push('styles')
<style>
    .cert-track-wrap:hover .cert-track { animation-play-state: paused; }
</style>
@endpush

<section class="py-16 overflow-hidden"
         style="background: rgba(var(--color-primary-rgb),0.05); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">

    <div class="max-w-7xl mx-auto px-4 md:px-8 text-center mb-10">
        <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: var(--color-primary);">
            Trust & Compliance
        </p>
        <h2 class="text-2xl md:text-3xl font-bold" style="color: var(--color-text);">
            Certified & Quality Assured
        </h2>
        <p class="text-sm mt-2 max-w-xl mx-auto" style="color: var(--color-text-muted);">
            Every LaLaDia product meets rigorous global compliance and quality safety standards.
        </p>
    </div>

    <div class="relative overflow-hidden cert-track-wrap">

        <div class="flex flex-nowrap min-w-max gap-6 marquee-left cert-track">
            @foreach ($allCerts as $cert)
                <div class="cert-card cursor-pointer shrink-0 transition-transform duration-200 hover:scale-105"
                     data-img="{{ $cert->image_url }}"
                     title="{{ $cert->name }}">
                    <div class="cert-canvas">
                        <img src="{{ $cert->logo_url }}" alt="{{ $cert->name }}"
                             loading="lazy" class="cert-img">
                    </div>
                </div>
            @endforeach

            {{-- Duplicate for seamless infinite loop --}}
            @foreach ($allCerts as $cert)
                <div class="cert-card cursor-pointer shrink-0 transition-transform duration-200 hover:scale-105"
                     aria-hidden="true"
                     data-img="{{ $cert->image_url }}"
                     title="{{ $cert->name }}">
                    <div class="cert-canvas">
                        <img src="{{ $cert->logo_url }}" alt="{{ $cert->name }}"
                             loading="lazy" class="cert-img">
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Fade edges --}}
        <div class="pointer-events-none absolute inset-y-0 left-0 w-20"
             style="background: linear-gradient(to right, rgba(var(--color-bg-rgb),1) 0%, transparent 100%);"></div>
        <div class="pointer-events-none absolute inset-y-0 right-0 w-20"
             style="background: linear-gradient(to left, rgba(var(--color-bg-rgb),1) 0%, transparent 100%);"></div>

    </div>

</section>

{{-- Cert lightbox --}}
<div id="certModal"
     class="fixed inset-0 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300"
     style="background: rgba(0,0,0,0.82); backdrop-filter: blur(6px);">

    <button id="certModalClose"
            class="absolute top-4 right-4 w-10 h-10 rounded-full flex items-center justify-center cursor-pointer transition-all duration-200"
            style="background: rgba(255,255,255,0.12); color: white;"
            onmouseover="this.style.background='rgba(255,255,255,0.22)'"
            onmouseout="this.style.background='rgba(255,255,255,0.12)'">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <img id="certModalImg"
         class="max-h-[85vh] max-w-[90vw] object-contain rounded-2xl shadow-2xl transition-transform duration-300 scale-95"
         src="" alt="Certification">

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal    = document.getElementById('certModal');
        const modalImg = document.getElementById('certModalImg');
        const closeBtn = document.getElementById('certModalClose');

        function openModal(src) {
            modalImg.src = src;
            modal.classList.remove('opacity-0', 'pointer-events-none');
            setTimeout(() => modalImg.classList.remove('scale-95'), 20);
        }

        function closeModal() {
            modal.classList.add('opacity-0', 'pointer-events-none');
            modalImg.classList.add('scale-95');
        }

        document.querySelectorAll('.cert-card').forEach(card => {
            card.addEventListener('click', () => {
                if (card.dataset.img) openModal(card.dataset.img);
            });
        });

        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });
        closeBtn?.addEventListener('click', closeModal);

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });
    });
</script>
@endpush

