# LaLaDia — Complete Tracking & Analytics Developer Guide

> **Project:** LaLaDia — Laravel + Vanilla JS / Alpine.js e-commerce  
> **Stack:** Laravel 12, GTM, GA4, Meta Pixel + CAPI, Consent Mode v2  
> **Last Updated:** 2026-05-17  
> **Scope:** Everything. GTM, dataLayer, GA4 browser events, GA4 Measurement Protocol (server-side),
> Meta Pixel browser events, Meta Conversions API (CAPI), Consent Mode v2, first-party cookies,
> deduplication, data quality, what is done, what is not done, what to do next.

---

## Table of Contents

1. [The Big Picture — How Everything Connects](#1-the-big-picture--how-everything-connects)
2. [Environment Setup — Keys You Must Configure](#2-environment-setup--keys-you-must-configure)
3. [Layer 1: Consent Mode v2](#3-layer-1-consent-mode-v2)
4. [Layer 2: GTM & the dataLayer](#4-layer-2-gtm--the-datalayer)
5. [Layer 3: GA4 Browser-Side Events](#5-layer-3-ga4-browser-side-events)
6. [Layer 4: Meta Pixel Browser-Side](#6-layer-4-meta-pixel-browser-side)
7. [Layer 5: Meta Conversions API (CAPI) — Server-Side](#7-layer-5-meta-conversions-api-capi--server-side)
8. [Layer 6: GA4 Measurement Protocol — Server-Side](#8-layer-6-ga4-measurement-protocol--server-side)
9. [Deduplication — How Double-Counting Is Prevented](#9-deduplication--how-double-counting-is-prevented)
10. [First-Party Cookies & Data Capture](#10-first-party-cookies--data-capture)
11. [The Two Checkout Paths](#11-the-two-checkout-paths)
12. [Order Tracking Fields on the Database](#12-order-tracking-fields-on-the-database)
13. [IP Filtering & Test Mode](#13-ip-filtering--test-mode)
14. [Complete File Reference](#14-complete-file-reference)
15. [What Is Not Yet Done](#15-what-is-not-yet-done)
16. [GTM Configuration You Must Do Manually](#16-gtm-configuration-you-must-do-manually)
17. [How to Test Everything](#17-how-to-test-everything)
18. [Common Mistakes and How They Are Protected](#18-common-mistakes-and-how-they-are-protected)

---

## 1. The Big Picture — How Everything Connects

Every user action on the site flows through this pipeline:

```
USER ACTION
    │
    ▼
BROWSER (JavaScript)
    ├─ window.dataLayer.push({ event: '...', ecommerce: {...} })
    │       │
    │       ▼
    │   Google Tag Manager (GTM)
    │       ├─ Forwards events → Google Analytics 4 (GA4)  [realtime reports]
    │       └─ Forwards events → Meta Pixel (fbq)          [ad campaign attribution]
    │
    └─ Meta Pixel (fbq) — direct PageView on every page
            │
            ▼ (via GTM for Purchase specifically)
        Meta Ads Manager — browser-side attribution

ORDER PLACED
    │
    ▼
Laravel Backend (PHP)
    ├─ order_status changed to 'confirmed'
    │       │
    │       ▼
    │   OrderObserver::updated()
    │       └─ dispatches SendConversionEvents job (5-second delay)
    │               │
    │               ├─ sendToMeta()    → Meta CAPI (Graph API v21.0)
    │               └─ sendToGA4()    → GA4 Measurement Protocol
    │
    └─ CheckoutController / LandingCheckoutController
            └─ sets session: pending_purchase_event (for browser purchase event)
```

### Why two channels for purchase?

The **browser Pixel / GTM** fires immediately when the success page loads (fast, real-time, user session context — cookies, browsing history). The **server-side CAPI / GA4 Measurement Protocol** fires when an admin marks the order `confirmed` (accurate — only real orders, not abandoned ones). These two channels are **deduplicated** using `event_id` so the platforms don't double-count them.

---

## 2. Environment Setup — Keys You Must Configure

All tracking keys live in `.env`. They map to `config/tracking.php` and `config/services.php`.

### Required `.env` keys

```dotenv
# ── Google Tag Manager ──────────────────────────────────────────────────
GTM_ID=GTM-XXXXXXX                # from GTM > Admin > Container Settings

# ── Meta (Facebook) Pixel & CAPI ───────────────────────────────────────
META_PIXEL_ID=                    # from Meta Events Manager > Datasets > your pixel
META_ACCESS_TOKEN=                # System User token — Events Manager > Settings > CAPI > Generate token
# META_TEST_EVENT_CODE=TEST12345  # ONLY during testing — remove before going live

# ── Google Analytics 4 ─────────────────────────────────────────────────
GA4_MEASUREMENT_ID=G-XXXXXXXXXX   # GA4 Admin > Data Streams > Web > Measurement ID
GA4_API_SECRET=                   # GA4 Admin > Data Streams > Web > Measurement Protocol API Secrets

# ── Tracking Filters ────────────────────────────────────────────────────
TRACKING_EXCLUDED_IPS=            # comma-separated, e.g. 192.168.1.100,10.0.0.1
```

### Where these map in code

| `.env` Key | Config key | Read by |
|---|---|---|
| `GTM_ID` | `services.gtm.id` | `layouts/app.blade.php`, `layouts/guest.blade.php` |
| `META_PIXEL_ID` | `tracking.meta_pixel_id` | `app/Jobs/SendConversionEvents.php` (CAPI only — GTM manages browser pixel) |
| `META_ACCESS_TOKEN` | `tracking.meta_access_token` | `app/Jobs/SendConversionEvents.php` |
| `META_TEST_EVENT_CODE` | `tracking.meta_test_event_code` | `app/Jobs/SendConversionEvents.php` |
| `GA4_MEASUREMENT_ID` | `tracking.ga4_measurement_id` | `app/Jobs/SendConversionEvents.php` |
| `GA4_API_SECRET` | `tracking.ga4_api_secret` | `app/Jobs/SendConversionEvents.php` |
| `TRACKING_EXCLUDED_IPS` | `tracking.excluded_ips` | `OrderObserver.php`, `SendConversionEvents.php` |

**Config files:**
- [`config/tracking.php`](../config/tracking.php) — Meta CAPI, GA4 MP, excluded IPs
- `config/services.php` — GTM ID only (`services.gtm.id`). `services.meta.pixel_id` is no longer used in Blade layouts.

---

## 3. Layer 1: Consent Mode v2

### What it is

Consent Mode v2 is a Google requirement (mandatory in Europe, best practice globally). It tells GTM and Google's tags whether the user has consented to tracking. Without it, Google's tags operate in a limited mode and your data quality degrades.

### How it works in LaLaDia

**Step 1 — Defaults set before GTM loads** (in `<head>`, before the GTM snippet):

**File:** [`resources/views/layouts/app.blade.php`](../resources/views/layouts/app.blade.php) lines ~35-46

```blade
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    @php $consent = request()->cookie('laladia_consent') ?? 'denied'; @endphp
    gtag('consent', 'default', {
        ad_storage:          '{{ $consent === "granted" ? "granted" : "denied" }}',
        analytics_storage:   '{{ $consent === "granted" ? "granted" : "denied" }}',
        ad_user_data:        '{{ $consent === "granted" ? "granted" : "denied" }}',
        ad_personalization:  '{{ $consent === "granted" ? "granted" : "denied" }}',
    });
</script>
```

The PHP reads the `laladia_consent` cookie **server-side**, so the consent state is correct from the very first script execution. There is no flash of denied→granted.

**Step 2 — Cookie consent banner** (bottom of `<body>`):

**File:** [`resources/views/partials/cookie-consent.blade.php`](../resources/views/partials/cookie-consent.blade.php)

- Only shown when `laladia_consent` cookie is not set (first visit)
- **Accept** → sets `laladia_consent=granted` (1 year) + calls `gtag('consent', 'update', { all: 'granted' })`
- **Decline** → sets `laladia_consent=denied` (1 year) + banner removed
- Banner is included in `app.blade.php` after the GTM noscript tag

**Step 3 — Cookie key:** `laladia_consent` — values: `granted` | `denied`

### What Consent Mode does NOT do yet

- No granular consent (analytics separate from ads) — currently all-or-nothing
- No integration with a CMP (Consent Management Platform like CookieBot or OneTrust)
- Auth pages (`guest.blade.php`) do not show the banner — add if needed

### GTM requirement

In GTM, under **Admin > Container Settings > Consent Overview**, ensure all your tags are marked as requiring the appropriate consent type. For Meta Pixel: `ad_storage + ad_user_data`. For GA4: `analytics_storage`.

---

## 4. Layer 2: GTM & the dataLayer

### The dataLayer contract

Every piece of data your JavaScript wants to send to GA4 or Meta goes through `window.dataLayer.push()`. GTM listens, matches triggers, and forwards to the right platforms.

**The one rule:** Only [`resources/js/analytics/AnalyticsManager.js`](../resources/js/analytics/AnalyticsManager.js) is allowed to call `dataLayer.push()` with ecommerce events. Nothing else in the codebase does it.

### Base dataLayer push (every page)

**File:** [`resources/views/partials/datalayer.blade.php`](../resources/views/partials/datalayer.blade.php)

This partial is included in `<head>` on **every page** via `layouts/app.blade.php` and `layouts/guest.blade.php`. It fires before GTM loads.

```js
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({
    'user_type':   'logged_in' | 'guest',   // for GTM audience segmentation
    'user_id':     123 | null,               // numeric user ID (null for guests)
    'environment': 'production' | 'local',   // skip reporting in dev
    'page_type':   'home' | 'product' | 'combo' | 'landing' | 'shop' |
                   'cart' | 'checkout' | 'thank-you' | 'order-failed' |
                   'account' | 'other'
});
```

**How `page_type` is set:** A View Composer in [`app/Providers/AppServiceProvider.php`](../app/Providers/AppServiceProvider.php) maps the current route name to a `$pageType` string. It runs automatically for every request that includes `partials.datalayer`. You do not need to set it in any controller.

```php
// AppServiceProvider::boot()
View::composer('partials.datalayer', function ($view) {
    $route = request()->route()?->getName() ?? '';
    $pageType = match (true) {
        $route === 'product.show'     => 'product',
        $route === 'combos.show'      => 'combo',
        $route === 'home'             => 'home',
        $route === 'order.success'    => 'thank-you',
        str_starts_with($route, 'checkout.') => 'checkout',
        // ... etc.
        default => 'other',
    };
    $view->with('pageType', $pageType);
});
```

### `window.__ga4__` — Page-level event injection

When a controller wants to fire a page-level GA4 event (e.g., `view_item`), it passes a `$ga4` array to the view. The `datalayer.blade.php` partial picks this up:

```blade
@if (!empty($ga4))
window.__ga4__ = @json($ga4);
@endif
```

Then in `app.js`, after DOMContentLoaded, `Analytics.autoFire()` reads `window.__ga4__` and fires the right event. This is the bridge between PHP (which knows the data) and JS (which fires the event).

### ecommerce: null clearing

GA4's specification requires clearing the `ecommerce` object before every ecommerce event push. `AnalyticsManager.push()` does this automatically:

```js
push(payload) {
    if ('ecommerce' in payload) {
        window.dataLayer.push({ ecommerce: null }); // ← mandatory
    }
    window.dataLayer.push(payload);
}
```

Without this, item arrays from previous events bleed into the next event. GA4 would show wrong product data associated with wrong events.

---

## 5. Layer 3: GA4 Browser-Side Events

All browser-side GA4 events are fired through GTM. The JavaScript pushes to `dataLayer`, GTM reads it and forwards to GA4 via the GA4 tag configured in your GTM container.

### Event inventory

| Event | Status | Where it fires | File |
|---|---|---|---|
| `view_item` | ✅ Done | Product page, Combo page, Landing pages (TYPE_PRODUCT, TYPE_COMBO) | `AnalyticsManager.autoFire()` |
| `add_to_cart` | ✅ Done | Any Add to Cart button in the store, Cart drawer, Listing landing pages | `CartManager._performCartAction()` + `listing-default.blade.php` |
| `view_cart` | ✅ Done | `/cart` page on load | `app.js` cart boot block |
| `begin_checkout` | ✅ Done | `/checkout` page after server preview confirms items | `CheckoutManager._fireBeginCheckout()` |
| `purchase` | ✅ Done | `/order-success/{order}` page | `order-success.blade.php` `@push('scripts')` |
| `remove_from_cart` | ❌ Not done | — | See [Section 15](#15-what-is-not-yet-done) |
| `add_shipping_info` | ❌ Not done | — | See [Section 15](#15-what-is-not-yet-done) |
| `add_payment_info` | ❌ Not done | — | See [Section 15](#15-what-is-not-yet-done) |
| `view_item_list` | ❌ Not done | — | See [Section 15](#15-what-is-not-yet-done) |
| `select_item` | ❌ Not done | — | See [Section 15](#15-what-is-not-yet-done) |

### The purchase event in detail

This is the most important event and has the most complexity.

**File:** [`resources/views/store/order-success.blade.php`](../resources/views/store/order-success.blade.php) — `@push('scripts')` block

```blade
@if (!empty($purchaseEvent))
<script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
        event:    'purchase',
        event_id: 'purchase_{{ $order->id }}',   // ← for Meta deduplication
        ecommerce: @json($purchaseEvent),
    });
</script>
@endif
```

The `$purchaseEvent` data comes from the PHP session (set by the checkout controller immediately after order creation). The `$order` variable is available from the route closure in `routes/web.php`. The `event_id` uses the database integer `$order->id` (not the order number) — the server-side CAPI uses the same value.

**Why `session()->pull()` and not `session()->get()`:** `pull()` reads AND deletes the session key in one atomic step. If the user refreshes the success page, `$purchaseEvent` is `null` on the second load, so the `@if (!empty($purchaseEvent))` block doesn't render, and the purchase event does not fire a second time.

### The GA4 item schema

Every item object must follow this structure:

```js
{
    item_id:        "SKU-123",      // REQUIRED — stable, unique product/variant ID
    item_name:      "Hilsa Fish",   // REQUIRED — product display name
    item_category:  "Fish",         // Recommended — top-level category
    item_brand:     "LaLaDia",      // Recommended — now present on view_item events
    item_variant:   "500g",         // Optional — variant name (weight/size)
    price:          350.00,         // Effective unit price (after variant discount)
    quantity:       1,              // Number of units
    index:          0,              // List position (0-based, for list events)
}
```

**`item_id` priority order** (consistent across all events):
1. `sku_snapshot` (order items) or `$product->sku` (catalog) — best, stable, human-readable
2. String variant ID — e.g. `"42"` — numeric but stable
3. `"combo_{id}"` — for combos, always prefixed to avoid collision with variant IDs

---

## 6. Layer 4: Meta Pixel Browser-Side

### What it does

The Meta Pixel fires a `PageView` event on every page load and purchase/product events via GTM triggers. Meta uses these to attribute ad campaigns.

### How the Pixel is loaded

**The Meta Pixel is loaded entirely through GTM.** There is no `fbq()` code in any Blade layout file. This is intentional — it allows Consent Mode to suppress the Pixel for users who decline cookies.

**GTM tag responsible:** `Meta Pixel - Base` — fires on All Pages trigger, runs:
```js
fbq('init', '871208752518827');
fbq('track', 'PageView');
```

See [`docs/gtm-setup-guide.md`](./gtm-setup-guide.md) for the exact GTM tag setup.

### How Purchase fires

1. `order-success.blade.php` pushes `{ event: 'purchase', event_id: 'purchase_N', ecommerce: {...} }` to `dataLayer`
2. GTM trigger `GA4 - purchase` fires
3. GTM tag `Meta Pixel - Purchase` calls `fbq('track', 'Purchase', {...}, { eventID: '{{DLV - event_id}}' })`

**The `eventID` in the GTM tag is critical** — this is how Meta deduplicates the browser event against the server-side CAPI event. Both must send `purchase_{order_id}` as the event ID. See [`docs/gtm-setup-guide.md`](./gtm-setup-guide.md) Tag 4.2.

---

## 7. Layer 5: Meta Conversions API (CAPI) — Server-Side

### What it does

CAPI is a direct server-to-Meta API call. It doesn't rely on the browser, cookies, or the user's network. This means it works even when:
- The user has an ad blocker
- The browser's cookie is blocked (iOS ITP)
- The user navigated away before the success page loaded

CAPI fires when an admin marks an order as **confirmed** (not when it's placed). This means only real, verified purchases are reported — not abandoned checkouts.

### The dispatch chain

```
Admin marks order status → 'confirmed'
    │
    ▼
OrderObserver::updated()                  [app/Domains/Order/Observers/OrderObserver.php]
    ├─ checks: test_mode? excluded IP?
    ├─ sets: $order->approved_at = now()
    └─ dispatches: SendConversionEvents::dispatch($order)->delay(5 seconds)
                    │
                    ▼
            Queue worker executes the job
                    │
                    ▼
            SendConversionEvents::handle()    [app/Jobs/SendConversionEvents.php]
                ├─ fresh(['items.variant.product.category', 'items.combo', 'shippingAddress'])
                ├─ conversion_fired guard (idempotency)
                ├─ sendToMeta($order)
                ├─ sendToGA4($order)
                └─ $order->update(['conversion_fired' => true])
```

**Why a 5-second delay on dispatch:** The order is dispatched immediately when the status changes, but the job waits 5 seconds before executing. This gives the database transaction time to fully commit before the job reads the order.

**Why `fresh()` with relations:** The `Order` model is serialized into the queue payload (only the ID is stored, actually — Laravel's `SerializesModels` trait). When the job runs, `fresh()` re-fetches from DB with all needed relations in one query chain, avoiding N+1 and ensuring fresh data.

### The CAPI payload

**File:** [`app/Jobs/SendConversionEvents.php`](../app/Jobs/SendConversionEvents.php) — `sendToMeta()` method

```php
[
    'data' => [[
        'event_name'       => 'Purchase',
        'event_time'       => $order->approved_at->timestamp,
        'event_id'         => 'purchase_' . $order->id,       // ← deduplication key
        'event_source_url' => $order->event_source_url,       // the page that was checked out
        'action_source'    => 'website',
        'user_data' => [
            'client_ip_address' => $order->ip_address,
            'client_user_agent' => $order->user_agent,
            'ph'  => [sha256(E164_normalized_phone)],          // E.164: 8801XXXXXXXXX
            'em'  => [sha256(lowercase_trimmed_email)],
            'fn'  => [sha256(lowercase_first_name)],
            'ln'  => [sha256(lowercase_last_name)],
            'ct'  => [sha256(lowercase_city)],
            'country' => [sha256('bd')],                       // always Bangladesh
            'fbp' => '_fbp cookie value',                      // captured at checkout
            'fbc' => '_fbc cookie value',                      // captured at checkout
        ],
        'custom_data' => [
            'currency'     => 'BDT',
            'value'        => (float) $order->grand_total,
            'order_id'     => $order->order_number,
            'contents'     => [[ 'id', 'quantity', 'item_price' ], ...],
            'content_type' => 'product',
        ],
    ]],
    'test_event_code' => 'TEST...',  // only if META_TEST_EVENT_CODE is set
]
```

**Phone normalization:** Bangladeshi numbers are `01XXXXXXXXX` (11 digits, starting with 0). E.164 requires country code without `+`: `8801XXXXXXXXX`. The code strips non-digits, then if 11 digits starting with `0`, replaces leading `0` with `880`.

**Email hashing:** `hash('sha256', strtolower(trim($email)))` — Meta requirement.

**API version:** `v21.0` of the Graph API (updated from the original `v19.0`).

### idempotency — `conversion_fired`

The job checks `$order->conversion_fired` before sending. After both Meta and GA4 calls complete, it sets `conversion_fired = true`. If the job retries (queue failure), it won't send duplicate conversions.

**Job retry config:** `public int $tries = 3; public int $backoff = 60;` — up to 3 attempts, 60 seconds between each.

---

## 8. Layer 6: GA4 Measurement Protocol — Server-Side

### What it does

GA4's Measurement Protocol lets you send events directly to GA4 from your server. Like Meta CAPI, it fires on order confirmed (not on order placed), giving you accurate, admin-verified conversion data.

### The payload

**File:** [`app/Jobs/SendConversionEvents.php`](../app/Jobs/SendConversionEvents.php) — `sendToGA4()` method

```php
[
    'client_id' => $order->ga_client_id ?? $order->ip_address ?? 'unknown',
    'events' => [[
        'name'   => 'purchase',
        'params' => [
            'transaction_id' => $order->order_number,
            'value'          => (float) $order->grand_total,
            'currency'       => 'BDT',
            'shipping'       => (float) $order->shipping_cost,
            'items'          => [[
                'item_id'       => sku_snapshot ?? variant_id ?? 'combo_N',
                'item_name'     => ...,
                'item_category' => 'Combo' | category_name,
                'price'         => ...,
                'quantity'      => ...,
            ], ...],
        ],
    ]],
]
```

**`client_id`:** This must be the GA4 client ID from the user's `_ga` cookie. Without it, GA4 creates an anonymous session that can't be joined to the user's browser session. The `_ga` cookie value is in format `GA1.1.<clientId>` — the part after the second dot is extracted. See [Section 10](#10-first-party-cookies--data-capture) for how this is captured.

**API endpoint:** `https://www.google-analytics.com/mp/collect?measurement_id={id}&api_secret={secret}`

---

## 9. Deduplication — How Double-Counting Is Prevented

### The problem

The browser fires a `Purchase` event on the success page. The server fires a `Purchase` event when the admin confirms the order. Without deduplication, **every sale is counted twice** — once in the browser channel, once in the server channel. This doubles reported revenue and inflates ROAS (Return on Ad Spend).

### How Meta deduplication works

Both the browser Pixel and the server CAPI must send the **exact same `event_id`** for the same purchase. Meta matches them and counts only one.

| Channel | Where set | Value |
|---|---|---|
| Browser (GTM tag) | `{ eventID: {{DLV - event_id}} }` in GTM Meta Purchase tag | `purchase_{{ $order->id }}` |
| Server (CAPI) | `'event_id' => 'purchase_' . $order->id` | Same value |

The browser value comes from:
1. `CheckoutController::store()` puts `'event_id' => 'purchase_' . $order->id` in the session
2. The success page Blade template puts `event_id: 'purchase_{{ $order->id }}'` as a top-level `dataLayer` key
3. The GTM tag reads the `event_id` DLV (Data Layer Variable) and passes it as `eventID`

**IMPORTANT:** This only works if GTM is configured correctly. See [Section 16](#16-gtm-configuration-you-must-do-manually).

### How GA4 deduplication works

GA4's Measurement Protocol uses `transaction_id`. If two purchase events (browser + server) have the same `transaction_id`, GA4 deduplicates them. Both use `$order->order_number` as the `transaction_id`.

GA4's deduplication is less strict than Meta's — it only deduplicates within the same property/stream, and there's a time window. The server-side event fires hours later (when admin confirms), which is outside most dedup windows. This is intentional: the browser event (places order) and server event (confirms order) may represent different stages of the funnel. If you want true deduplication, you can use the `event_id` / `event_key` approach on GA4 Measurement Protocol as well, but for this project it's acceptable to have both.

---

## 10. First-Party Cookies & Data Capture

### Cookies captured at checkout time

When a user places an order, these browser values are captured and stored on the Order model:

| Value | Cookie / Source | Order field | Captured in |
|---|---|---|---|
| Meta `_fbp` | `_fbp` cookie | `orders.fbp` | `CheckoutController`, `LandingCheckoutController` |
| Meta `_fbc` | `_fbc` cookie | `orders.fbc` | same |
| GA4 Client ID | `_ga` cookie value, extracted | `orders.ga_client_id` | same |
| Checkout URL | `Referer` header | `orders.event_source_url` | same |
| User agent | `User-Agent` header | `orders.user_agent` | same |
| IP address | `$request->ip()` | `orders.ip_address` | same |

### GA4 client ID extraction

The `_ga` cookie format is `GA1.1.<clientId>` where `<clientId>` is like `123456789.987654321`. The part after the second dot is what GA4's Measurement Protocol needs as `client_id`.

**Server side (when JS sends it):** The Alpine.js form in `_checkout.blade.php` extracts it in `init()`:
```js
const gaMatch = document.cookie.match(/_ga=GA\d+\.\d+\.(.+?)(?:;|$)/);
this.form.ga_client_id = gaMatch ? gaMatch[1] : null;
```

**Server fallback:** If `ga_client_id` is not in the request, the controller falls back to the raw `_ga` cookie value:
```php
'ga_client_id' => $request->input('ga_client_id') ?? $request->cookie('_ga'),
```

**File:** `orders` table `ga_client_id` column — stores the extracted client ID (not the full `_ga` cookie value).

### `_fbp` and `_fbc`

- `_fbp` — Meta's first-party browser fingerprint. Set by the Meta Pixel on first page load. Persists across sessions.
- `_fbc` — Meta's click identifier. Only present when the user arrived via a Meta ad (contains the `fbclid` from the URL). Persists 90 days.

Both are captured from cookies at checkout and stored on the order, then sent to CAPI as `user_data.fbp` and `user_data.fbc`. These dramatically improve Meta's ability to match the conversion to an ad click.

---

## 11. The Two Checkout Paths

LaLaDia has two completely separate checkout flows. Both must produce identical tracking data.

### Path A — Main Store Checkout

```
User browses store → adds to cart → /checkout → POST /checkout
```

**Controller:** [`app/Domains/Order/Controllers/CheckoutController.php`](../app/Domains/Order/Controllers/CheckoutController.php)

Key tracking code (in `store()` method):
```php
// 1. Capture tracking data at controller boundary
$data = array_merge($request->validated(), [
    'ip_address'       => $request->ip(),
    'fbp'              => $request->cookie('_fbp'),
    'fbc'              => $request->cookie('_fbc'),
    'ga_client_id'     => $request->input('ga_client_id') ?? $request->cookie('_ga'),
    'event_source_url' => $request->header('Referer'),
    'user_agent'       => $request->userAgent(),
    'test_mode'        => !app()->isProduction(),
]);

// 2. After order creation, eager-load for item_category
$order->load(['items.variant.product.category', 'items.combo']);

// 3. Put complete purchase event in session
$request->session()->put('pending_purchase_event', [
    'event_id'       => 'purchase_' . $order->id,    // ← dedup key
    'transaction_id' => $order->order_number,
    'value'          => (float) $order->grand_total,
    'currency'       => 'BDT',
    'coupon'         => $request->input('coupon_code'),
    'items'          => $order->items->map(fn ($item) => [
        'item_id'       => $item->sku_snapshot ?? ...  // snapshot priority
        'item_name'     => ...,
        'item_variant'  => $item->variant_title_snapshot,
        'item_category' => ...,
        'price'         => (float) $item->unit_price,
        'quantity'      => $item->quantity,
    ])->toArray(),
]);

// 4. Redirect to success page
return redirect()->to(route('order.success', ['order' => $order->order_number]));
```

### Path B — Landing Page Direct Checkout

```
User visits landing page → fills inline checkout form → POST /api/v1/landing/{slug}/checkout
```

**Controller:** [`app/Domains/Landing/Controllers/LandingCheckoutController.php`](../app/Domains/Landing/Controllers/LandingCheckoutController.php)

**Service:** [`app/Domains/Landing/Services/LandingCheckoutService.php`](../app/Domains/Landing/Services/LandingCheckoutService.php)

**Frontend form:** [`resources/views/landing/partials/_checkout.blade.php`](../resources/views/landing/partials/_checkout.blade.php) (Alpine.js)

The landing checkout path produces **exactly the same** tracking output as Path A. Both:
- Capture fbp, fbc, ga_client_id, event_source_url, user_agent, ip_address
- Set `pending_purchase_event` with event_id, all item fields, coupon
- Eager-load `items.variant.product.category` and `items.combo` before building the session payload
- Redirect to the same `/order-success/{order}` route, which fires the same browser purchase event

**The Alpine form `ga_client_id` extraction:**

```js
// In init() of landingCheckout() Alpine component:
const gaMatch = document.cookie.match(/_ga=GA\d+\.\d+\.(.+?)(?:;|$)/);
this.form.ga_client_id = gaMatch ? gaMatch[1] : null;
```

This runs on page load and stores the client ID in the form, which is then submitted as part of the JSON payload to the API.

---

## 12. Order Tracking Fields on the Database

The `orders` table has dedicated columns for all tracking data. These are what the server-side CAPI and GA4 Measurement Protocol jobs read.

| Column | Type | What it stores | Set by |
|---|---|---|---|
| `ip_address` | string | Customer's IP at checkout | Both checkout controllers |
| `user_agent` | string | Browser user agent string | Both checkout controllers |
| `event_source_url` | string | The URL the checkout came from (Referer) | Both checkout controllers |
| `fbp` | string | Meta `_fbp` cookie value | Both checkout controllers |
| `fbc` | string | Meta `_fbc` cookie value (from Meta ad click) | Both checkout controllers |
| `ga_client_id` | string | GA4 client ID (from `_ga` cookie, extracted) | Both checkout controllers |
| `test_mode` | boolean | Was this order placed in local/staging? | Both checkout controllers |
| `conversion_fired` | boolean | Has CAPI/MP job already run? (idempotency) | `SendConversionEvents::handle()` |
| `approved_at` | datetime | When admin confirmed the order | `OrderObserver::updated()` |

**Model:** [`app/Domains/Order/Models/Order.php`](../app/Domains/Order/Models/Order.php) — all these are in `$fillable` and `$casts`.

---

## 13. IP Filtering & Test Mode

### Test mode

When the app is not in `production` environment (`!app()->isProduction()`), all orders have `test_mode = true`. The `SendConversionEvents` job skips these orders entirely. This prevents development/staging checkouts from polluting your Meta Events Manager and GA4 data.

### IP exclusion

You can exclude specific IPs from conversion tracking (office IPs, QA testers, your own IP). Set them in `.env`:

```dotenv
TRACKING_EXCLUDED_IPS=203.0.113.1,203.0.113.2
```

Private/loopback ranges (`192.168.x.x`, `10.x.x.x`, `127.x.x.x`) are **always excluded** automatically — no configuration needed.

Both `OrderObserver` and `SendConversionEvents` check the excluded IPs independently (two layers of protection).

---

## 14. Complete File Reference

### PHP — Backend

| File | Role | Key methods |
|---|---|---|
| [`app/Jobs/SendConversionEvents.php`](../app/Jobs/SendConversionEvents.php) | Queue job — sends Meta CAPI + GA4 MP on order confirmed | `handle()`, `sendToMeta()`, `sendToGA4()` |
| [`app/Domains/Order/Observers/OrderObserver.php`](../app/Domains/Order/Observers/OrderObserver.php) | Dispatches `SendConversionEvents` when `order_status → confirmed` | `updated()` |
| [`app/Domains/Order/Controllers/CheckoutController.php`](../app/Domains/Order/Controllers/CheckoutController.php) | Main store checkout — captures tracking data, sets session payload | `store()` |
| [`app/Domains/Landing/Controllers/LandingCheckoutController.php`](../app/Domains/Landing/Controllers/LandingCheckoutController.php) | Landing page checkout — same as above | `checkout()` |
| [`app/Domains/Landing/Services/LandingCheckoutService.php`](../app/Domains/Landing/Services/LandingCheckoutService.php) | Creates the Order — saves ga_client_id to DB | `checkout()` → `Order::create()` |
| [`app/Providers/AppServiceProvider.php`](../app/Providers/AppServiceProvider.php) | View Composer that sets `$pageType` for every datalayer include | `boot()` |
| [`app/Domains/Store/Controllers/ProductPageController.php`](../app/Domains/Store/Controllers/ProductPageController.php) | Builds `$ga4` for product `view_item` | `show()` |
| [`app/Domains/Store/Controllers/ComboPageController.php`](../app/Domains/Store/Controllers/ComboPageController.php) | Builds `$ga4` for combo `view_item` | `show()` |
| [`app/Domains/Landing/Controllers/LandingPageController.php`](../app/Domains/Landing/Controllers/LandingPageController.php) | Builds `$ga4` for landing page `view_item` | `buildGa4Context()` |
| [`config/tracking.php`](../config/tracking.php) | All tracking env keys mapped to config | — |

### JavaScript — Frontend

| File | Role | Key methods |
|---|---|---|
| [`resources/js/analytics/AnalyticsManager.js`](../resources/js/analytics/AnalyticsManager.js) | Central hub — only file that calls `dataLayer.push()` for ecommerce events | `push()`, `viewItem()`, `addToCart()`, `viewCart()`, `beginCheckout()`, `autoFire()`, `_cartItemToGa4()` |
| [`resources/js/app.js`](../resources/js/app.js) | Orchestrator — boots managers, fires view_cart, exposes `window.Analytics` | DOMContentLoaded block |
| [`resources/js/cart/CartManager.js`](../resources/js/cart/CartManager.js) | Cart state + API — fires `add_to_cart` after successful cart API | `_performCartAction()` |
| [`resources/js/managers/CheckoutManager.js`](../resources/js/managers/CheckoutManager.js) | Checkout page JS — fires `begin_checkout` after server preview | `_fireBeginCheckout()` |
| [`resources/js/cart/product-card.js`](../resources/js/cart/product-card.js) | Variant card — keeps `data-ga-item` in sync when variant changes | `render(v)` |

### Blade — Views

| File | Role |
|---|---|
| [`resources/views/layouts/app.blade.php`](../resources/views/layouts/app.blade.php) | Main layout — Consent Mode defaults, GTM, Meta Pixel, datalayer include, cookie consent banner |
| [`resources/views/layouts/guest.blade.php`](../resources/views/layouts/guest.blade.php) | Auth pages layout — GTM, Meta Pixel, datalayer include |
| [`resources/views/partials/datalayer.blade.php`](../resources/views/partials/datalayer.blade.php) | Base dataLayer push (user_type, user_id, environment, page_type, window.__ga4__) |
| [`resources/views/partials/cookie-consent.blade.php`](../resources/views/partials/cookie-consent.blade.php) | Cookie consent banner (Consent Mode v2) |
| [`resources/views/store/order-success.blade.php`](../resources/views/store/order-success.blade.php) | Purchase dataLayer push + event_id |
| [`resources/views/landing/partials/_checkout.blade.php`](../resources/views/landing/partials/_checkout.blade.php) | Alpine.js landing checkout form — captures ga_client_id from _ga cookie |
| [`resources/views/landing/templates/listing-default.blade.php`](../resources/views/landing/templates/listing-default.blade.php) | Listing landing template — fires add_to_cart with live tier price |
| [`resources/views/components/product-card.blade.php`](../resources/views/components/product-card.blade.php) | Product card — `data-ga-item` on Add to Cart button |
| [`resources/views/store/product.blade.php`](../resources/views/store/product.blade.php) | Product detail page — `data-ga-item` on buttons, synced on variant change |
| [`resources/views/store/combo.blade.php`](../resources/views/store/combo.blade.php) | Combo detail page — `data-ga-item` on Add to Cart button |

### Configuration & env

| File | Role |
|---|---|
| [`.env.example`](../.env.example) | Documents all required tracking env keys with comments |
| [`config/tracking.php`](../config/tracking.php) | Maps `META_*`, `GA4_*`, `TRACKING_EXCLUDED_IPS` to config keys |

---

## 15. What Is Not Yet Done

### `remove_from_cart`

**Priority:** High — completes the funnel picture  
**When:** User removes an item from the cart drawer or `/cart` page  
**File to edit:** [`resources/js/cart/CartManager.js`](../resources/js/cart/CartManager.js) — `remove()` method

```js
async remove(cartItemId) {
    // Capture BEFORE the remove API call deletes it from state
    const removedItem = this.state.items.find(i => i.id === cartItemId);
    const res = await this.api('/remove', { cart_item_id: cartItemId });
    this.setState(res.data);

    if (removedItem) {
        window.Analytics?.push({
            event: 'remove_from_cart',
            ecommerce: {
                currency: 'BDT',
                value: parseFloat(removedItem.unit_price) * removedItem.quantity,
                items: [window.Analytics._cartItemToGa4(removedItem)],
            },
        });
    }
}
```

---

### `add_shipping_info`

**Priority:** Medium  
**When:** User selects a delivery zone on the checkout page  
**File to edit:** [`resources/js/managers/CheckoutManager.js`](../resources/js/managers/CheckoutManager.js)

Find where `this.selectedZoneId` is set (zone radio change handler) and add after `fetchPreview()` resolves:

```js
window.Analytics?.push({
    event: 'add_shipping_info',
    ecommerce: {
        currency:      'BDT',
        value:         this.previewData?.grand_total ?? 0,
        shipping_tier: this.zones?.find(z => z.id === this.selectedZoneId)?.name ?? '',
        items: this._checkoutItems()
                   .filter(i => !i.is_gift)
                   .map((i, idx) => window.Analytics._cartItemToGa4(i, idx)),
    },
});
```

---

### `add_payment_info`

**Priority:** Low  
**When:** User selects COD vs SSLCommerz on checkout  
**File to edit:** [`resources/js/managers/CheckoutManager.js`](../resources/js/managers/CheckoutManager.js)

Find the payment method selection handler and add:

```js
window.Analytics?.push({
    event: 'add_payment_info',
    ecommerce: {
        currency:     'BDT',
        value:        this.previewData?.grand_total ?? 0,
        payment_type: 'cod' | 'sslcommerz',
        items: this._checkoutItems()
                   .filter(i => !i.is_gift)
                   .map((i, idx) => window.Analytics._cartItemToGa4(i, idx)),
    },
});
```

---

### `view_item_list`

**Priority:** Medium  
**When:** User sees a product grid on `/products`, `/category/{slug}`, or `/combos`  
**Files to edit:**
- [`app/Domains/Store/Controllers/CatalogController.php`](../app/Domains/Store/Controllers/CatalogController.php) — build `$ga4` payload
- [`resources/js/analytics/AnalyticsManager.js`](../resources/js/analytics/AnalyticsManager.js) — add `view_item_list` case to `autoFire()`

In `CatalogController::index()`:
```php
$ga4 = [
    'event'     => 'view_item_list',
    'list_id'   => 'shop_page',
    'list_name' => 'Shop',
    'items'     => $products->map(fn ($p, $idx) => [
        'item_id'       => $p->sku ?? (string) $p->id,
        'item_name'     => $p->name,
        'item_category' => $p->category?->name,
        'item_brand'    => config('app.name', 'LaLaDia'),
        'price'         => (float) $p->variants->first()?->final_price,
        'index'         => $idx,
    ])->toArray(),
];
```

In `AnalyticsManager.autoFire()`:
```js
case 'view_item_list':
    this.push({
        event: 'view_item_list',
        ecommerce: {
            item_list_id:   cfg.list_id,
            item_list_name: cfg.list_name,
            items:          cfg.items,
        },
    });
    break;
```

---

### `select_item`

**Priority:** Low — implement alongside `view_item_list`  
**When:** User clicks a product card from a list  
**File to edit:** [`resources/views/components/product-card.blade.php`](../resources/views/components/product-card.blade.php) — the card's `<a>` link click handler

```js
card.querySelector('a').addEventListener('click', function () {
    window.Analytics?.push({
        event: 'select_item',
        ecommerce: {
            item_list_id:   listId,    // from data-list-id on parent grid
            item_list_name: listName,
            items: [gaItem],
        },
    });
});
```

---

### SSLCommerz payment gateway

When SSLCommerz is implemented, the checkout success/fail flows will need:
- A `purchase` session event set before redirecting to the payment gateway
- A separate landing page after payment confirmation (currently goes to `order.failed`)
- The server-side CAPI/MP will work automatically (fires on `confirmed`)

---

### TikTok Pixel + Events API

Explicitly deferred. TikTok has its own Pixel SDK and Events API. When you're ready, the pattern is nearly identical to Meta — browser Pixel via GTM, server-side Events API via a new job or extending `SendConversionEvents`.

---

## 16. GTM Configuration You Must Do Manually

> **Full step-by-step GTM guide:** [`docs/gtm-setup-guide.md`](./gtm-setup-guide.md)
>
> That file has every variable, trigger, tag, and consent setting with exact field values
> and copy-paste HTML for every Meta Pixel tag.

### Summary of what GTM must have

**Variables (Data Layer Variables):**
`DLV - event_id`, `DLV - page_type`, `DLV - user_id`, `DLV - ecommerce`,
`DLV - value`, `DLV - currency`, `DLV - coupon`, `DLV - transaction_id`

**Tags to create:**
- `GA4 - Configuration` — fires All Pages, sets `user_id` field
- `GA4 - view_item`, `add_to_cart`, `view_cart`, `begin_checkout`, `purchase` — GA4 event tags
- `Meta Pixel - Base` — fires All Pages, replaces the removed hard-coded `fbq` init
- `Meta Pixel - Purchase` — fires on `purchase` event, **must include `eventID: {{DLV - event_id}}`**
- `Meta Pixel - ViewContent`, `AddToCart`, `InitiateCheckout` — optional but recommended

**Consent settings (Admin → Container Settings → Consent Overview):**
- All GA4 tags → `analytics_storage`
- All Meta Pixel tags → `ad_storage` + `ad_user_data`

---

## 17. How to Test Everything

### Quick console checks

```js
// Paste in Chrome DevTools console on any page:

// 1. Is the base push working?
console.log(window.dataLayer[0]);
// Expected: { user_type: 'guest', user_id: null, environment: 'local', page_type: 'home' }

// 2. All events fired on this page:
console.table(window.dataLayer.filter(e => e.event));

// 3. The last purchase event (on /order-success page):
window.dataLayer.find(e => e.event === 'purchase');
// Expected: { event: 'purchase', event_id: 'purchase_123', ecommerce: { transaction_id: 'LLD-...', ... } }

// 4. Is Analytics available?
console.log(typeof window.Analytics?.push === 'function'); // true

// 5. Is consent state correct?
document.cookie.match(/laladia_consent=(\w+)/)?.[1]; // 'granted' | 'denied' | undefined
```

### Page-by-page verification

| Page | Expected `window.dataLayer` events |
|---|---|
| Any page | `[0]` = base push with `user_type`, `user_id`, `environment`, `page_type` |
| `/` (home) | `page_type: 'home'` |
| `/products/{slug}` | `page_type: 'product'` + `view_item` event with item data |
| `/combos/{slug}` | `page_type: 'combo'` + `view_item` with `item_id: 'combo_N'` |
| Add to Cart click | New `add_to_cart` event appears, `ecommerce.items[0].item_id` is correct |
| `/cart` | `view_cart` event with all cart items |
| `/checkout` | `begin_checkout` event — check `items` and `value` |
| `/order-success/{id}` | `purchase` event with `event_id`, `transaction_id`, all items with `item_category` |
| `/login` | Base push exists (`page_type: 'other'`), no tracking errors |

### GTM Preview mode (most reliable)

1. Open GTM → your container → **Preview**
2. Enter your site URL and click Connect
3. GTM opens your site with a debug panel at the bottom
4. Click any event in the left panel to see exactly what data GA4/Meta will receive
5. Check that `event_id` is present in the purchase event's Variables tab

### Meta test events

1. Set `META_TEST_EVENT_CODE=TEST12345` in `.env` (get the code from Events Manager > Test Events)
2. Place a test order (environment must be non-production, or temporarily override test_mode check)
3. Open Meta Events Manager > Test Events tab
4. You should see the `Purchase` event appear with your payload
5. Verify `ph` is in E.164 format hash, `fn`/`ln`/`ct`/`country` are present
6. **Remove `META_TEST_EVENT_CODE` from `.env` before going live**

### GA4 DebugView

1. In GA4: **Admin > DebugView** (or **Reports > Realtime**)
2. Add `&debug_view=true` to your GA4 Measurement Protocol API call temporarily (in `sendToGA4()` method, append `&debug=1` to the URL)
3. Events should appear in DebugView within seconds

---

## 18. Common Mistakes and How They Are Protected

| Mistake | Consequence | Protection |
|---|---|---|
| `ecommerce: null` not cleared before push | Item arrays from previous events bleed into next — GA4 reports wrong products on wrong events | `AnalyticsManager.push()` always clears first automatically |
| `purchase` fires twice on page refresh | Double revenue in GA4 | `session()->pull()` deletes key atomically after first read |
| Browser Pixel and CAPI counted as separate purchases | ROAS is doubled, budget decisions based on fake numbers | `event_id: 'purchase_N'` on both sides + GTM `eventID` config |
| `ga_client_id` missing on landing orders | GA4 MP events orphaned (no session attribution) | Alpine `init()` extracts from `_ga` cookie; controller fallback to raw cookie |
| Phone not E.164 — hashed differently on each system | Meta match rate drops significantly | Normalized to `880` prefix before hashing |
| `conversion_fired` not set | Queue retry sends CAPI twice | Flag set after both calls succeed; checked before each run |
| Analytics crash breaks cart | User cannot add to cart | All calls use `window.Analytics?.method()` optional chaining |
| test_mode orders in production data | Inflated conversions during testing | `test_mode = !app()->isProduction()` set at controller boundary |
| GA4 item_id `"42"` vs `"combo_42"` inconsistency | GA4 treats same product as two different products | All paths use the same priority logic: sku_snapshot → variant_id → `combo_N` |
| Bengali product names breaking JSON | JS syntax error, page crash | `@json()` Blade directive handles all Unicode encoding |
| `window.cart` (lowercase) vs `window.Cart` (capital) | Add to Cart silently does nothing | Fixed in `listing-default.blade.php`; use `window.Cart` in all new code |
