@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
    <section class="max-w-7xl mx-auto px-4 md:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            @include('customer.partials.nav')

            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white border border-champagne rounded-2xl p-6 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <div>
                            <h1 class="text-2xl font-bold text-brand">Order #{{ $order->order_number }}</h1>
                            <p class="text-sm text-muted mt-1">Placed on {{ $order->created_at?->format('d M, Y h:i A') }}
                            </p>
                        </div>
                        <span class="text-sm px-3 py-1 rounded-lg bg-gray-100 text-brown capitalize">
                            {{ str_replace('_', ' ', $order->order_status ?? 'pending') }}
                        </span>
                    </div>
                </div>

                <div class="bg-white border border-champagne rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-brand mb-4">Items</h2>
                    <div class="space-y-3">
                        @foreach ($order->items as $item)
                            <div
                                class="flex items-center justify-between border-b border-champagne pb-3 last:border-0 last:pb-0">
                                <div>
                                    <p class="font-medium text-brand">{{ $item->product_name_snapshot }}</p>
                                    <p class="text-xs text-muted">Qty: {{ $item->quantity }}</p>
                                </div>
                                <p class="font-semibold text-brand font-bengali">
                                    ৳{{ number_format($item->total_price, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white border border-champagne rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-brand mb-4">Order Summary</h2>
                    <div class="space-y-2 text-sm font-bengali">
                        <div class="flex items-center justify-between text-muted">
                            <span>Subtotal</span>
                            <span>৳{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-muted">
                            <span>Shipping</span>
                            <span>৳{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-muted">
                            <span>Discount</span>
                            <span>-৳{{ number_format($order->discount_total, 2) }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between text-brand font-bold pt-2 border-t border-champagne">
                            <span>Total</span>
                            <span>৳{{ number_format($order->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection










