@php
    // Use the passed certifications or fetch active ones as a fallback
    $allCerts = (isset($certifications) && $certifications->isNotEmpty()) 
        ? $certifications->flatten()->sortBy('sort_order') 
        : \App\Domains\Certification\Models\Certification::where('is_active', true)->orderBy('sort_order', 'asc')->get();
@endphp

<section class="py-20 relative overflow-hidden bg-white">
    {{-- Subtle Background Elements --}}
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
    <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>

    <div class="max-w-7xl mx-auto px-4 md:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <span class="text-[10px] font-bold uppercase tracking-[0.3em] mb-4 block text-gold-600">
                Quality & Standards
            </span>
            <h2 class="text-3xl md:text-4xl font-serif font-medium text-black mb-4">
                Global Certifications
            </h2>
            <div class="w-16 h-0.5 bg-gold-400 mx-auto mb-6"></div>
            <p class="text-sm md:text-base text-gray-500 max-w-2xl mx-auto leading-relaxed">
                LaLaDia is committed to the highest standards of food safety and quality management. 
                Our products are processed in certified facilities meeting international compliance.
            </p>
        </div>

        {{-- Certification Grid - Responsive Carousel on Mobile only --}}
        <div class="flex flex-nowrap lg:grid lg:grid-cols-4 gap-6 overflow-x-auto lg:overflow-x-visible pb-8 lg:pb-0 snap-x justify-start lg:justify-center items-center hide-scrollbar">
            @foreach ($allCerts as $cert)
                <div class="cert-card group relative bg-gray-50/50 border border-gray-100 p-6 md:p-8 rounded-xl transition-all duration-500 hover:bg-white hover:border-gold-200 hover:shadow-xl hover:shadow-gold-900/5 cursor-pointer shrink-0 w-[240px] md:w-auto snap-center"
                     data-img="{{ $cert->image_url }}"
                     data-name="{{ $cert->name }}"
                     data-details="{{ $cert->additional_details }}">
                    
                    {{-- Logo Container --}}
                    <div class="aspect-square flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110">
                        <img src="{{ $cert->logo_url }}" 
                             alt="{{ $cert->name }}" 
                             class="max-h-24 md:max-h-32 w-auto object-contain grayscale group-hover:grayscale-0 transition-all duration-500 opacity-70 group-hover:opacity-100"
                             loading="lazy">
                    </div>

                    {{-- Info --}}
                    <div class="text-center">
                        <h3 class="text-sm md:text-base font-bold text-gray-900 mb-1 group-hover:text-gold-700 transition-colors duration-300">
                            {{ $cert->name }}
                        </h3>
                        <p class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider group-hover:text-gray-500 transition-colors duration-300">
                            {{ $cert->category ?? 'Safety Standard' }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Cert lightbox --}}
<div id="certModal"
     class="fixed inset-0 z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-all duration-500"
     style="background: rgba(0,0,0,0.92); backdrop-filter: blur(8px);">

    <button id="certModalClose"
            class="absolute top-6 right-6 w-12 h-12 rounded-full flex items-center justify-center cursor-pointer transition-all duration-300 z-[101] hover:rotate-90"
            style="background: rgba(255,255,255,0.05); color: white;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <div class="relative max-w-[95vw] max-h-[90vh] flex flex-col items-center">
        <img id="certModalImg"
             class="max-h-[75vh] w-auto object-contain rounded-lg shadow-2xl transition-all duration-500 scale-90"
             src="" alt="Certification">
        
        <div class="mt-8 text-center text-white max-w-2xl px-6 opacity-0 translate-y-4 transition-all duration-500 delay-200" id="certModalText">
            <h4 class="text-xl font-serif text-gold-400 mb-2" id="certModalName"></h4>
            <p class="text-sm text-gray-400 leading-relaxed font-light" id="certModalDetails"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal       = document.getElementById('certModal');
        const modalImg    = document.getElementById('certModalImg');
        const modalText   = document.getElementById('certModalText');
        const modalName   = document.getElementById('certModalName');
        const modalDetails= document.getElementById('certModalDetails');
        const closeBtn    = document.getElementById('certModalClose');

        function openModal(src, name, details) {
            modalImg.src = src;
            modalName.textContent = name;
            modalDetails.textContent = details;
            modal.classList.remove('opacity-0', 'pointer-events-none');
            setTimeout(() => {
                modalImg.classList.remove('scale-90');
                modalText.classList.remove('opacity-0', 'translate-y-4');
            }, 50);
        }

        function closeModal() {
            modal.classList.add('opacity-0', 'pointer-events-none');
            modalImg.classList.add('scale-90');
            modalText.classList.add('opacity-0', 'translate-y-4');
        }

        document.querySelectorAll('.cert-card').forEach(card => {
            card.addEventListener('click', () => {
                if (card.dataset.img) openModal(card.dataset.img, card.dataset.name, card.dataset.details);
            });
        });

        modal.addEventListener('click', e => {
            if (e.target === modal || e.target.closest('#certModalClose')) closeModal();
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });
    });
</script>
@endpush
