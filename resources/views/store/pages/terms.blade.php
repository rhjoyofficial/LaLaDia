@extends('layouts.app')

@section('title', $data['title'])

@section('content')
    <!-- Elegant, Light-Themed Cinematic Design -->
    <div
        class="bg-ivory min-h-screen text-muted font-light overflow-hidden selection:bg-primary/30 shadow-2xl bg-white border-x border-champagne">

        <!-- Cinematic Hero -->
        <section
            class="relative h-100 flex items-center justify-center border-b border-champagne bg-primary overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=2000"
                    class="w-full h-full object-cover opacity-20 scale-105" alt="Terms Background">
                <div class="absolute inset-0 bg-linear-to-b from-transparent via-primary/80 to-primary"></div>
            </div>

            <div class="relative z-10 text-center px-4 max-w-3xl mx-auto mt-4">
                <p class="text-yellow-400 uppercase tracking-[0.4em] text-[10px] font-medium mb-3">Legal</p>
                <h1
                    class="text-4xl md:text-5xl lg:text-6xl font-playfair text-white mb-4 leading-tight tracking-tight drop-shadow-2xl">
                    {{ $data['title'] }}
                </h1>
                <p class="text-sm md:text-base text-ivory max-w-xl mx-auto leading-relaxed font-light">
                    {{ $data['last_updated'] }}
                </p>
            </div>
        </section>

        <!-- Content -->
        <div class="px-4 py-24 max-w-4xl mx-auto">
            <div class="bg-white shadow-xl p-8 md:p-16 border border-champagne">
                <div class="space-y-16">
                    @foreach ($data['sections'] as $section)
                        <section class="group">
                            <h2 class="text-3xl font-playfair text-brand mb-6 flex items-center gap-4">
                                <span
                                    class="w-1.5 h-8 bg-primary rounded-sm group-hover:h-10 transition-all duration-300"></span>
                                {{ $section['heading'] }}
                            </h2>
                            <div class="text-muted leading-loose text-lg font-light space-y-6">
                                {{ $section['content'] }}
                            </div>
                        </section>
                    @endforeach
                </div>

                <div class="mt-20 pt-12 border-t border-champagne text-center">
                    <p class="text-lg text-muted mb-6 font-light">If you have any questions about this
                        {{ strtolower($data['title']) }}, please contact us:</p>
                    <a href="mailto:legal@bionic.garden"
                        class="text-primary font-bold text-xl hover:text-brand transition-colors duration-300">legal@bionic.garden</a>
                </div>
            </div>
        </div>
    </div>
@endsection










