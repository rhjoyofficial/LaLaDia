<?php

use App\Console\Commands\AbandonExpiredCarts;
use App\Console\Commands\CheckCodCancellations;
use App\Console\Commands\ExpireCoupons;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Deactivate coupons whose end_date has passed
Schedule::command(ExpireCoupons::class)
    ->daily()
    ->description('Deactivate expired coupons');

// Release reserved stock for abandoned/expired guest carts
Schedule::command(AbandonExpiredCarts::class)
    ->hourly()
    ->description('Release reserved stock from expired guest carts');

// Dispatch server-side conversion events for COD orders confirmed > 48 h ago
Schedule::command(CheckCodCancellations::class)
    ->hourly()
    ->description('Fire missed conversion events for approved COD orders');

// ── Maintenance: prune growing tables ────────────────────────────────────────

// Prune activity_log entries older than 90 days to keep the table manageable.
// Spatie's activitylog ships this command — requires 'delete_records_older_than_days'
// to be set in config/activitylog.php (or pass --days=90 directly).
Schedule::command('activitylog:clean --days=90')
    ->monthly()
    ->description('Prune activity log entries older than 90 days');

// Prune read database notifications older than 30 days.
Schedule::command('model:prune', ['--model' => [\Illuminate\Notifications\DatabaseNotification::class]])
    ->weekly()
    ->description('Prune old database notifications');

// Prune expired Sanctum personal access tokens (respects SANCTUM_TOKEN_EXPIRATION).
Schedule::command('sanctum:prune-expired --hours=168')
    ->daily()
    ->description('Prune expired Sanctum tokens (7-day window)');

// ── Queue worker (shared/cron hosting fallback) ───────────────────────────────
// On production servers use Supervisor to run a persistent `queue:work` daemon
// instead — that eliminates the 60-second dispatch lag this approach has.
// This scheduler entry is a safe fallback for hosts without Supervisor access.
Schedule::command('queue:work --stop-when-empty --tries=3 --timeout=60 --max-jobs=500')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->runInBackground()
    ->description('Process queued jobs (emails, SMS, notifications)');

// Prune stale database sessions (driver=database only; safe to leave in for other drivers).
Schedule::command('session:gc')
    ->daily()
    ->description('Prune expired database sessions');
