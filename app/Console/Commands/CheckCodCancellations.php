<?php

namespace App\Console\Commands;

use App\Domains\Order\Models\Order;
use App\Jobs\SendConversionEvents;
use Illuminate\Console\Command;

class CheckCodCancellations extends Command
{
    protected $signature = 'orders:check-cod-cancellations';

    protected $description = 'Dispatch conversion events for approved COD orders where conversion has not yet fired';

    public function handle(): void
    {
        $count = 0;

        Order::query()
            ->where('payment_method', 'cod')
            ->where('conversion_fired', false)
            ->whereNotNull('approved_at')
            ->where('approved_at', '<=', now()->subHours(48))
            ->whereNotIn('order_status', ['cancelled', 'returned'])
            ->chunkById(50, function ($orders) use (&$count) {
                foreach ($orders as $order) {
                    SendConversionEvents::dispatch($order);
                    $count++;
                }
            });

        $this->info("Dispatched conversion events for {$count} order(s).");
    }
}
