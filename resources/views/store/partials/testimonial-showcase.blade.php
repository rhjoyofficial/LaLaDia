<section class="py-16 px-4 md:px-8 overflow-hidden" style="background: var(--color-bg);">
    <div class="max-w-7xl mx-auto">

        <div class="flex items-end justify-between gap-6 mb-10">
            <div class="max-w-xl shrink-0">
                <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: var(--color-primary);">
                    Customer Stories
                </p>
                <h2 class="font-heading text-3xl md:text-4xl font-bold mb-3" style="color: var(--color-text);">
                    What Our Community Says
                </h2>
                <p class="text-sm leading-relaxed" style="color: var(--color-text-muted);">
                    Real stories from real customers who trust LaLaDia for pure, authentic products.
                </p>
            </div>

            <div class="flex gap-2 shrink-0">
                <button class="testi-prev w-10 h-10 rounded-xl flex items-center justify-center cursor-pointer transition-all duration-200"
                        style="border: 1px solid var(--color-border); color: var(--color-text-muted);"
                        onmouseover="this.style.background='var(--color-primary)'; this.style.color='white'; this.style.borderColor='var(--color-primary)'"
                        onmouseout="this.style.background='transparent'; this.style.color='var(--color-text-muted)'; this.style.borderColor='var(--color-border)'">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button class="testi-next w-10 h-10 rounded-xl flex items-center justify-center cursor-pointer transition-all duration-200"
                        style="border: 1px solid var(--color-border); color: var(--color-text-muted);"
                        onmouseover="this.style.background='var(--color-primary)'; this.style.color='white'; this.style.borderColor='var(--color-primary)'"
                        onmouseout="this.style.background='transparent'; this.style.color='var(--color-text-muted)'; this.style.borderColor='var(--color-border)'">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="swiper testimonial-swiper overflow-visible">
            <div class="swiper-wrapper items-stretch pb-4">

                {{-- Slide 1: Image review --}}
                <div class="swiper-slide flex h-auto">
                    <div class="flex flex-col w-full h-full rounded-2xl p-5 transition-all duration-300 hover:shadow-lg"
                         style="background: var(--color-surface); border: 1px solid var(--color-border);">
                        <div class="media-container mb-5">
                            <div class="media-frame image-preview-trigger cursor-zoom-in rounded-xl overflow-hidden"
                                 style="aspect-ratio: 4/3;"
                                 data-image="{{ asset('assets/review/review-3.jpeg') }}">
                                <img src="{{ asset('assets/review/review-3.jpeg') }}"
                                     loading="lazy"
                                     class="w-full h-full object-contain"
                                     alt="Customer review">
                            </div>
                        </div>
                        <div class="flex items-center gap-3 pt-4 mt-auto"
                             style="border-top: 1px solid var(--color-border);">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
                                 style="background: rgba(var(--color-primary-rgb),0.12); color: var(--color-primary);">R</div>
                            <div>
                                <p class="text-sm font-bold" style="color: var(--color-text);">Rina Begum</p>
                                <p class="text-xs" style="color: var(--color-text-muted);">Verified Buyer</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 2: Video review --}}
                <div class="swiper-slide flex h-auto">
                    <div class="flex flex-col w-full h-full rounded-2xl p-5 transition-all duration-300 hover:shadow-lg"
                         style="background: var(--color-surface); border: 1px solid var(--color-border);">
                        <div class="media-container mb-5">
                            <div class="media-frame"
                                 data-video
                                 data-video-type="html5"
                                 data-video-src="{{ asset('assets/video/video-file.mp4') }}"
                                 data-video-lazy="true"
                                 style="aspect-ratio:4/3; border-radius: 0.75rem; overflow: hidden;">
                            </div>
                        </div>
                        <div class="flex items-center gap-3 pt-4 mt-auto"
                             style="border-top: 1px solid var(--color-border);">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
                                 style="background: rgba(var(--color-primary-rgb),0.12); color: var(--color-primary);">M</div>
                            <div>
                                <p class="text-sm font-bold" style="color: var(--color-text);">Md. Karim</p>
                                <p class="text-xs" style="color: var(--color-text-muted);">Verified Buyer</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 3: Text review — Honey --}}
                <div class="swiper-slide flex h-auto">
                    <div class="flex flex-col w-full h-full rounded-2xl p-5 transition-all duration-300 hover:shadow-lg"
                         style="background: var(--color-surface); border: 1px solid var(--color-border);">
                        <div class="media-container mb-5">
                            <div class="flex gap-0.5 mb-4">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"
                                         style="color: var(--color-primary);">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-sm leading-relaxed italic font-bengali" style="color: var(--color-text-secondary);">
                                "সুন্দরবনের মধু সত্যিই অসাধারণ। এর রঙ, ঘ্রাণ, স্বাদ — সব কিছুই অন্য রকম। বাজারের মধুর সাথে তুলনাই হয় না। পরিবারের সবাই খুব পছন্দ করেছে।"
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-4 mt-auto"
                             style="border-top: 1px solid var(--color-border);">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
                                 style="background: rgba(var(--color-primary-rgb),0.12); color: var(--color-primary);">A</div>
                            <div>
                                <p class="text-sm font-bold" style="color: var(--color-text);">Ariful Islam</p>
                                <p class="text-xs" style="color: var(--color-text-muted);">Verified Buyer · Dhaka</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 4: Text review — Ghee --}}
                <div class="swiper-slide flex h-auto">
                    <div class="flex flex-col w-full h-full rounded-2xl p-5 transition-all duration-300 hover:shadow-lg"
                         style="background: var(--color-surface); border: 1px solid var(--color-border);">
                        <div class="media-container mb-5">
                            <div class="flex gap-0.5 mb-4">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"
                                         style="color: var(--color-primary);">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-sm leading-relaxed italic font-bengali" style="color: var(--color-text-secondary);">
                                "Royal Essence Ghee-র সুবাস রান্নাঘর ভরিয়ে দেয়। এত সুন্দর রঙ, এত ঘন — বহু বছর পর আসল গরুর ঘি পেলাম। পোলাওয়ে দিলে স্বাদ দ্বিগুণ হয়ে যায়!"
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-4 mt-auto"
                             style="border-top: 1px solid var(--color-border);">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
                                 style="background: rgba(var(--color-primary-rgb),0.12); color: var(--color-primary);">F</div>
                            <div>
                                <p class="text-sm font-bold" style="color: var(--color-text);">Fatima Khanam</p>
                                <p class="text-xs" style="color: var(--color-text-muted);">Verified Buyer · Chittagong</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 5: Text review — Pickles --}}
                <div class="swiper-slide flex h-auto">
                    <div class="flex flex-col w-full h-full rounded-2xl p-5 transition-all duration-300 hover:shadow-lg"
                         style="background: var(--color-surface); border: 1px solid var(--color-border);">
                        <div class="media-container mb-5">
                            <div class="flex gap-0.5 mb-4">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"
                                         style="color: var(--color-primary);">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-sm leading-relaxed italic font-bengali" style="color: var(--color-text-secondary);">
                                "ইলিশ মাছের আচার খেয়ে অবাক হয়ে গেলাম! এটা সত্যিই ঐতিহ্যবাহী রেসিপিতে তৈরি — মায়ের হাতের আচারের কথা মনে পড়ে গেল। বারবার অর্ডার করব।"
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-4 mt-auto"
                             style="border-top: 1px solid var(--color-border);">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
                                 style="background: rgba(var(--color-primary-rgb),0.12); color: var(--color-primary);">S</div>
                            <div>
                                <p class="text-sm font-bold" style="color: var(--color-text);">Sumaiya Ahmed</p>
                                <p class="text-xs" style="color: var(--color-text-muted);">Verified Buyer · Sylhet</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

{{-- IMAGE PREVIEW MODAL --}}
<div id="image-preview-modal"
     class="fixed inset-0 hidden items-center justify-center z-50 p-6"
     style="background: rgba(0,0,0,0.85); backdrop-filter: blur(6px);">
    <img id="preview-image"
         class="max-h-[90vh] max-w-[90vw] rounded-xl shadow-2xl"
         src="" alt="Review">
</div>

@push('scripts')
<script>
    new Swiper('.testimonial-swiper', {
        slidesPerView: 1.15,
        spaceBetween: 16,
        loop: false, // loop:true clones DOM nodes — cloned <video> breaks VideoManager init

        navigation: {
            nextEl: '.testi-next',
            prevEl: '.testi-prev',
        },

        breakpoints: {
            640:  { slidesPerView: 2.1,  spaceBetween: 20 },
            1024: { slidesPerView: 3.1,  spaceBetween: 20 },
            1280: { slidesPerView: 4,    spaceBetween: 20 },
        },
    });

    // Image preview modal
    const imageModal   = document.getElementById('image-preview-modal');
    const previewImage = document.getElementById('preview-image');

    document.querySelectorAll('.image-preview-trigger').forEach(el => {
        el.addEventListener('click', () => {
            previewImage.src = el.dataset.image;
            imageModal.classList.remove('hidden');
            imageModal.classList.add('flex');
        });
    });

    imageModal.addEventListener('click', () => {
        imageModal.classList.add('hidden');
        imageModal.classList.remove('flex');
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !imageModal.classList.contains('hidden')) {
            imageModal.click();
        }
    });
</script>
@endpush

