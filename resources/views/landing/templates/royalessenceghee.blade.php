@extends('layouts.app')

@section('title', $landing->meta_title ?? $product->name)
@section('meta_description', $landing->meta_description ?? $product->short_description)

@section('content')
    <section class="relative bg-secondary overflow-hidden border-b border-primary/10">
        <div class="absolute inset-0 z-0">
            <svg class="absolute -top-10 -right-10 text-primary/5" width="400" height="400" viewBox="0 0 200 200"
                fill="currentColor">
                <path
                    d="M100 0C130 0 160 20 180 50C200 80 210 120 190 150C170 180 120 200 90 190C60 180 20 150 10 120C0 90 30 50 60 20C70 10 80 0 100 0Z">
                </path>
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-16 md:py-24 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 items-center">
                <div class="md:col-span-7 text-center md:text-left">
                    <span
                        class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-5 border border-primary/20">
                        <i class="fas fa-certificate text-accent"></i>
                        প্রিমিয়াম কোয়ালিটি | খাঁটি দেশি ঘি
                    </span>

                    <h1 class="text-4xl md:text-6xl font-bold text-gray-950 leading-tight mb-6 font-hind">
                        রয়্যাল এসেন্স <span class="text-primary">ঘি</span> — আভিজাত্য ও
                        স্বাস্থ্যের এক অনন্য মিশেল!
                    </h1>

                    <p class="text-lg md:text-xl text-gray-700 mb-8 leading-relaxed font-hind">
                        দেশি মাঠের ঘাস খাওয়া গরুর দুধ থেকে তৈরি। অতুলনীয় ঘ্রাণ, ঘনত্ব ও
                        খাঁটি স্বাদ—যা আপনার প্রতিদিনের খাবারকে করবে আরও সুস্বাদু ও
                        পুষ্টিকর।
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="#checkout"
                            class="px-8 py-4 bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-amber-900 transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            এখনই অর্ডার করুন
                        </a>
                        <a href="#about"
                            class="px-8 py-4 bg-white text-primary font-bold rounded-xl border-2 border-primary hover:bg-primary/5 transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            বিস্তারিত জানুন
                        </a>
                    </div>
                </div>

                <div class="md:col-span-5 relative flex justify-center items-center">
                    <div
                        class="absolute w-72 h-72 md:w-96 md:h-96 bg-white rounded-full shadow-inner border border-primary/10">
                    </div>

                    <img src="{{ asset('assets/landing/ghee-jar.jpg') }}" alt="Royal Essence Ghee"
                        class="relative z-10 w-64 h-auto md:w-80 aspect-square object-cover rounded-full transform hover:scale-105 transition-transform duration-500 hover:rotate-2" />
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 font-hind">
                    রয়্যাল এসেন্স ঘি: বিশুদ্ধতার অনন্য নিদর্শন
                </h2>
                <div class="w-24 h-1.5 bg-primary mx-auto rounded-full mb-6"></div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto font-hind">
                    প্রতিটি ফোঁটায় মিশে আছে প্রকৃতির ছোঁয়া এবং সর্বোচ্চ গুণমান নিশ্চিত
                    করার নিশ্চয়তা।
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="flex justify-center">
                    <img src="{{ asset('assets/landing/ghee-texture.jpg') }}" alt="ঘি এর গঠন"
                        class="rounded-3xl shadow-lg w-full aspect-7/5 object-cover" />
                </div>

                <div class="space-y-6">
                    <div class="flex gap-4">
                        <i class="fas fa-check-circle text-primary text-2xl mt-1"></i>
                        <div>
                            <h4 class="text-lg font-bold text-gray-950 font-hind">
                                ১০০% খাঁটি ও প্রাকৃতিক
                            </h4>
                            <p class="text-gray-600 font-hind">
                                দেশি মাঠে বিচরণ করা গরুর দুধের ক্রিম থেকে সরাসরি প্রস্তুত।
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <i class="fas fa-check-circle text-primary text-2xl mt-1"></i>
                        <div>
                            <h4 class="text-lg font-bold text-gray-950 font-hind">
                                প্রিমিয়াম মান নিশ্চিত
                            </h4>
                            <p class="text-gray-600 font-hind">
                                উৎপাদন সীমিত (প্রতিবারে সর্বোচ্চ ১০ কেজি), যাতে মানের কোনো আপস
                                না হয়।
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <i class="fas fa-check-circle text-primary text-2xl mt-1"></i>
                        <div>
                            <h4 class="text-lg font-bold text-gray-950 font-hind">
                                কোনো কৃত্রিম উপাদান নেই
                            </h4>
                            <p class="text-gray-600 font-hind">
                                MSG মুক্ত, প্রিজারভেটিভ বিহীন এবং পুরোপুরি প্রাকৃতিক স্বাদ ও
                                ঘ্রাণ।
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <i class="fas fa-weight-hanging text-primary text-2xl mt-1"></i>
                        <div>
                            <h4 class="text-lg font-bold text-gray-950 font-hind">
                                নিট ওজন
                            </h4>
                            <p class="text-gray-600 font-hind">৩৫০ গ্রাম (প্রায়)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="benefits" class="py-16 md:py-24 bg-secondary/50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 font-hind">
                    স্বাস্থ্য উপকারিতা
                </h2>
                <div class="w-24 h-1.5 bg-primary mx-auto rounded-full mb-6"></div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto font-hind">
                    প্রতিদিন রয়্যাল এসেন্স ঘি গ্রহণ করলে আপনার শরীরের সামগ্রিক পুষ্টি
                    নিশ্চিত হয় এবং নানাবিধ রোগ থেকে সুরক্ষা পাওয়া যায়।
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        হজম ও রোগ প্রতিরোধ
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        পাচনতন্ত্র সুস্থ রাখে এবং শরীরের বিষাক্ত পদার্থ বের করে লিভার
                        পরিষ্কার রাখে।
                    </p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-bone"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        হাড়ের শক্তি
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        ভিটামিন K2 ক্যালসিয়াম শোষণে সহায়তা করে, যা হাড়কে শক্তিশালী করে।
                    </p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        মানসিক কার্যক্ষমতা
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        মস্তিষ্কের কার্যক্ষমতা বৃদ্ধি করে এবং মানসিক চাপ কমাতে সাহায্য
                        করে।
                    </p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        ত্বক ও চুলের যত্ন
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        ত্বককে মসৃণ ও উজ্জ্বল করে এবং চুল পড়া কমিয়ে পুষ্টি জোগায়।
                    </p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-burn"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        ওজন নিয়ন্ত্রণ
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        শরীরের কোলেস্টেরল বার্ন করতে সাহায্য করে এবং অতিরিক্ত চর্বি জমা
                        রোধ করে।
                    </p>
                </div>

                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center text-3xl mb-6">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-950 mb-3 font-hind">
                        সামগ্রিক পুষ্টি
                    </h3>
                    <p class="text-gray-600 leading-relaxed font-hind">
                        ভিটামিন A, D, E ও K-এর ভালো উৎস যা সামগ্রিক পুষ্টি নিশ্চিত করে।
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="usage" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 font-hind">
                    ব্যবহারের নিয়মাবলী
                </h2>
                <div class="w-24 h-1.5 bg-primary mx-auto rounded-full mb-6"></div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto font-hind">
                    রয়্যাল এসেন্স ঘি আপনার প্রতিদিনের খাদ্যতালিকায় অন্তর্ভুক্ত করার কিছু
                    সহজ উপায়।
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="flex gap-4 bg-secondary/30 p-6 rounded-2xl border border-primary/10">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center text-xl">
                        <i class="fas fa-sun"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-950 mb-1 font-hind">
                            সকাল শুরু করুন সতেজতায়
                        </h4>
                        <p class="text-gray-700 font-hind">
                            প্রতিদিন সকালে এক গ্লাস গরম পানির সাথে এক চামচ ঘি মিশিয়ে খেলে
                            চুল পড়া সমস্যা ধীরে ধীরে কমে যায় এবং চর্বি জমা রোধ করে।
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 bg-secondary/30 p-6 rounded-2xl border border-primary/10">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center text-xl">
                        <i class="fas fa-mug-hot"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-950 mb-1 font-hind">
                            সুস্বাস্থ্য ও শক্তি
                        </h4>
                        <p class="text-gray-700 font-hind">
                            এক গ্লাস গরম দুধের সাথে এক চামচ ঘি মিশিয়ে খেলে শরীরের শক্তি
                            বাড়ে এবং হজম ক্ষমতা উন্নত হয়।
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 bg-secondary/30 p-6 rounded-2xl border border-primary/10">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center text-xl">
                        <i class="fas fa-cookie-bite"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-950 mb-1 font-hind">
                            খাবারের স্বাদ বাড়াতে
                        </h4>
                        <p class="text-gray-700 font-hind">
                            তরকারি রান্নায় অথবা রুটি বা পরোটার সাথে ঘি মিশিয়ে খেলে খাবারের
                            স্বাদ অতুলনীয় হয়ে ওঠে।
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 bg-secondary/30 p-6 rounded-2xl border border-primary/10">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center text-xl">
                        <i class="fas fa-wind"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-950 mb-1 font-hind">
                            শীতকালীন সুরক্ষা
                        </h4>
                        <p class="text-gray-700 font-hind">
                            শীতকালে ঘি এর সাথে সামান্য গোল মরিচ মিশিয়ে খেলে ঠাণ্ডাজনিত
                            সমস্যা দূর হয়।
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="why-us" class="py-16 md:py-24 bg-primary text-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="flex justify-center">
                    <img src="{{ asset('assets/landing/farm-cows.jpg') }}" alt="ঘি তৈরির প্রক্রিয়া"
                        class="rounded-3xl shadow-2xl w-full aspect-7/5 object-cover border-4 border-white/10" />
                </div>

                <div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 font-hind">
                        কেন বেছে নেবেন রয়্যাল এসেন্স ঘি?
                    </h2>
                    <div class="w-24 h-1.5 bg-accent rounded-full mb-8"></div>

                    <p class="text-lg leading-relaxed mb-6 font-hind text-white/90">
                        আমাদের এই ঘি তৈরি করা হয় দেশি মাঠে বিচরণ করা, কাঁচা ঘাস খাওয়া,
                        রোদে পোড়া গরুর দুধ থেকে এবং সেই দুধ ক্রিম তৈরি করে ঘি বানানো হয়।
                    </p>

                    <div class="bg-white/10 p-6 rounded-2xl border border-white/20 backdrop-blur-sm">
                        <p class="text-lg font-semibold mb-3 font-hind text-accent">
                            <i class="fas fa-check-circle mr-2"></i>সীমাবদ্ধ উৎপাদন,
                            সর্বোচ্চ গুণমান
                        </p>
                        <p class="text-white/80 font-hind">
                            আমরা একসাথে দশ কেজির বেশি ঘি তৈরি করতে পারি না। প্রোডাকশন সীমিত
                            রাখি যেন প্রতিটি জারে ঘি এর মান ও বিশুদ্ধতা অটুট থাকে।
                            বাংলাদেশের সবচেয়ে প্রিমিয়াম কোয়ালিটি আমরাই দিচ্ছি।
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="certifications" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 font-hind">
                    আন্তর্জাতিক মানের নিশ্চয়তা
                </h2>
                <div class="w-24 h-1.5 bg-primary mx-auto rounded-full mb-6"></div>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto font-hind">
                    রয়্যাল এসেন্স ঘি কঠোর মাননিয়ন্ত্রণ প্রক্রিয়া পার হয়ে আপনার কাছে
                    পৌঁছায়। আমাদের বিশ্বাসযোগ্যতার সনদসমূহ:
                </p>
            </div>

            <div class="flex flex-wrap justify-center items-center gap-10 md:gap-16 lg:gap-20">
                <!-- ISO -->
                <div
                    class="group flex flex-col items-center text-center transition duration-300 hover:scale-105 hover:-translate-y-1">
                    <img src="img/cert-iso-22000.png" alt="ISO 22000 Certified"
                        class="h-20 md:h-24 object-contain transition-transform duration-300 group-hover:scale-110"
                        loading="lazy" />
                    <span class="mt-3 text-sm font-medium text-gray-800 font-inter">ISO 22000</span>
                </div>

                <!-- Halal -->
                <div
                    class="group flex flex-col items-center text-center transition duration-300 hover:scale-105 hover:-translate-y-1">
                    <img src="img/cert-halal.png" alt="Halal Certified"
                        class="h-20 md:h-24 object-contain transition-transform duration-300 group-hover:scale-110"
                        loading="lazy" />
                    <span class="mt-3 text-sm font-medium text-gray-800 font-inter">Halal Certified</span>
                </div>

                <!-- HACCP -->
                <div
                    class="group flex flex-col items-center text-center transition duration-300 hover:scale-105 hover:-translate-y-1">
                    <img src="img/cert-haccp.png" alt="HACCP Certified"
                        class="h-20 md:h-24 object-contain transition-transform duration-300 group-hover:scale-110"
                        loading="lazy" />
                    <span class="mt-3 text-sm font-medium text-gray-800 font-inter">HACCP</span>
                </div>

                <!-- GMP Quality -->
                <div
                    class="group flex flex-col items-center text-center transition duration-300 hover:scale-105 hover:-translate-y-1">
                    <img src="img/gmp-quality.png" alt="GMP Quality"
                        class="h-20 md:h-24 object-contain transition-transform duration-300 group-hover:scale-110"
                        loading="lazy" />
                    <span class="mt-3 text-sm font-medium text-gray-800 font-inter">GMP Quality</span>
                </div>

                <!-- BSTI (added since it's in your product list) -->
                <div
                    class="group flex flex-col items-center text-center transition duration-300 hover:scale-105 hover:-translate-y-1">
                    <img src="img/cert-bsti.png" alt="BSTI Approved"
                        class="h-20 md:h-24 object-contain transition-transform duration-300 group-hover:scale-110"
                        loading="lazy" />
                    <span class="mt-3 text-sm font-medium text-gray-800 font-inter">BSTI Approved</span>
                </div>
            </div>
            <div class="flex justify-center mt-4">
                <img src="img/check-out-security.webp" alt="Security Checkout"
                    className="w-full h-auto object-contain " />
            </div>
        </div>
    </section>
@endsection
