@extends('layouts.app')

@section('title', 'My Orders')

@push('styles')
    <style>
        .order-status {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: capitalize;
            letter-spacing: 0.02em;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.12);
            color: #B45309;
        }

        .status-processing {
            background: rgba(59, 130, 246, 0.12);
            color: #1D4ED8;
        }

        .status-confirmed {
            background: rgba(99, 102, 241, 0.12);
            color: #4338CA;
        }

        .status-shipped {
            background: rgba(99, 102, 241, 0.12);
            color: #4338CA;
        }

        .status-delivered {
            background: rgba(34, 197, 94, 0.12);
            color: #15803D;
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.12);
            color: var(--color-danger);
        }
    </style>
@endpush

@section('content')
    <section class="max-w-8xl mx-auto px-4 md:px-8 py-8" style="background: var(--color-bg); min-height: 60vh;">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            @include('customer.partials.nav')

            <div class="lg:col-span-3">
                <div class="card p-6">
                    <h1 class="text-xl font-bold mb-6" style="color: var(--color-text);">My Orders</h1>

                    @forelse($orders as $order)
                        @php
                            $status = $order->order_status ?? 'pending';
                            $statusClass = 'status-' . str_replace('_', '-', $status);
                        @endphp
                        <div class="rounded-xl p-4 mb-3 last:mb-0 transition-all duration-200"
                            style="border: 1px solid var(--color-border); background: var(--color-surface);"
                            onmouseover="this.style.borderColor='rgba(var(--color-primary-rgb),0.3)'"
                            onmouseout="this.style.borderColor='var(--color-border)'">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-sm" style="color: var(--color-text);">
                                        Order #{{ $order->order_number }}
                                    </p>
                                    <p class="text-xs mt-0.5" style="color: var(--color-text-muted);">
                                        {{ $order->created_at?->format('d M, Y h:i A') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3 flex-wrap">
                                    <span class="order-status {{ $statusClass }}">
                                        {{ str_replace('_', ' ', $status) }}
                                    </span>
                                    <span class="font-bold font-bengali text-sm" style="color: var(--color-text);">
                                        ৳{{ number_format($order->grand_total, 2) }}
                                    </span>
                                    <a href="{{ route('customer.order-details', $order) }}"
                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200"
                                        style="background: rgba(var(--color-primary-rgb),0.1); color: var(--color-primary);"
                                        onmouseover="this.style.background='var(--color-primary)'; this.style.color='white'"
                                        onmouseout="this.style.background='rgba(var(--color-primary-rgb),0.1)'; this.style.color='var(--color-primary)'">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16">
                            <svg class="w-14 h-14 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color: var(--color-border);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-base font-semibold mb-1" style="color: var(--color-text);">No orders found</p>
                            <p class="text-sm mb-5" style="color: var(--color-text-muted);">
                                You haven't placed any orders yet.
                            </p>
                            <a href="{{ route('shop') }}" class="btn-primary px-6 py-2.5 text-sm">
                                Browse Products
                            </a>
                        </div>
                    @endforelse

                    @if ($orders->hasPages())
                        <div class="mt-6 pt-4" style="border-top: 1px solid var(--color-border);">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
