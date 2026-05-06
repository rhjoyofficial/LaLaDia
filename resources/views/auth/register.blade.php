@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="flex items-center justify-center min-h-[90vh] bg-cream px-4 py-12 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-sm border border-champagne">

            {{-- Logo & Header --}}
            <div class="text-center">
                <a href="{{ url('/') }}" class="inline-block">
                    <img class="mx-auto h-16 w-auto" src="{{ asset('assets/images/laladia-logo.png') }}" alt="Laladia Logo">
                </a>
                <h2 class="mt-6 text-3xl font-bold tracking-tight text-brand font-['Plus_Jakarta_Sans']">
                    Create Account
                </h2>
                <p class="mt-2 text-sm text-muted">Join Laladia Garden for a better shopping experience</p>
            </div>

            {{-- Error Container --}}
            <div id="error-box"
                class="hidden p-3 text-sm text-red-600 bg-red-50 rounded-lg border border-red-100 font-['Noto_Sans_Bengali']">
                <ul id="error-list" class="list-disc pl-5 space-y-0.5"></ul>
            </div>

            {{-- Form --}}
            <form id="registerForm" class="mt-8 space-y-5" novalidate>
                @csrf

                {{-- Full Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-brown">Full Name</label>
                    <input id="name" name="name" type="text" autocomplete="name" required
                        placeholder="আপনার পূর্ণ নাম লিখুন"
                        class="mt-1 appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gold-antique focus:border-primary sm:text-sm transition-colors font-['Noto_Sans_Bengali'] placeholder:font-['Noto_Sans_Bengali']">
                </div>

                {{-- Email (optional) --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-brown">
                        Email Address
                        <span class="text-taupe font-normal text-xs ml-1">(optional)</span>
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email"
                        placeholder="আপনার ইমেইল লিখুন (ঐচ্ছিক)"
                        class="mt-1 appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gold-antique focus:border-primary sm:text-sm transition-colors font-['Noto_Sans_Bengali'] placeholder:font-['Noto_Sans_Bengali']">
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-brown">Phone Number</label>
                    <input id="phone" name="phone" type="tel" autocomplete="tel" required
                        placeholder="আপনার ফোন নম্বর লিখুন"
                        class="mt-1 appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gold-antique focus:border-primary sm:text-sm transition-colors font-['Noto_Sans_Bengali'] placeholder:font-['Noto_Sans_Bengali']">
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-brown">Password</label>
                    <div class="mt-1 relative">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            placeholder="পাসওয়ার্ড দিন (কমপক্ষে ৬ অক্ষর)"
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gold-antique focus:border-primary sm:text-sm transition-colors font-['Noto_Sans_Bengali'] placeholder:font-['Noto_Sans_Bengali']">
                        <button type="button" data-password-toggle="password"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-taupe hover:text-primary transition-colors">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-brown">
                        Confirm Password
                    </label>
                    <div class="mt-1 relative">
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            autocomplete="new-password" required placeholder="পাসওয়ার্ডটি পুনরায় দিন"
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gold-antique focus:border-primary sm:text-sm transition-colors font-['Noto_Sans_Bengali'] placeholder:font-['Noto_Sans_Bengali']">
                        <button type="button" data-password-toggle="password_confirmation"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-taupe hover:text-primary transition-colors">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <button type="submit" id="submitBtn"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-primary cursor-pointer hover:bg-gold-antique focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-antique transition-all transform active:scale-95 disabled:opacity-70">
                        Create Account
                    </button>
                </div>
            </form>

            {{-- Login Link --}}
            <div class="mt-6 text-center text-sm">
                <p class="text-muted">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-bold text-primary hover:text-gold-antique transition-colors">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
