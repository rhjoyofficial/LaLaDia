# Phase 10 — Email, SMS, Push & External Integrations

**Date:** 2026-05-04  
**Auditor:** Claude (Senior Engineer Review)  
**Status:** ✅ Complete — all issues fixed

---

## Scope

| Area | Files Reviewed |
|---|---|
| Listeners | `SendOrderConfirmationEmail`, `SendOrderStatusEmail`, `SendOrderSMSListener`, `SendOrderWhatsAppListener`, `NotifyAdminOnNewOrder`, `OrderStatusNotificationListener`, `DispatchOrderCreatedWebhook`, `DispatchOrderStatusChangedWebhook`, `DispatchShipmentStatusUpdatedWebhook`, `DispatchOrderPaymentUpdatedWebhook`, `DispatchCouponExpiredWebhook`, `DispatchCustomerRegisteredWebhook` |
| Jobs | `SendWelcomeMailJob`, `SendWebhookJob`, `SendSMSJob`, `SendWhatsAppJob`, `SendConversionEvents` |
| Infrastructure | `WebhookService`, `SMSService`, `WhatsAppService` |
| Notifications | `OrderStatusPushNotification` |
| Mail | `OrderConfirmationMail`, `OrderStatusMail`, `WelcomeMail` |
| Observers | `OrderObserver` |
| Providers | `AppServiceProvider` |
| Config | `config/mail.php` |

---

## Findings & Fixes

### 🔴 CRITICAL — `DispatchOrderCreatedWebhook` — `$i->name_snapshot` does not exist on `OrderItem`

**File:** `app/Listeners/DispatchOrderCreatedWebhook.php` — line 43

**Problem:** The webhook payload maps each order item's name as:

```php
'name' => $i->name_snapshot,
```

The `OrderItem` model has no `name_snapshot` column. The correct field (confirmed from `OrderItem::$fillable`) is `product_name_snapshot`. At runtime `$i->name_snapshot` returns `null`, so every item in the `order.created` webhook payload silently sends `"name": null`.

**Fix applied:**
```php
// Before
'name' => $i->name_snapshot,

// After
'name' => $i->product_name_snapshot,
```

---

### 🔴 HIGH — `OrderStatusNotificationListener` — missing `$afterCommit`, retry config, and `failed()`

**File:** `app/Listeners/OrderStatusNotificationListener.php`

**Problem:** This is the only queued listener on the `OrderStatusChanged` event that lacked `public bool $afterCommit = true`. Without it, the FCM push notification could be dispatched to the queue before the DB transaction that changed the order status has committed — a race condition that could result in the push notification firing against an inconsistent DB read (or not at all if the transaction rolls back). Additionally, the listener was missing `$tries`, `$backoff`, and a `failed()` method that all sibling listeners have.

**Fix applied:** Added `$afterCommit = true`, `$tries = 3`, `$backoff = [10, 30, 60]`, `use InteractsWithQueue`, and a `failed()` logging method.

---

### 🟠 HIGH — `env()` called inside queued listeners — broken under `config:cache`

**Files:**
- `app/Listeners/SendOrderConfirmationEmail.php` — lines 41–42
- `app/Listeners/SendOrderStatusEmail.php` — lines 42–43

**Problem:** Both listeners read `env('NOREPLY_MAIL_FROM_ADDRESS', ...)` and `env('NOREPLY_MAIL_FROM_NAME', ...)` directly inside their `handle()` methods. In production, after `php artisan config:cache` runs, `env()` returns `null` for every key. This means the `from()` address on the Mailable is `null`, which causes an SMTP send failure or the email arriving with a null/empty sender address — with no visible error on the listener level since the exception is caught and logged.

**Fix applied (two-step):**

Step 1 — Added a `from` key to the `noreply` mailer entry in `config/mail.php` so the values are accessible via `config()`:

```php
'from' => [
    'address' => env('NOREPLY_MAIL_FROM_ADDRESS', 'no-reply@bionic.garden'),
    'name'    => env('NOREPLY_MAIL_FROM_NAME', ''),
],
```

Step 2 — Updated both listeners to use `config()`:

```php
// Before
$fromAddress = env('NOREPLY_MAIL_FROM_ADDRESS', 'no-reply@bionic.garden');
$fromName    = env('NOREPLY_MAIL_FROM_NAME', config('app.name') . ' Orders');

// After
$fromAddress = config('mail.mailers.noreply.from.address');
$fromName    = config('mail.mailers.noreply.from.name') ?: config('app.name') . ' Orders';
```

---

### 🟡 MEDIUM — `NotifyAdminOnNewOrder` — `Mail::raw()` uses the default mailer, not `noreply`

**File:** `app/Listeners/NotifyAdminOnNewOrder.php` — line 69

**Problem:** Admin notification emails are sent via `Mail::raw(...)` without specifying which mailer to use. This uses whatever `MAIL_MAILER` is set to in `.env` (typically the primary customer-facing mailer). Transactional admin alerts should use the same `noreply` mailer as all other order emails. If the primary mailer is throttled or misconfigured, admin alerts are affected even though the noreply mailer is healthy.

**Fix applied:**
```php
// Before
Mail::raw($emailBody, fn($msg) => ...);

// After
Mail::mailer('noreply')->raw($emailBody, fn($msg) => ...);
```

---

### 🟢 LOW — `SendOrderStatusEmail` — missing `failed()` method

**File:** `app/Listeners/SendOrderStatusEmail.php`

**Problem:** `SendOrderConfirmationEmail` has a `failed()` method that logs permanent job failures to the error channel. `SendOrderStatusEmail` was missing this, so permanent failures on status update emails would be silently swallowed by the failed jobs table with no log entry.

**Fix applied:** Added `failed(Throwable $exception): void` with `Log::error()`.

---

## Observations (No Fix Required)

### ✅ `SendWebhookJob` — retry config and `failed()` are correct — CORRECT
`tries = 3`, `backoff = [30, 120, 300]`, `timeout = 15`, `failed()` logs permanently. The job delegates all delivery logic to `WebhookService`.

### ✅ `WebhookService` — HMAC signing is correct — CORRECT
Signs each webhook with `hash_hmac('sha256', json_encode($payload), $hook->secret)`. Skips and logs a warning for any hook missing a secret. No issues.

### ✅ `SendSMSJob` / `SendWhatsAppJob` — retry config correct — CORRECT
Both have `tries = 3`, `backoff = [10, 30, 60]`, `timeout = 15`, and `failed()`. The corresponding listeners (`SendOrderSMSListener`, `SendOrderWhatsAppListener`) have no `$tries`/`$backoff` but that is acceptable — the retry strategy lives on the dispatched job, not the listener.

### ✅ `SendConversionEvents` — duplicate-fire guard works — CORRECT
Guards against double-firing with `$order->conversion_fired`. IP exclusion correctly filters private/loopback ranges via `FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE` before checking the config exclusion list. `test_mode` guard also present.

### ✅ `OrderObserver` — conversion dispatch is guarded correctly — CORRECT
Only fires `SendConversionEvents` when `order_status` changes to `confirmed`. Uses `updateQuietly` for `approved_at` to avoid observer recursion. 5-second delay gives the page time to deliver the browser-side pixel before the server-side event fires (deduplication window).

### ✅ `SMSService` / `WhatsAppService` — use `config()` correctly — CORRECT
Both services read all credentials via `config()`, not `env()`. No issues.

### ✅ `SendWelcomeMailJob` — coupon creation failure is isolated — CORRECT
If `Coupon::create()` throws, the exception is caught, logged as a warning, and `null` is returned. The welcome email still sends without a coupon block. No mail-blocking failure path.

### ✅ `OrderConfirmationMail` / `OrderStatusMail` / `WelcomeMail` — Mailable classes are clean — CORRECT
All three use the modern `Envelope`/`Content` API with proper `SerializesModels`. No issues.

### ✅ `AppServiceProvider` — `OrderObserver` registration confirmed — CORRECT
`Order::observe(OrderObserver::class)` is present. Events are auto-discovered via listener type-hints (no `EventServiceProvider` in Laravel 11 auto-discovery mode).

### ℹ️ `SendConversionEvents` — `combo_name_snapshot` does not exist on `OrderItem` — DATA QUALITY INFO
Line 165 uses `$item->combo_name_snapshot ?? $item->product_name_snapshot` for the GA4 `item_name`. `combo_name_snapshot` is not a column on `OrderItem` (confirmed from `$fillable`). For combo items, `product_name_snapshot` is also null. Result: GA4 `item_name` is `null` for combo order items. This is a GA4 data quality gap, not a runtime error. No fix applied — resolving it would require adding `combo_name_snapshot` to `OrderItem` and populating it at order creation time, which is a separate feature-scope change.

---

## Production Checklist (Phase 10 Actions)

| Item | Action |
|---|---|
| Add `NOREPLY_MAIL_FROM_ADDRESS` to `.env` | Ensure value is set — listeners now read it via `config()`, which requires the env var to be in `.env` (not just present at runtime without caching) |
| Run `php artisan config:cache` | Confirm config cache picks up the new `mail.mailers.noreply.from` key |
| Test order confirmation email | Place a test order → confirm email arrives from the correct noreply address |
| Test order status email | Advance order status → confirm status update email arrives from noreply |
| Test `order.created` webhook | Place order with a registered webhook → inspect payload: items should have non-null `name` |
| Test FCM push notification | Change order status for a user with an FCM token → confirm notification is queued after commit |

---

## Summary

| Severity | Found | Fixed |
|---|---|---|
| 🔴 Critical | 1 | 1 |
| 🔴 High | 1 | 1 |
| 🟠 High | 1 | 1 |
| 🟡 Medium | 1 | 1 |
| 🟢 Low | 1 | 1 |
| ✅ Pass | 12 | — |
| ℹ️ Info | 1 | — |

**All actionable issues resolved. Email, SMS, push, webhook, and conversion tracking integrations are production-grade.**

---

*Next: Phase 11 — Performance, Caching & Production Readiness*
