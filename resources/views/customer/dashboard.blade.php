@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
    <section class="max-w-7xl mx-auto px-4 md:px-8 py-8" style="background: var(--color-bg); min-height: 60vh;">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            @include('customer.partials.nav')

            <div class="lg:col-span-3 space-y-6">

                {{-- Flash --}}
                @if (session('success'))
                    <div class="rounded-xl px-4 py-3 text-sm font-medium"
                         style="background: rgba(var(--color-primary-rgb),0.1); border: 1px solid rgba(var(--color-primary-rgb),0.3); color: var(--color-primary);">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Welcome card --}}
                <div class="card p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest mb-1"
                               style="color: var(--color-text-muted);">Welcome back</p>
                            <h1 class="text-2xl font-bold" style="color: var(--color-text);">{{ $user->name }}</h1>
                            <p class="text-sm mt-1" style="color: var(--color-text-muted);">
                                Manage your orders and profile from this dashboard.
                            </p>
                        </div>

                        <div class="shrink-0">
                            <p class="text-xs font-semibold uppercase tracking-widest mb-2"
                               style="color: var(--color-text-muted);">Referral Code</p>
                            @if ($user->referral_code)
                                <p class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-mono font-bold tracking-widest text-sm"
                                   style="background: rgba(var(--color-primary-rgb),0.1); border: 1px solid rgba(var(--color-primary-rgb),0.3); color: var(--color-primary);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    {{ $user->referral_code }}
                                </p>
                            @else
                                <p class="text-sm mb-2" style="color: var(--color-text-muted);">No referral code yet.</p>
                            @endif
                            <form action="{{ route('customer.referral.generate') }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn-primary text-sm px-4 py-2 cursor-pointer">
                                    Generate Referral Code
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="card p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                                 style="background: rgba(var(--color-primary-rgb),0.1);">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="color: var(--color-primary);">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold uppercase tracking-widest"
                               style="color: var(--color-text-muted);">Total Orders</p>
                        </div>
                        <p class="text-3xl font-bold" style="color: var(--color-text);">{{ $orderCount }}</p>
                    </div>

                    <div class="card p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                                 style="background: rgba(var(--color-primary-rgb),0.1);">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="color: var(--color-primary);">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold uppercase tracking-widest"
                               style="color: var(--color-text-muted);">Total Spent</p>
                        </div>
                        <p class="text-3xl font-bold font-bengali" style="color: var(--color-text);">
                            ৳{{ number_format($totalSpent, 2) }}
                        </p>
                    </div>
                </div>

                {{-- Recent Orders --}}
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base font-bold" style="color: var(--color-text);">Recent Orders</h2>
                        <a href="{{ route('customer.orders') }}"
                           class="text-xs font-semibold transition-colors duration-200"
                           style="color: var(--color-primary);"
                           onmouseover="this.style.color='var(--color-primary-hover)'"
                           onmouseout="this.style.color='var(--color-primary)'">
                            View all →
                        </a>
                    </div>

                    <div class="space-y-0">
                        @forelse($orders as $order)
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 py-3.5"
                                 style="border-bottom: 1px solid var(--color-border);">
                                <div>
                                    <p class="text-sm font-semibold" style="color: var(--color-text);">
                                        #{{ $order->order_number }}
                                    </p>
                                    <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">
                                        {{ $order->created_at?->format('d M, Y') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold font-bengali" style="color: var(--color-primary);">
                                        ৳{{ number_format($order->grand_total, 2) }}
                                    </span>
                                    <a href="{{ route('customer.order-details', $order) }}"
                                       class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200"
                                       style="background: rgba(var(--color-primary-rgb),0.1); color: var(--color-primary);"
                                       onmouseover="this.style.background='var(--color-primary)'; this.style.color='white'"
                                       onmouseout="this.style.background='rgba(var(--color-primary-rgb),0.1)'; this.style.color='var(--color-primary)'">
                                        Details
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="color: var(--color-border);">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm" style="color: var(--color-text-muted);">No orders yet.</p>
                                <a href="{{ route('shop') }}" class="btn-primary inline-block mt-3 px-5 py-2 text-sm">
                                    Start Shopping
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

