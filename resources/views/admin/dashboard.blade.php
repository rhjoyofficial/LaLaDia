@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        {{-- Revenue Today --}}
        <div class="bg-white rounded-xl border border-champagne p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-muted uppercase tracking-wider">Revenue Today</span>
                <span class="w-8 h-8 rounded-lg bg-cream flex items-center justify-center">
                    <i class="fa-solid fa-bangladeshi-taka-sign text-gold-antique text-sm"></i>
                </span>
            </div>
            <p class="text-2xl font-bold text-brand">৳{{ number_format($kpi['revenue_today'], 2) }}</p>
            <p class="text-xs text-taupe mt-1">This month: ৳{{ number_format($kpi['revenue_month'], 2) }}</p>
        </div>

        {{-- Orders Today --}}
        <div class="bg-white rounded-xl border border-champagne p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-muted uppercase tracking-wider">Orders Today</span>
                <span class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fa-solid fa-box text-blue-700 text-sm"></i>
                </span>
            </div>
            <p class="text-2xl font-bold text-brand">{{ $kpi['orders_today'] }}</p>
            <p class="text-xs text-taupe mt-1">This month: {{ $kpi['orders_month'] }}</p>
        </div>

        {{-- Total Customers --}}
        <div class="bg-white rounded-xl border border-champagne p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-muted uppercase tracking-wider">Customers</span>
                <span class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fa-solid fa-users text-purple-700 text-sm"></i>
                </span>
            </div>
            <p class="text-2xl font-bold text-brand">{{ number_format($kpi['customers_total']) }}</p>
            <p class="text-xs text-taupe mt-1">Registered accounts</p>
        </div>

        {{-- Active Products --}}
        <div class="bg-white rounded-xl border border-champagne p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-muted uppercase tracking-wider">Active Products</span>
                <span class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-leaf text-amber-700 text-sm"></i>
                </span>
            </div>
            <p class="text-2xl font-bold text-brand">{{ $kpi['products_active'] }}</p>
            <p class="text-xs text-taupe mt-1">Published in store</p>
        </div>
    </div>

    {{-- Late orders warning --}}
    @if ($lateOrders > 0)
        <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl p-4">
            <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
            <p class="text-sm text-red-700 font-medium">
                {{ $lateOrders }} {{ Str::plural('order', $lateOrders) }} pending for over 48 hours.
                <a href="{{ route('admin.orders') }}" class="underline hover:text-red-900">Review now</a>
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Revenue Chart (takes 2 columns on XL) --}}
        <div class="xl:col-span-2 bg-white rounded-xl border border-champagne p-5">
            <h3 class="text-sm font-bold text-brown mb-4">Revenue &mdash; Last 14 Days</h3>
            <div class="h-64" x-data="revenueChart()" x-init="init()">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        {{-- Order Status Breakdown --}}
        <div class="bg-white rounded-xl border border-champagne p-5">
            <h3 class="text-sm font-bold text-brown mb-4">Order Status</h3>
            @php
                $statusColors = [
                    'pending'    => 'bg-yellow-100 text-yellow-800',
                    'confirmed'  => 'bg-blue-100 text-blue-800',
                    'processing' => 'bg-indigo-100 text-indigo-800',
                    'shipped'    => 'bg-cyan-100 text-cyan-800',
                    'delivered'  => 'bg-cream text-gold-antique',
                    'cancelled'  => 'bg-red-100 text-red-800',
                    'returned'   => 'bg-gray-100 text-brown',
                ];
            @endphp
            <div class="space-y-2">
                @forelse ($ordersByStatus as $status => $count)
                    <div class="flex items-center justify-between py-2 px-3 rounded-lg {{ $statusColors[$status] ?? 'bg-gray-100 text-brown' }}">
                        <span class="text-sm font-medium capitalize">{{ $status }}</span>
                        <span class="text-sm font-bold">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-sm text-taupe text-center py-4">No orders yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Orders Table --}}
    <div class="mt-6 bg-white rounded-xl border border-champagne overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-champagne">
            <h3 class="text-sm font-bold text-brown">Recent Orders</h3>
            <a href="{{ route('admin.orders') }}" class="text-xs text-gold-antique font-medium hover:underline">
                View all &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase">Order</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase">Customer</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase">Total</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase">Payment</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($recentOrders as $order)
                        <tr class="hover:bg-cream transition">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="font-mono text-xs font-medium text-gold-antique hover:underline">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-brown">{{ $order->customer_name }}</td>
                            <td class="px-5 py-3 font-medium text-brand">৳{{ number_format($order->grand_total, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize
                                    {{ $statusColors[$order->order_status] ?? 'bg-gray-100 text-brown' }}">
                                    {{ $order->order_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-muted uppercase text-xs">{{ $order->payment_method }}</td>
                            <td class="px-5 py-3 text-taupe text-xs">{{ $order->placed_at?->format('M d, H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-taupe">No orders yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Chart.js for the revenue chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        function revenueChart() {
            return {
                init() {
                    const data = @json($dailyRevenue);
                    new Chart(this.$refs.canvas, {
                        type: 'bar',
                        data: {
                            labels: data.map(d => {
                                const dt = new Date(d.date);
                                return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                            }),
                            datasets: [{
                                label: 'Revenue (৳)',
                                data: data.map(d => d.revenue),
                                backgroundColor: 'rgba(var(--color-primary-rgb), 0.15)',
                                borderColor: 'rgb(var(--color-primary-rgb))',
                                borderWidth: 1.5,
                                borderRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => '৳' + ctx.parsed.y.toLocaleString()
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: v => '৳' + (v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v)
                                    },
                                    grid: { color: 'rgba(0,0,0,0.04)' }
                                },
                                x: {
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
@endpush










