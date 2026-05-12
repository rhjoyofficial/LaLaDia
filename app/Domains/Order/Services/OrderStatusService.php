<?php

namespace App\Domains\Order\Services;

use App\Domains\Admin\Services\DashboardStatsService;
use App\Domains\Order\Models\Order;
use App\Domains\Order\Enums\OrderStatus;
use App\Domains\Product\Models\ProductVariant;
use App\Events\OrderStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderStatusService
{
    public function changeStatus(Order $order, OrderStatus $newStatus): Order
    {
        try {
            return DB::transaction(function () use ($order, $newStatus) {
                // Lock the order for update to prevent race conditions during status changes
                $order = Order::lockForUpdate()->findOrFail($order->id);

                // Read status AFTER acquiring lock so transition check uses current data
                $oldStatusStr = $order->order_status;

                if (!$this->isValidTransition($oldStatusStr, $newStatus->value)) {
                    throw new Exception("Invalid status transition from {$oldStatusStr} to {$newStatus->value}");
                }

                // 1. Inventory Logic: Handle Transitions
                // Eager-load all required relations in one query before the loops
                // in fulfillStock/releaseStock to avoid N+1 per combo item.
                if ($newStatus === OrderStatus::Shipped || $newStatus === OrderStatus::Cancelled) {
                    $order->load(['items.combo.items']);
                }

                // Only fulfill stock if we are moving TO Shipped from a non-shipped state
                if ($newStatus === OrderStatus::Shipped && $oldStatusStr !== 'shipped') {
                    $this->fulfillStock($order);
                }

                // Handle Cancellation
                if ($newStatus === OrderStatus::Cancelled) {
                    $this->releaseStock($order);
                }

                // 2. Update Status & Timestamps
                $order->order_status = $newStatus->value;

                match ($newStatus) {
                    OrderStatus::Confirmed  => $order->confirmed_at = now(),
                    OrderStatus::Processing => $order->processing_at = now(),
                    OrderStatus::Shipped    => $order->shipped_at = now(),
                    OrderStatus::Delivered  => $order->delivered_at = now(),
                    OrderStatus::Cancelled  => $order->cancelled_at = now(),
                    OrderStatus::Returned   => $order->returned_at = now(),
                    default => null
                };

                $order->save();

                event(new OrderStatusChanged($order, $oldStatusStr, $newStatus->value));

                DashboardStatsService::flush();

                return $order;
            });
        } catch (Exception $e) {
            Log::error('Order Status Update Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Finalize the inventory: Move items out of 'reserved' and out of 'physical stock'.
     * Uses min() to prevent stock/reserved_stock from going negative under any race condition.
     * Batch-loads all variant rows in a single locked query to avoid N+1.
     */
    private function fulfillStock(Order $order): void
    {
        $variants = $this->loadLockedVariants($order);

        foreach ($order->items as $item) {
            if ($item->combo_id && $item->combo) {
                foreach ($item->combo->items as $comboItem) {
                    $variant = $variants->get($comboItem->product_variant_id);
                    if ($variant) {
                        $qty = $comboItem->quantity * $item->quantity;
                        $variant->decrement('stock', min($variant->stock, $qty));
                        $variant->decrement('reserved_stock', min($variant->reserved_stock, $qty));
                    }
                }
            } elseif ($item->variant_id) {
                $variant = $variants->get($item->variant_id);
                if ($variant) {
                    $variant->decrement('stock', min($variant->stock, $item->quantity));
                    $variant->decrement('reserved_stock', min($variant->reserved_stock, $item->quantity));
                }
            }
        }
    }

    /**
     * Release reserved stock back to the available pool (for cancellations).
     * Uses min() to prevent reserved_stock from going negative.
     * Batch-loads all variant rows in a single locked query to avoid N+1.
     */
    private function releaseStock(Order $order): void
    {
        $variants = $this->loadLockedVariants($order);

        foreach ($order->items as $item) {
            if ($item->combo_id && $item->combo) {
                foreach ($item->combo->items as $comboItem) {
                    $variant = $variants->get($comboItem->product_variant_id);
                    if ($variant) {
                        $qty = $comboItem->quantity * $item->quantity;
                        $variant->decrement('reserved_stock', min($variant->reserved_stock, $qty));
                    }
                }
            } elseif ($item->variant_id) {
                $variant = $variants->get($item->variant_id);
                if ($variant) {
                    $variant->decrement('reserved_stock', min($variant->reserved_stock, $item->quantity));
                }
            }
        }
    }

    /**
     * Collect all variant IDs referenced by this order's items (direct + combo components)
     * and lock them in a single query — one batch lock instead of one lock per item.
     *
     * @return \Illuminate\Support\Collection<int, ProductVariant> keyed by variant id
     */
    private function loadLockedVariants(Order $order): \Illuminate\Support\Collection
    {
        $variantIds = collect();

        foreach ($order->items as $item) {
            if ($item->combo_id && $item->combo) {
                $variantIds = $variantIds->merge(
                    $item->combo->items->pluck('product_variant_id')
                );
            } elseif ($item->variant_id) {
                $variantIds->push($item->variant_id);
            }
        }

        return ProductVariant::whereIn('id', $variantIds->unique())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    private function isValidTransition(string $current, string $next): bool
    {
        $allowed = [
            'pending'    => ['confirmed', 'processing', 'cancelled'],
            'confirmed'  => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped'    => ['delivered', 'returned'],
            'delivered'  => [],
            'cancelled'  => [],
            'returned'   => [],
        ];

        return in_array($next, $allowed[$current] ?? []);
    }
}
