<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Notifications\OrderStatusPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class OrderStatusNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public bool  $afterCommit = true;
    public int   $tries       = 3;
    public array $backoff     = [10, 30, 60];

    public function handle(OrderStatusChanged $event): void
    {
        $user = $event->order->user;

        if (!$user) return;

        // Skip if FCM is not configured (placeholder key or missing)
        $key = config('firebase.server_key');
        if (empty($key) || $key === 'your_firebase_server_key') return;

        Notification::send(
            $user,
            new OrderStatusPushNotification($event->order)
        );
    }

    public function failed(Throwable $exception): void
    {
        Log::error('OrderStatusNotificationListener: failed permanently', [
            'error' => $exception->getMessage(),
        ]);
    }
}
