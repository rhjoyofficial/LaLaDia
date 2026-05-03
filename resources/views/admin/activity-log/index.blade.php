@extends('layouts.admin')

@section('title', 'Activity Log')

@section('content')
    <div class="bg-white border border-champagne rounded-xl">
        <div class="p-4 border-b border-champagne flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
            <h2 class="text-sm font-semibold text-brown">System Activity</h2>

            <form method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search description/log"
                    class="w-52 rounded-lg border border-champagne px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique">
                <select name="log"
                    class="rounded-lg border border-champagne px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-gold-antique cursor-pointer">
                    <option value="">All Logs</option>
                    <option value="admin-auth" @selected(request('log') === 'admin-auth')>Admin Auth</option>
                    <option value="customers" @selected(request('log') === 'customers')>Customers</option>
                    <option value="products" @selected(request('log') === 'products')>Products</option>
                    <option value="coupons" @selected(request('log') === 'coupons')>Coupons</option>
                    <option value="settings" @selected(request('log') === 'settings')>Settings</option>
                    <option value="order" @selected(request('log') === 'order')>Orders</option>
                    <option value="courier" @selected(request('log') === 'courier')>Courier</option>
                </select>
                <button type="submit"
                    class="rounded-lg bg-gold-antique text-white px-3 py-2 text-sm font-medium hover:bg-gold-antique cursor-pointer transition">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-cream">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">When</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">Log</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase">Properties</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($activities as $activity)
                        <tr>
                            <td class="px-4 py-3 text-muted whitespace-nowrap">
                                {{ $activity->created_at?->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-brown">
                                    {{ $activity->log_name ?: 'default' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-brown">{{ $activity->description }}</td>
                            <td class="px-4 py-3 text-muted whitespace-nowrap">
                                {{ $activity->causer?->name ?? 'System' }}
                            </td>
                            <td class="px-4 py-3 text-muted min-w-0 break-all align-top">
                                @if (!empty($activity->properties))
                                    <div class="max-h-32 overflow-y-auto bg-white border border-champagne rounded-md p-2">
                                        <ul class="space-y-1 text-xs">
                                            @foreach ($activity->properties as $key => $value)
                                                <li class="flex flex-col sm:flex-row gap-1">
                                                    <span class="font-medium text-brown">{{ $key }}:</span>
                                                    <span class="text-muted font-mono bg-cream px-1 rounded break-all">
                                                        @if (is_array($value) || is_object($value))
                                                            {{ json_encode($value) }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <span class="text-taupe italic">No properties</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-taupe">No activity found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-champagne">
            {{ $activities->links() }}
        </div>
    </div>
@endsection










