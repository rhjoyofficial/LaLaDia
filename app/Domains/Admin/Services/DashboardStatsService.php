<?php

namespace App\Domains\Admin\Services;

use App\Domains\Order\Models\Order;
use App\Domains\Product\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    // KPI cards: revenue/order counts change frequently — 5-minute TTL is a
    // good balance between freshness and avoiding 6 expensive queries per load.
    private const KPI_TTL          = 300;  // 5 minutes
    private const STATUS_TTL       = 300;
    private const REVENUE_TTL      = 600;  // 10 minutes — chart data is less time-sensitive
    private const LATE_ORDERS_TTL  = 300;
    private const RECENT_ORDERS_TTL = 60; // 1 minute — admins expect near-real-time order feed

    public function kpiCards(): array
    {
        return Cache::remember('admin:kpi_cards', self::KPI_TTL, function () {
            $today        = Carbon::today();
            $startOfMonth = Carbon::now()->startOfMonth();

            return [
                'revenue_today'   => Order::whereDate('placed_at', $today)
                    ->whereNotIn('order_status', ['cancelled', 'returned'])
                    ->sum('grand_total'),
                'revenue_month'   => Order::where('placed_at', '>=', $startOfMonth)
                    ->whereNotIn('order_status', ['cancelled', 'returned'])
                    ->sum('grand_total'),
                'orders_today'    => Order::whereDate('placed_at', $today)->count(),
                'orders_month'    => Order::where('placed_at', '>=', $startOfMonth)->count(),
                'customers_total' => User::role('Customer')->count(),
                'products_active' => Product::where('is_active', true)->count(),
            ];
        });
    }

    public function ordersByStatus(): array
    {
        return Cache::remember('admin:orders_by_status', self::STATUS_TTL, fn() =>
            Order::select('order_status', DB::raw('COUNT(*) as count'))
                ->groupBy('order_status')
                ->pluck('count', 'order_status')
                ->toArray()
        );
    }

    public function recentOrders(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("admin:recent_orders:{$limit}", self::RECENT_ORDERS_TTL, fn() =>
            Order::with('user:id,name,phone')
                ->latest('placed_at')
                ->limit($limit)
                ->get(['id', 'order_number', 'user_id', 'customer_name', 'grand_total', 'order_status', 'payment_method', 'placed_at'])
        );
    }

    public function dailyRevenue(int $days = 14): array
    {
        return Cache::remember("admin:daily_revenue:{$days}", self::REVENUE_TTL, function () use ($days) {
            $from = Carbon::today()->subDays($days - 1);

            $rows = Order::where('placed_at', '>=', $from)
                ->select(
                    DB::raw('DATE(placed_at) as date'),
                    DB::raw('SUM(grand_total) as revenue'),
                    DB::raw('COUNT(*) as orders'),
                )
                ->groupBy(DB::raw('DATE(placed_at)'))
                ->orderBy('date')
                ->get();

            $filled = [];
            for ($i = 0; $i < $days; $i++) {
                $date     = $from->copy()->addDays($i)->toDateString();
                $row      = $rows->firstWhere('date', $date);
                $filled[] = [
                    'date'    => $date,
                    'revenue' => $row ? (float) $row->revenue : 0,
                    'orders'  => $row ? (int) $row->orders : 0,
                ];
            }

            return $filled;
        });
    }

    public function lateOrders(): int
    {
        return Cache::remember('admin:late_orders', self::LATE_ORDERS_TTL, fn() =>
            Order::where('order_status', 'pending')
                ->where('placed_at', '<', Carbon::now()->subHours(48))
                ->count()
        );
    }

    /**
     * Flush all dashboard caches — call this when an order status changes
     * or a new order is placed so KPI cards reflect reality promptly.
     */
    public static function flush(): void
    {
        Cache::forget('admin:kpi_cards');
        Cache::forget('admin:orders_by_status');
        Cache::forget('admin:late_orders');
        Cache::forget('admin:recent_orders:10');
        Cache::forget('admin:daily_revenue:14');
    }
}

