@php
    $certifications =
        isset($certifications) && $certifications->isNotEmpty() ? $certifications->flatten()->sortBy('sort_order') : [];
@endphp

<section class="py-8 bg-cream border-y border-champagne overflow-hidden">
    <div class="max-w-8xl mx-auto px-4 md:px-8">

        <!-- Header Section: Compact & Small Fonts -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-2 mb-3">
                <span class="w-6 h-[1px] bg-primary/30"></span>
                <span class="text-primary font-semibold tracking-[0.2em] text-[9px] md:text-[11px] uppercase">Quality
                    Assurance</span>
                <span class="w-6 h-[1px] bg-primary/30"></span>
            </div>

            <h2 class="text-xl md:text-3xl font-heading font-bold text-brand">
                Our <span class="text-primary">Certifications</span>
            </h2>
        </div>

        <!-- Certifications Container -->
        <div class="relative group">
            <!-- Decorative Glows (Subtle) -->
            <div
                class="absolute -left-10 top-1/2 -translate-y-1/2 w-32 h-32 bg-primary/5 blur-[60px] rounded-full pointer-events-none">
            </div>
            <div
                class="absolute -right-10 top-1/2 -translate-y-1/2 w-32 h-32 bg-primary/5 blur-[60px] rounded-full pointer-events-none">
            </div>

            <!-- Edge Fades (Mobile Only) -->
            <div
                class="absolute left-0 top-0 bottom-0 w-12 bg-gradient-to-r from-cream to-transparent z-10 pointer-events-none md:hidden">
            </div>
            <div
                class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-cream to-transparent z-10 pointer-events-none md:hidden">
            </div>

            <div class="overflow-hidden py-2">
                {{-- Flex container: animation ONLY on mobile --}}
                <div
                    class="flex items-center gap-4 w-max md:w-full md:flex-wrap md:justify-center max-md:animate-[marquee-left_30s_linear_infinite]">

                    {{-- First Set --}}
                    @foreach ($certifications as $cert)
                        <div class="cert-card group/card !w-20 !h-20 md:!w-28 md:!h-28"
                            data-img="{{ $cert->image_url }}">
                            <div class="cert-canvas bg-white relative">
                                <div
                                    class="absolute inset-0 bg-primary/0 group-hover/card:bg-primary/[0.02] transition-colors duration-300">
                                </div>
                                <img src="{{ $cert->logo_url }}" alt="{{ $cert->name }}" loading="lazy"
                                    class="cert-img group-hover/card:scale-105 transition-transform duration-500 opacity-90 group-hover/card:opacity-100">
                            </div>
                        </div>
                    @endforeach

                    {{-- Duplicate Set for Seamless Marquee (Mobile only) --}}
                    @foreach ($certifications as $cert)
                        <div class="cert-card md:hidden !w-20 !h-20" data-img="{{ $cert->image_url }}">
                            <div class="cert-canvas bg-white">
                                <img src="{{ $cert->logo_url }}" alt="{{ $cert->name }}" loading="lazy"
                                    class="cert-img">
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

    </div>
</section>

{{-- Modal remains the same but cleaned up --}}
<div id="certModal"
    class="fixed inset-0 bg-black/80 backdrop-blur-md z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-all duration-500 ease-out">

    <div class="relative max-w-[90vw] max-h-[85vh] group">
        <button id="closeCertModal"
            class="absolute -top-12 right-0 text-white hover:text-primary transition-colors flex items-center gap-2 text-sm font-medium tracking-widest uppercase">
            <span>Close</span>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <img id="certModalImg" loading="lazy" alt="Certification Image"
            class="max-h-[85vh] max-w-full object-contain rounded-2xl shadow-[0_0_50px_rgba(0,0,0,0.5)] border border-white/10 scale-90 transition-transform duration-500">

        <div class="absolute inset-0 border border-primary/20 rounded-2xl pointer-events-none"></div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.cert-card')
            const modal = document.getElementById('certModal')
            const modalImg = document.getElementById('certModalImg')
            const closeBtn = document.getElementById('closeCertModal')

            const openModal = (imgSrc) => {
                modalImg.src = imgSrc
                modal.classList.remove('opacity-0', 'pointer-events-none')
                document.body.style.overflow = 'hidden' // Prevent scroll
                setTimeout(() => {
                    modalImg.classList.remove('scale-90')
                    modalImg.classList.add('scale-100')
                }, 10)
            }

            const closeModal = () => {
                modal.classList.add('opacity-0', 'pointer-events-none')
                modalImg.classList.remove('scale-100')
                modalImg.classList.add('scale-90')
                document.body.style.overflow = ''
            }

            cards.forEach(card => {
                card.addEventListener('click', () => {
                    openModal(card.dataset.img)
                })
            })

            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target.closest('#closeCertModal')) {
                    closeModal()
                }
            })

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closeModal()
            })
        })
    </script>
@endpush
