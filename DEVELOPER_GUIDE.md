# LaLaDia — Developer Guide

**Version:** 1.0 · **Date:** 2026-05-04 · **Author:** Lead Architect Review

> This guide is written for a developer joining the team on day one. Read it top-to-bottom once, then use it as a reference. It covers every layer of the system — from HTTP request to database row — with exact file paths for every claim.

---

## Table of Contents

1. [High-Level Architecture](#1-high-level-architecture)
2. [Tech Stack](#2-tech-stack)
3. [Folder & Project Structure](#3-folder--project-structure)
4. [Domain Map](#4-domain-map)
5. [Feature Map](#5-feature-map)
6. [Data Flow Walkthroughs](#6-data-flow-walkthroughs)
7. [Database Schema](#7-database-schema)
8. [Authentication & Authorization](#8-authentication--authorization)
9. [Event System & Async Processing](#9-event-system--async-processing)
10. [External Integrations](#10-external-integrations)
11. [Frontend Architecture](#11-frontend-architecture)
12. [Setup & Deployment](#12-setup--deployment)
13. [Key Conventions & Patterns](#13-key-conventions--patterns)
14. [Future Roadmap & Technical Debt](#14-future-roadmap--technical-debt)

---

## 1. High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                          BROWSER / CLIENT                           │
│                                                                     │
│  Blade + Alpine.js (Storefront)    Blade + Alpine.js (Admin Panel)  │
│  Vanilla JS Modules (app.js)       admin.js (ValidationManager)     │
└──────────────┬──────────────────────────────────┬───────────────────┘
               │  Blade page renders               │  Alpine.js calls
               │  + fetch() JSON calls             │  REST API
               ▼                                   ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         LARAVEL 12 (PHP 8.2)                        │
│                                                                     │
│  routes/web.php        ←  Blade page routes (storefront + admin)   │
│  routes/api.php        ←  /api/v1/* — JSON API                     │
│    └── public.php      ←  Public endpoints (no auth required)       │
│    └── admin.php       ←  Admin endpoints (auth:sanctum + admin)    │
│                                                                     │
│  Middleware Stack                                                   │
│    SecureHeaders (CSP, HSTS, X-Frame)                              │
│    HandleCartSession (laladia_cart_token cookie)                     │
│    EnsureUserIsAdmin   (role guard)                                  │
│    Spatie Permission   (granular permission checks)                 │
│                                                                     │
│  app/Domains/*  ←  Domain-Driven Design — all business logic here  │
│  app/Core/*     ←  Thin base classes                                │
│  app/Infrastructure/* ← Third-party driver adapters                │
│                                                                     │
│  Event Bus (Laravel auto-discovery)                                 │
│    Events → Listeners (queued or sync) → Jobs                       │
└──────────┬─────────────────────────────────┬────────────────────────┘
           │                                 │
           ▼                                 ▼
┌─────────────────┐              ┌─────────────────────────────┐
│  MySQL / SQLite │              │  Queue Worker (database/redis│
│  (primary store)│              │  queue:work --stop-when-empty│
│                 │              │  Scheduled every minute)     │
│  Cache table    │              │                             │
│  Sessions table │              │  Jobs: Email, SMS, WhatsApp │
│  Jobs table     │              │  Webhook, ConversionEvents  │
└─────────────────┘              └─────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────────────────────────────────┐
│  External Services                                                   │
│  ─────────────────────────────────────────────────────────────────  │
│  SMTP (noreply mailer)     GreenWeb SMS API    WhatsApp Business API│
│  Meta Conversion API       GA4 Measurement Protocol                 │
│  FCM (Firebase)            Pathao / Steadfast / RedX / CarryBee     │
│  SSLCommerz  (stub — not yet live)                                   │
└─────────────────────────────────────────────────────────────────────┘
```

The architecture follows **Domain-Driven Design (DDD)**. Every business capability lives in its own domain under `app/Domains/`. Controllers are thin: they resolve auth, validate input, delegate to a service, and return a response. Services own all business logic. Infrastructure drivers (courier, SMS, WhatsApp, webhooks) are behind interfaces, switchable via config.

---

## 2. Tech Stack

| Layer | Technology | Version / Notes |
|---|---|---|
| Language | PHP | 8.2+ |
| Framework | Laravel | 12.x |
| Database | MySQL (production) / SQLite (dev/test) | |
| Cache | Database (dev) → Redis (production) | `CACHE_STORE` env |
| Queue | Database (dev) → Redis (production) | `QUEUE_CONNECTION` env |
| Sessions | Database (dev) → Redis (production) | `SESSION_DRIVER` env |
| Auth | Laravel Sanctum | Stateful (web) + token (API) |
| Authorization | Spatie Laravel Permission | Roles + granular permissions |
| Frontend (storefront) | Blade + Tailwind CSS v4 + Alpine.js | Vite bundled |
| Frontend (admin) | Blade + Tailwind CSS + Alpine.js | Same Vite bundle |
| JS Build | Vite + `@vitejs/plugin-laravel` | `resources/js/app.js`, `admin.js` |
| Activity Log | Spatie Laravel ActivityLog | `activity_log` table |
| Push Notifications | `laravel-notification-channels/fcm` | Firebase Cloud Messaging |
| HTML Sanitization | mews/purifier | Used in rich-text fields |
| Scheduler | Laravel Scheduler via `queue:work` | Runs every minute via cron |
| Dev tooling | Laravel Pail, Pint, Sail | `require-dev` only |
| Debug | fruitcake/laravel-debugbar | `require-dev` — excluded from prod |

---

## 3. Folder & Project Structure

```
LaLaDia/
├── app/
│   ├── Console/Commands/          # Artisan scheduled commands
│   │   ├── AbandonExpiredCarts.php    # Hourly: release stock from expired carts
│   │   ├── ExpireCoupons.php          # Daily: fire CouponExpired event
│   │   └── CheckCodCancellations.php  # Hourly: dispatch conversion events for COD
│   │
│   ├── Core/
│   │   ├── BaseController.php         # Shared controller helpers
│   │   ├── BaseService.php            # (empty — reserved for future base)
│   │   └── BaseRepository.php         # (empty — reserved for future base)
│   │
│   ├── Domains/                   # ← ALL business logic lives here
│   │   ├── ActivityLog/           # Admin activity log viewer
│   │   ├── Admin/                 # Dashboard stats, Settings model, AdminLogger
│   │   ├── Auth/                  # Login, register, password reset, roles/perms
│   │   ├── Cart/                  # Cart model, CartService, CartPricingService
│   │   ├── Category/              # Product categories (CRUD + observer)
│   │   ├── Certification/         # Product certifications
│   │   ├── Coupon/                # Coupon CRUD, validation service
│   │   ├── Courier/               # Shipment assignment + status sync
│   │   ├── Customer/              # Customer admin views + account dashboard
│   │   ├── Intelligence/          # Product recommendation engine
│   │   ├── Landing/               # Landing pages (product/combo/sales/listing)
│   │   ├── Marketing/             # Referral commission service
│   │   ├── Notification/          # Admin broadcast notifications + failed job retry
│   │   ├── Order/                 # Orders, checkout, pricing, shipments
│   │   ├── Product/               # Products, variants, combos, tier prices
│   │   ├── Shipping/              # Shipping zones
│   │   ├── Store/                 # Storefront pages (home, catalog, product page)
│   │   └── Webhook/               # Outgoing webhook config + dispatch
│   │
│   ├── Events/                    # Domain events (thin data bags)
│   │   ├── OrderCreated.php
│   │   ├── OrderStatusChanged.php
│   │   ├── OrderPaymentUpdated.php
│   │   ├── ShipmentStatusUpdated.php
│   │   ├── CustomerRegistered.php
│   │   └── CouponExpired.php
│   │
│   ├── Helpers/
│   │   ├── ApiResponse.php        # Uniform JSON wrapper: success/error/paginated
│   │   ├── flash.php              # flash() global helper for blade flash messages
│   │   └── format.php             # Bengali number formatting helpers
│   │
│   ├── Http/
│   │   ├── Controllers/           # Base controller only
│   │   └── Middleware/
│   │       ├── SecureHeaders.php      # CSP, HSTS, X-Frame-Options on every response
│   │       ├── EnsureUserIsAdmin.php  # Blocks non-admin roles from /admin/*
│   │       └── HandleCartSession.php  # Manages laladia_cart_token cookie
│   │
│   ├── Infrastructure/            # Adapters for external systems
│   │   ├── Courier/
│   │   │   ├── CourierInterface.php   # Contract: createShipment, trackShipment, cancel
│   │   │   ├── CourierService.php     # Factory: resolves driver by config('courier.default')
│   │   │   └── Drivers/              # PathaoCourier, SteadfastCourier, RedxCourier, CarryBeeCourier
│   │   ├── SMS/SMSService.php         # GreenWeb HTTP API
│   │   ├── WhatsApp/WhatsAppService.php # WhatsApp Business API
│   │   └── Webhook/WebhookService.php  # HMAC-signed HTTP dispatch
│   │
│   ├── Jobs/                      # Queued jobs
│   │   ├── SendConversionEvents.php   # Meta CAPI + GA4 (with duplicate guard)
│   │   ├── SendWebhookJob.php         # Outgoing webhook dispatch
│   │   ├── SendSMSJob.php
│   │   ├── SendWhatsAppJob.php
│   │   └── SendWelcomeMailJob.php     # Creates welcome coupon + sends mail
│   │
│   ├── Listeners/                 # Event handlers
│   │   ├── SendOrderConfirmationEmail.php
│   │   ├── SendOrderStatusEmail.php
│   │   ├── SendOrderSMSListener.php
│   │   ├── SendOrderWhatsAppListener.php
│   │   ├── NotifyAdminOnNewOrder.php
│   │   ├── OrderStatusNotificationListener.php  # FCM push
│   │   ├── DeactivateExpiredCoupons.php          # Sync listener — deactivates immediately
│   │   ├── CreateCourierShipmentListener.php
│   │   ├── CreateReferralCommissionListener.php
│   │   └── Dispatch*Webhook.php (×5)            # One per event → SendWebhookJob
│   │
│   ├── Mail/                      # Mailable classes
│   │   ├── OrderConfirmationMail.php  → emails.order-confirmation
│   │   ├── OrderStatusMail.php        → emails.order-status
│   │   └── WelcomeMail.php            → emails.welcome
│   │
│   ├── Models/
│   │   └── User.php               # Core auth model (HasRoles, HasApiTokens)
│   │
│   ├── Notifications/
│   │   ├── OrderStatusPushNotification.php  # FCM push via FcmMessage
│   │   └── AdminBroadcastNotification.php
│   │
│   ├── Policies/
│   │   └── ProductPolicy.php
│   │
│   └── Providers/
│       ├── AppServiceProvider.php     # Registers all model observers
│       └── ViewServiceProvider.php    # Shares cached globalCategories with all views
│
├── bootstrap/
│   └── app.php                    # Laravel 12 bootstrap: routing, middleware, exception handler
│
├── config/
│   ├── laladia.php                 # ADMIN_PHONE, ADMIN_EMAIL
│   ├── courier.php                # Pathao, Steadfast, RedX, CarryBee credentials
│   ├── firebase.php               # FCM server key
│   ├── sms.php                    # GreenWeb SMS API token + URL
│   ├── tracking.php               # Meta pixel ID, GA4 keys, excluded IPs
│   └── whatsapp.php               # WhatsApp Business API credentials
│
├── database/
│   ├── migrations/                # 34 migrations (see §7 for schema map)
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php         # Creates 6 roles + 40 permissions
│       ├── ProductSeeder.php
│       ├── CategorySeeder.php
│       └── ... (8 more seeders)
│
├── resources/
│   ├── css/app.css                # Tailwind entry point + custom design tokens
│   ├── js/
│   │   ├── app.js                 # Storefront entry: CartManager, AuthManager, CheckoutManager
│   │   ├── admin.js               # Admin entry: ValidationManager + flash wiring
│   │   ├── api/                   # Thin fetch() wrappers per domain (auth, cart, coupon…)
│   │   ├── cart/                  # CartManager, CartRenderer, AddToCartBinder
│   │   ├── managers/              # CheckoutManager, ValidationManager
│   │   └── filter/                # catalogFilter, categoryFilter
│   └── views/
│       ├── admin/                 # Blade templates for admin panel
│       ├── store/                 # Blade templates for storefront
│       ├── landing/               # Landing page templates + partials
│       ├── emails/                # Transactional email templates (3 files)
│       ├── errors/                # 404.blade.php, 500.blade.php
│       ├── layouts/               # app.blade.php (storefront), admin.blade.php
│       └── components/            # Reusable Blade components
│
├── routes/
│   ├── web.php                    # Blade page routes (storefront + admin Blade views)
│   ├── api.php                    # Prefix /api/v1 → includes public.php + admin.php
│   ├── public.php                 # Public JSON API routes (no auth)
│   ├── admin.php                  # Admin JSON API routes (auth:sanctum + admin)
│   └── console.php                # Scheduled commands
│
└── vite.config.js                 # Inputs: app.css, app.js, admin.js
```

---

## 4. Domain Map

Each directory under `app/Domains/` is self-contained: its own Controllers, Models, Services, Requests, Resources, and Observers.

| Domain | Purpose |
|---|---|
| `Admin` | Dashboard KPIs, system settings CRUD, cache/maintenance management |
| `ActivityLog` | Read-only admin log viewer wrapping Spatie ActivityLog |
| `Auth` | Customer + admin login/register, password reset, role/permission CRUD |
| `Cart` | Session/DB cart with stock reservation and pricing preview |
| `Category` | Product category hierarchy with cache invalidation via observer |
| `Certification` | Product certifications displayed on product pages |
| `Coupon` | Coupon CRUD, bulk generation, validation service, usage tracking |
| `Courier` | Shipment creation, status sync, cancel — via pluggable courier drivers |
| `Customer` | Admin customer management + storefront account dashboard |
| `Intelligence` | Product recommendation engine (upsells, cross-sells) |
| `Landing` | Custom landing pages for products, combos, sales, and listings |
| `Marketing` | Referral commission tracking |
| `Notification` | Admin broadcast notifications + failed job management |
| `Order` | Checkout, order lifecycle, status transitions, admin order management |
| `Product` | Products, variants, combos, tier prices, relations |
| `Shipping` | Shipping zones with flat/free rates and reorder via SortableJS |
| `Store` | Public-facing storefront: home, catalog, product page, combo page |
| `Webhook` | Outgoing webhook config (CRUD), dispatch, HMAC signing |

---

## 5. Feature Map

### Storefront (Customer-Facing)

| Feature | Key Files |
|---|---|
| Home page (banners, categories, trending, combos) | `Store/Controllers/HomeController.php` |
| Product catalog with filters + price range | `Store/Controllers/CatalogController.php`, `resources/js/filter/catalogFilter.js` |
| Product detail page (variants, tier pricing, upsells) | `Store/Controllers/ProductPageController.php` |
| Combo detail page | `Store/Controllers/ComboPageController.php` |
| Landing pages (embedded single-page checkout) | `Landing/Controllers/LandingPageController.php`, `LandingCheckoutController.php` |
| Cart (add, remove, update, combo add) | `Cart/Controllers/CartController.php`, `Cart/Services/CartService.php` |
| Checkout (COD only; sslcommerz stub) | `Order/Controllers/CheckoutController.php`, `Order/Services/OrderService.php` |
| Checkout pricing preview | `Order/Controllers/CheckoutController.php@preview`, `Order/Services/CheckoutPricingService.php` |
| Order success / failed pages | `routes/web.php` inline + `Order/Controllers/OrderController.php` |
| Customer account dashboard | `Customer/Controllers/CustomerDashboard.php` |
| Customer order history | `Customer/Controllers/CustomerDashboard.php@orders` |
| Login / Register / Forgot password | `Auth/Controllers/WebAuthController.php`, `Auth/Controllers/AuthController.php` |
| Product search (live) | `Product/Controllers/ProductSearchController.php`, `resources/js/search-suggestion.js` |
| Product recommendations | `Product/Controllers/ProductRecommendationController.php`, `Intelligence/Services/` |
| Coupon validation (inline) | `Coupon/Controllers/PublicCouponController.php`, `Coupon/Services/CouponValidationService.php` |
| Informational pages (About, FAQ, Blog…) | `Store/Controllers/PageController.php` |
| Trust badges, certifications | `Certification/Models/Certification.php` (displayed on product page) |

### Admin Panel

| Feature | Key Files |
|---|---|
| Dashboard (KPIs, revenue chart, recent orders) | `Admin/Controllers/AdminDashboardController.php`, `Admin/Services/DashboardStatsService.php` |
| Product CRUD (with variants, tier prices, images) | `Product/Controllers/AdminProductController.php`, `views/admin/products/` |
| Combo CRUD | `Product/Controllers/AdminComboController.php`, `views/admin/combos/` |
| Product tier pricing | `Product/Controllers/ProductTierPriceController.php` |
| Product relations (upsells, cross-sells) | `Product/Controllers/ProductRelationController.php` |
| Category CRUD | `Category/Controllers/AdminCategoryController.php` |
| Order listing, detail, status update | `Order/Controllers/AdminOrderController.php`, `views/admin/orders/` |
| Admin order creation | `Order/Services/AdminOrderCreationService.php` |
| Order editing (items, customer, address, zone) | `Order/Services/OrderEditService.php` |
| Bulk order export (CSV) | `AdminOrderController@exportBulk` |
| Bulk order import (CSV) | `AdminOrderController@importBulk` |
| Courier assignment + status sync | `Courier/Controllers/AdminCourierController.php`, `Courier/Services/ShipmentService.php` |
| Pathao city/zone/area cascade | `AdminCourierController@pathaoCities/pathaoZones/pathaoAreas` |
| Transaction ledger | `Order/Controllers/AdminTransactionController.php` |
| Payment reconciliation | `AdminTransactionController@reconciliation` |
| Coupon CRUD + bulk generation | `Coupon/Controllers/AdminCouponController.php` |
| Shipping zone CRUD + drag reorder | `Shipping/Controllers/AdminShippingZoneController.php` |
| Customer CRUD + deactivate | `Customer/Controllers/AdminCustomerController.php` |
| Landing page CRUD | `Landing/Controllers/AdminLandingPageController.php` |
| Hero banner CRUD | `Store/Controllers/AdminHeroBannerController.php` |
| Role & permission management | `Auth/Controllers/AdminRoleController.php`, `AdminPermissionController.php` |
| Webhook CRUD + test fire | `Webhook/Controllers/AdminWebhookController.php` |
| Notification broadcast | `Notification/Controllers/AdminNotificationController.php` |
| Failed job retry | `AdminNotificationController@retryJob/retryAllFailed` |
| Activity log viewer | `Admin/Controllers/AdminActivityLogController.php` |
| System settings (cache clear, maintenance) | `Admin/Controllers/AdminSettingsController.php` |
| Settings model (typed, cached) | `Admin/Models/Setting.php` |

### Background / Async

| Feature | Key Files |
|---|---|
| Order confirmation email | `Listeners/SendOrderConfirmationEmail.php`, `Mail/OrderConfirmationMail.php` |
| Order status change email | `Listeners/SendOrderStatusEmail.php`, `Mail/OrderStatusMail.php` |
| Customer SMS on order | `Listeners/SendOrderSMSListener.php` → `Jobs/SendSMSJob.php` |
| Customer WhatsApp on order | `Listeners/SendOrderWhatsAppListener.php` → `Jobs/SendWhatsAppJob.php` |
| Admin new-order alert (SMS + email) | `Listeners/NotifyAdminOnNewOrder.php` |
| FCM push on status change | `Listeners/OrderStatusNotificationListener.php`, `Notifications/OrderStatusPushNotification.php` |
| Welcome email + coupon creation | `Jobs/SendWelcomeMailJob.php`, `Mail/WelcomeMail.php` |
| Outgoing webhooks (5 event types) | `Listeners/Dispatch*Webhook.php` → `Jobs/SendWebhookJob.php` → `Infrastructure/Webhook/WebhookService.php` |
| Meta CAPI + GA4 conversion events | `Jobs/SendConversionEvents.php` (triggered by `OrderObserver` on confirm, and by `CheckCodCancellations`) |
| Coupon auto-expiry | `Commands/ExpireCoupons.php` → `Events/CouponExpired` → `Listeners/DeactivateExpiredCoupons.php` |
| Cart stock release | `Commands/AbandonExpiredCarts.php` → `Cart/Services/CartService@releaseReservedStock` |
| Referral commission | `Listeners/CreateReferralCommissionListener.php`, `Marketing/Services/` |

---

## 6. Data Flow Walkthroughs

### 6.1 — Customer Places a COD Order (most common path)

```
Browser
  └─ POST /api/v1/checkout  (JSON, throttle:10,1)
        │
        ▼
  HandleCartSession middleware
    → reads/creates laladia_cart_token cookie
    → attaches cart_token to $request->attributes
        │
        ▼
  CheckoutController::store()        [Order/Controllers/CheckoutController.php]
    1. Auth::guard('web')->user()     ← auth resolved here, never in services
    2. CheckoutRequest::validate()    ← items auto-populated from cart if not sent
    3. Merges: ip_address, fbp, fbc, ga_client_id, user_agent, test_mode
    4. Calls OrderService::create($data, $cart, $user)
        │
        ▼
  OrderService::create()             [Order/Services/OrderService.php]
    1. Coupon check → throws if coupon + guest
    2. DB::transaction() {
         a. Idempotency: check checkout_token in orders table
         b. CartService::clearCart($cart)  ← release reserved stock
         c. CheckoutPricingService::calculate()  ← single pricing engine
              │
              ▼
          CheckoutPricingService::calculate()  [Order/Services/CheckoutPricingService.php]
            • lockForUpdate() on product_variants (pessimistic lock)
            • Stock validation (throws if insufficient)
            • PricingService::calculate() per line item
            • Tier pricing lookup
            • Gift variant injection (if tier has gift_variant_id)
            • Coupon validation via CouponValidationService
            • Shipping cost from ShippingZone
            • Returns: CheckoutPricingResult DTO
              │
         d. Order::create() with all computed financials
         e. OrderAddress::create()  ← snapshot of delivery address
         f. OrderItem::create() for each line item  ← product name/sku snapshots
         g. CouponUsage::create() if coupon applied
         h. OrderCreated::dispatch($order)  ← fires after commit
       }
    5. Returns $order
        │
        ▼
  Back in CheckoutController:
    • session()->put('pending_purchase_event', [...])  ← for browser-side GTM
    • resolveRedirectUrl($order) → /order-success/{number}
        │
  ── async (queue worker) ──────────────────────────────────────────
  OrderCreated event triggers (all queued, $afterCommit = true):
    • SendOrderConfirmationEmail   → SMTP via noreply mailer
    • SendOrderSMSListener         → SendSMSJob → GreenWeb API
    • SendOrderWhatsAppListener    → SendWhatsAppJob → WhatsApp Business API
    • NotifyAdminOnNewOrder        → SMS + email to admin
    • DispatchOrderCreatedWebhook  → SendWebhookJob → HMAC-signed HTTP POST
    • CreateReferralCommissionListener (if referral code used)
  ─────────────────────────────────────────────────────────────────
        │
        ▼
  Browser → GET /order-success/{order_number}
    • Pulls pending_purchase_event from session (auto-deleted)
    • Injects into Blade for browser-side GTM dataLayer push
```

### 6.2 — Admin Confirms an Order (triggers conversion tracking)

```
Admin Browser
  └─ PATCH /api/v1/admin/orders/{id}/status  { status: 'confirmed' }
        │
        ▼
  AdminOrderController::updateStatus()   [Order/Controllers/AdminOrderController.php]
    → OrderStatusService::transition($order, 'confirmed')
        │
        ▼
  OrderStatusService::transition()       [Order/Services/OrderStatusService.php]
    1. Validates transition is legal (enum guard)
    2. $order->update(['order_status' => 'confirmed', 'confirmed_at' => now()])
    3. OrderStatusChanged::dispatch($order, $old, $new)  ← triggers listeners
        │
  ── async ──────────────────────────────────────────────────────────
    • SendOrderStatusEmail
    • OrderStatusNotificationListener → FCM push
    • DispatchOrderStatusChangedWebhook
  ───────────────────────────────────────────────────────────────────
        │
        ▼
  OrderObserver::updated()              [Order/Observers/OrderObserver.php]
    • wasChanged('order_status') AND new status == 'confirmed'
    • updateQuietly(['approved_at' => now()])
    • SendConversionEvents::dispatch($order)->delay(5 seconds)
        │
        ▼  (5 seconds later, in queue)
  SendConversionEvents::handle()        [Jobs/SendConversionEvents.php]
    • $order->fresh() — guard: skip if conversion_fired = true
    • shouldSkip() — skip test_mode, private IPs, excluded IPs
    • sendToMeta() — POST to graph.facebook.com CAPI
    • sendToGA4()  — POST to GA4 Measurement Protocol
    • $order->update(['conversion_fired' => true])
```

### 6.3 — Landing Page Checkout (direct-to-order, no cart)

```
Landing page embed form
  └─ POST /api/v1/landing/{slug}/checkout
        │
        ▼
  LandingCheckoutController::checkout()   [Landing/Controllers/LandingCheckoutController.php]
    1. LandingPage::where('slug',...)->where('is_active', true)->firstOrFail()
    2. hasEmbeddedCheckout() guard
    3. Validate inline (no FormRequest — validates directly in controller)
    4. LandingCheckoutService::checkout(...)
        │
        ▼
  LandingCheckoutService::checkout()    [Landing/Services/LandingCheckoutService.php]
    • Resolves items from landing page type (product/combo/sales/listing)
    • DB::transaction → CheckoutPricingService → Order::create → same flow as above
    • Fires OrderCreated event
```

---

## 7. Database Schema

### Core Tables (34 total)

| Table | Purpose | Key Columns |
|---|---|---|
| `users` | Customers + admin staff | `phone`, `referral_code`, `referred_by`, `is_active`, `is_guest` |
| `products` | Product catalog | `slug` (unique), `category_id`, `is_active`, `is_trending`, `landing_slug` |
| `product_variants` | SKUs per product | `sku` (unique), `price`, `final_price`, `stock`, `is_active` |
| `product_tier_prices` | Volume discount tiers | `variant_id`, `min_quantity`, `discount_type`, `discount_value`, `gift_variant_id`, `free_delivery_zones` |
| `combos` | Product bundles | `slug` (unique), `pricing_mode` (auto/manual), `discount_type`, `is_active` |
| `combo_items` | Items in each combo | `combo_id`, `variant_id`, `quantity` |
| `product_relations` | Upsell/cross-sell links | `product_id`, `related_product_id`, `relation_type` |
| `categories` | Product categories | `slug` (unique), `sort_order`, `is_active` |
| `orders` | Order header | `order_number` (unique), `checkout_token` (unique), `order_status` (enum), `payment_status`, `grand_total` |
| `order_items` | Line items (snapshot) | `product_name_snapshot`, `sku_snapshot`, `variant_title_snapshot`, `unit_price`, `quantity` |
| `order_addresses` | Delivery address | Snapshot at order time — survives address changes |
| `order_transactions` | Payment ledger | `type` (charge/refund/commission), `amount`, `gateway_ref` |
| `order_notes` | Admin internal notes | `order_id`, `admin_id`, `note` |
| `carts` | Active/locked carts | `cart_id` (token), `user_id`, `status`, `expires_at`, `locked_at` |
| `cart_items` | Items in cart | Unique: `[cart_id, variant_id]` or `[cart_id, combo_id]` |
| `coupons` | Discount codes | `code` (unique), `type` (percentage/fixed), `usage_limit`, `limit_per_user` |
| `coupon_usages` | Usage tracking | `coupon_id`, `user_id`, `order_id` |
| `shipping_zones` | Delivery zones + rates | `name`, `cost`, `min_free_delivery_amount`, `sort_order`, `is_active` |
| `courier_shipments` | Courier consignments | `courier`, `consignment_id`, `tracking_code`, `status`, `status_synced_at` |
| `landing_pages` | Custom landing pages | `slug` (unique), `type` (product/combo/sales/listing), `product_id`, `combo_id` |
| `landing_page_items` | Items on listing pages | `landing_page_id`, `variant_id`/`combo_id`, `quantity`, `sort_order` |
| `webhooks` | Outgoing webhook config | `event`, `url`, `secret` (for HMAC), `is_active` |
| `settings` | Admin-editable config | `group`, `key`, `type`, `value`, `is_readonly` |
| `hero_banners` | Homepage banners | `image`, `link_url`, `sort_order`, `is_active` |
| `certifications` | Product certifications | `name`, `icon`, `category`, `is_active` |
| `device_tokens` | FCM push tokens | `user_id`, `token`, `platform` |
| `commissions` | Referral commissions | `order_id`, `referrer_id`, `amount`, `status` |
| `activity_log` | Spatie audit trail | All admin model changes |
| `notifications` | Laravel DB notifications | For admin broadcast |
| `jobs` / `failed_jobs` | Queue tables | Standard Laravel |
| `cache` / `cache_locks` | DB cache store | Standard Laravel |
| `sessions` | DB session store | Standard Laravel |
| `personal_access_tokens` | Sanctum tokens | Standard Laravel |

### Order Status Lifecycle

```
pending → confirmed → processing → shipped → delivered
                                           ↘ cancelled
                                             returned
```

Defined in `app/Domains/Order/Enums/OrderStatus.php`. Transitions are guarded in `OrderStatusService`.

### Key Indexes (performance migrations)

| Table | Index | Purpose |
|---|---|---|
| `products` | `[is_active, is_trending]`, `[is_active, category_id]`, `[is_active, created_at]` | Storefront listing queries |
| `product_variants` | `[product_id, is_active]`, `[is_active, price]` | Variant filtering + price sort |
| `orders` | `[order_status, customer_phone]`, `payment_status`, `[placed_at, delivered_at]` | Admin filtering, transaction queries |
| `landing_pages` | `[slug, is_active]` | Public landing page lookups |
| `categories` | `[is_active, sort_order]` | Ordered category nav |
| `cart_items` | `[cart_id, variant_id]` | Cart item lookups |

---

## 8. Authentication & Authorization

### Two Auth Contexts

| Context | Guard | Session |
|---|---|---|
| Storefront | `auth:sanctum` (stateful) | PHP session via `StartSession` |
| Admin panel | `auth:sanctum` + `admin` middleware | PHP session |
| Mobile/API | `auth:sanctum` (token-based) | Bearer token |

The same Sanctum guard handles both stateful (cookie-based) and token-based requests transparently. `bootstrap/app.php` calls `$middleware->statefulApi()` to wire this up.

### Roles (defined in `database/seeders/RoleSeeder.php`)

| Role | Capabilities |
|---|---|
| `Super Admin` | All 40 permissions |
| `Admin` | All 40 permissions |
| `Order Manager` | Orders, shipping view, customer view, notifications |
| `Inventory Clerk` | Products + categories only |
| `Marketing` | Coupons, hero banners, landing pages, analytics |
| `Customer Support` | Order view, customer management, notifications |
| `Customer` | `product.view` only (storefront role) |

### Permission Granularity

Every admin API route is gated by `->middleware('permission:X.Y')`. The permission list is in `RoleSeeder.php`. Permissions follow `resource.action` naming: `product.view`, `order.update`, `system.webhooks`, etc.

### `EnsureUserIsAdmin` middleware (`app/Http/Middleware/EnsureUserIsAdmin.php`)

Blocks any user whose highest role is `Customer` from accessing `/admin/*`. Role check is the coarse gate; permission check is the fine gate on individual routes.

---

## 9. Event System & Async Processing

### Event → Listener Wiring

Laravel auto-discovers listeners via `bootstrap/app.php`:

```php
->withEvents(discover: [
    app_path('Listeners'),
    app_path('Domains'),
])
```

Any listener class with a `handle(EventClass $event)` method is automatically wired. No manual `$listen` array required.

### Event Reference

| Event | Fired by | Queued Listeners |
|---|---|---|
| `OrderCreated` | `OrderService::create()` | Confirmation email, SMS, WhatsApp, admin notify, webhook, referral commission |
| `OrderStatusChanged` | `OrderStatusService::transition()` | Status email, FCM push, webhook |
| `OrderPaymentUpdated` | `AdminTransactionController` | Webhook |
| `ShipmentStatusUpdated` | `ShipmentService::syncStatus()` | Webhook |
| `CustomerRegistered` | `AuthController::register()` | Webhook, `SendWelcomeMailJob` |
| `CouponExpired` | `ExpireCoupons` command | `DeactivateExpiredCoupons` (sync), `DispatchCouponExpiredWebhook` (queued) |

### Job Retry Policy (standard for all queued listeners/jobs)

| Property | Value |
|---|---|
| `$tries` | 3 |
| `$backoff` | `[10, 30, 60]` seconds |
| `$timeout` | 15–30 seconds per job type |
| `$afterCommit` | `true` on all listeners (fire only after DB commit) |
| `failed()` | Logs to `Log::error` on permanent failure |

### Scheduled Commands (`routes/console.php`)

| Command | Schedule | Purpose |
|---|---|---|
| `coupons:expire` | Daily midnight | Fires `CouponExpired` for each overdue coupon |
| `app:abandon-expired-carts` | Hourly | Releases reserved stock from expired carts |
| `orders:check-cod-cancellations` | Hourly | Dispatches `SendConversionEvents` for approved COD orders ≥48h old |
| `queue:work --stop-when-empty` | Every minute (withoutOverlapping 5 min) | Processes queued jobs — safe for shared hosting |

> **Shared hosting note:** The `queue:work --stop-when-empty` approach avoids a persistent worker process. On a VPS/cloud with Redis, replace this with a `supervisor`-managed `queue:work --queue=default,emails` daemon.

---

## 10. External Integrations

### Configuration Files

| Integration | Config File | Key ENV Vars |
|---|---|---|
| SMTP (transactional email) | `config/mail.php` | `NOREPLY_MAIL_*`, `MAIL_*` |
| SMS (GreenWeb) | `config/sms.php` | `SMS_URL`, `SMS_TOKEN`, `SMS_SENDER` |
| WhatsApp Business | `config/whatsapp.php` | `WHATSAPP_URL`, `WHATSAPP_TOKEN`, `WHATSAPP_PHONE_NUMBER_ID` |
| Firebase (FCM) | `config/firebase.php` | `FIREBASE_SERVER_KEY` |
| Meta Pixel / CAPI | `config/tracking.php` | `META_PIXEL_ID`, `META_ACCESS_TOKEN` |
| Google Analytics 4 | `config/tracking.php` | `GA4_MEASUREMENT_ID`, `GA4_API_SECRET` |
| Pathao courier | `config/courier.php` | `PATHAO_CLIENT_ID`, `PATHAO_CLIENT_SECRET`, `PATHAO_STORE_ID` |
| Steadfast courier | `config/courier.php` | `STEADFAST_API_KEY`, `STEADFAST_SECRET_KEY` |
| RedX courier | `config/courier.php` | `REDX_API_KEY` |
| CarryBee courier | `config/courier.php` | `CARRYBEE_API_KEY` |
| SSLCommerz (stub) | — | `SSLCOMMERZ_*` (not yet wired) |

### Courier Driver Pattern

All couriers implement `Infrastructure/Courier/CourierInterface.php`. The active driver is resolved by `CourierService` based on `config('courier.default')` (set via `COURIER_DRIVER` env). To add a new courier: create a driver in `Infrastructure/Courier/Drivers/`, implement the interface, add to `config/courier.php`, register in `CourierService`.

### Outgoing Webhooks

Webhooks are stored in the `webhooks` table (managed via the admin UI). On any supported event, `DispatchOrderCreatedWebhook` (et al.) dispatch `SendWebhookJob`, which calls `WebhookService::dispatch()`. Each HTTP call is signed with `X-laladia-Signature: HMAC-SHA256(json_payload, secret)`.

Supported webhook events: `order.created`, `order.status_changed`, `order.payment_updated`, `shipment.status_updated`, `customer.registered`, `coupon.expired`.

---

## 11. Frontend Architecture

### Two Entry Points (Vite)

```
resources/js/app.js      → storefront CSS + JS
resources/js/admin.js    → admin panel JS
resources/css/app.css    → Tailwind v4 (design tokens defined here)
```

### Storefront JS Modules

| File | Purpose |
|---|---|
| `bootstrap.js` | Axios + CSRF setup |
| `api/client.js` | Fetch wrapper with CSRF header injection |
| `api/cart.js`, `auth.js`, `coupon.js`, `order.js`, `product.js` | Domain API modules |
| `cart/CartManager.js` | Cart state + API calls |
| `cart/CartRenderer.js` | DOM updates for cart icon + count |
| `cart/AddToCartBinder.js` | Binds add-to-cart buttons to CartManager |
| `cart/CartPageRenderer.js` | Renders the `/cart` page items |
| `managers/CheckoutManager.js` | Checkout form: zone change, coupon apply, pricing preview |
| `managers/ValidationManager.js` | Admin form validation helper |
| `search-suggestion.js` | Live product search dropdown |
| `filter/catalogFilter.js` | Category/price filter on `/products` |
| `flash.js` | Wires `window.flash()` to toast notification |

### Admin Panel Alpine.js Pattern

Every admin page is wrapped in `x-data="componentName()"`. The component is defined in an inline `<script>` at the bottom of each Blade view (or `_combo_form_script.blade.php` which is `@include`'d). Components call the JSON API (`/api/v1/admin/*`) and update reactive state.

Example pattern:
```html
<div x-data="orderManager()" x-init="init()">
   <!-- template uses x-for, x-text, x-show -->
</div>
@push('scripts')
<script>
function orderManager() {
    return {
        orders: [],
        async init() { await this.fetchOrders(); },
        async fetchOrders() { ... fetch('/api/v1/admin/orders') ... }
    };
}
</script>
@endpush
```

### Design System

The design token system is documented in `design_system_chart.md`. Key tokens:
- Colors: `gold-antique`, `gold-warm`, `ivory`, `cream`, `champagne`, `brown`, `taupe`, `muted`
- These map to CSS variables in `resources/css/app.css` and are available as Tailwind utility classes.

---

## 12. Setup & Deployment

### Prerequisites

- PHP 8.2+, Composer 2+
- Node.js 20+, npm 10+
- MySQL 8 (production) or SQLite (dev/test)
- Redis (production — sessions, queue, cache)

### Local Development

```bash
# 1. Clone + install
git clone <repo>
cd LaLaDia
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database
# Edit .env: DB_CONNECTION=sqlite (default) or point to MySQL
touch database/database.sqlite   # SQLite only
php artisan migrate
php artisan db:seed              # Seeds roles, demo products, zones, coupons

# 4. Storage symlink
php artisan storage:link

# 5. Run everything at once (uses concurrently)
composer run dev
# Starts: PHP server, queue worker, Pail log viewer, Vite dev server
```

### Accessing the Admin Panel

After seeding, create the first super admin:

```bash
php artisan tinker
>>> $user = App\Models\User::create([
...     'name' => 'Admin',
...     'email' => 'admin@example.com',
...     'phone' => '01700000000',
...     'password' => bcrypt('password'),
...     'is_active' => true,
... ]);
>>> $user->assignRole('Super Admin');
```

Then visit `http://localhost:8000/admin/login`.

### Production Deployment

```bash
# 1. Install production dependencies only
composer install --no-dev --optimize-autoloader

# 2. Build frontend assets
npm ci && npm run build

# 3. Environment (critical settings)
# APP_ENV=production
# APP_DEBUG=false
# LOG_LEVEL=error
# SESSION_DRIVER=redis
# QUEUE_CONNECTION=redis
# CACHE_STORE=redis
# SESSION_ENCRYPT=true

# 4. Run migrations
php artisan migrate --force

# 5. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Queue worker (via Supervisor — example config)
# [program:laladia-worker]
# command=php /var/www/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
# numprocs=2
# autostart=true
# autorestart=true

# 7. Cron (single entry — Laravel scheduler)
# * * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1
```

### Key ENV Variables Checklist

```bash
# Required for production
APP_KEY=                        # php artisan key:generate
APP_URL=https://yourdomain.com
DB_HOST=, DB_DATABASE=, DB_USERNAME=, DB_PASSWORD=

REDIS_HOST=, REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=, MAIL_PORT=, MAIL_USERNAME=, MAIL_PASSWORD=
NOREPLY_MAIL_FROM_ADDRESS=
NOREPLY_MAIL_FROM_NAME=
NOREPLY_MAIL_HOST=, NOREPLY_MAIL_PORT=, NOREPLY_MAIL_USERNAME=, NOREPLY_MAIL_PASSWORD=

ADMIN_PHONE=
ADMIN_EMAIL=

# Courier (choose one as default)
COURIER_DRIVER=pathao
PATHAO_CLIENT_ID=, PATHAO_CLIENT_SECRET=, PATHAO_USERNAME=, PATHAO_PASSWORD=, PATHAO_STORE_ID=

# Conversion tracking
META_PIXEL_ID=, META_ACCESS_TOKEN=
GA4_MEASUREMENT_ID=, GA4_API_SECRET=

# Optional
SMS_TOKEN=, SMS_URL=, SMS_SENDER=
WHATSAPP_TOKEN=, WHATSAPP_PHONE_NUMBER_ID=
FIREBASE_SERVER_KEY=
```

---

## 13. Key Conventions & Patterns

### The Controller → Service → Event pattern

```
Controller:   resolve auth, validate input, call service, return response
Service:      own business logic, DB::transaction(), fire events
Event:        thin data bag — just carries the model
Listener:     handle one side-effect, implement ShouldQueue
Job:          one external HTTP call, with retries
```

**Services must never call `Auth::`** — the controller resolves the user and passes it in. This makes services unit-testable and side-effect-free.

### `ApiResponse` helper

All JSON responses use three static methods:

```php
ApiResponse::success($data, 'Message', 201);
ApiResponse::error('Message', $errors, 422);
ApiResponse::paginated(ResourceCollection::collection($paginator));
```

The paginated format always returns `{ success, data: [...], meta: { current_page, last_page, ... } }`.

### `CheckoutPricingResult` DTO

`app/Domains/Order/DTOs/CheckoutPricingResult.php` — immutable, readonly PHP 8 properties. Used as the single return type from `CheckoutPricingService::calculate()`. Both the preview API and order creation use the same pricing engine with the `withLock` flag toggled.

### Model Observers (registered in `AppServiceProvider`)

| Observer | Trigger |
|---|---|
| `ProductObserver` | Slug auto-generation, cache invalidation |
| `ProductVariantObserver` | Slug/SKU generation |
| `CategoryObserver` | Cache invalidation on change |
| `ComboObserver` | Slug auto-generation |
| `LandingPageObserver` | Sync product/combo landing_slug |
| `HeroBannerObserver` | Cache invalidation |
| `ShippingZoneObserver` | Cache invalidation |
| `OrderObserver` | Dispatch `SendConversionEvents` on confirm |

### Pricing Engine Rules (in priority order)

1. `final_price` on `ProductVariant` (pre-applied discount override)
2. Tier price lookup by `min_quantity` (highest tier that qualifies wins)
3. Gift variant injection if tier has `gift_variant_id` and stock exists
4. Coupon applied to subtotal after tier discounts
5. Shipping cost from `ShippingZone` (free if `grand_total >= min_free_delivery_amount`)

---

## 14. Future Roadmap & Technical Debt

### 🔴 Not Implemented — Blocking for Full Deployment

| Item | Location | What's Missing |
|---|---|---|
| **SSLCommerz payment gateway** | `Order/Controllers/CheckoutController.php:149` | `resolveRedirectUrl()` has a complete stub with all required fields commented out. Install `karim007/laravel-sslcommerz`, uncomment the stub, add success/fail/cancel routes, and implement `SslCommerzNotification::makePayment()`. Public `CheckoutRequest` only allows `cod` — uncomment line 55 to enable `sslcommerz` once the gateway is wired. |
| **SSLCommerz callback routes** | `routes/web.php` | No success/fail/cancel routes exist. These must validate the SSLCOMMERZ IPN signature and update `payment_status` via `OrderPaymentUpdated` event. |

### 🟠 Partially Implemented

| Item | Location | Gap |
|---|---|---|
| **Automatic courier status sync** | `Courier/Services/ShipmentService.php` | `syncStatus()` exists and works, but it's only called manually via the admin UI. Add a scheduled command (`orders:sync-shipments`) to poll active shipments hourly, especially for `in_transit` and `out_for_delivery` statuses. |
| **Referral commission payout** | `Marketing/Services/`, `Listeners/CreateReferralCommissionListener.php`, `commissions` table | Commission records are created but there is no payout workflow — no admin UI for approving commissions, no payment processing, no balance tracking for the referrer. The `commissions` table has a `status` column but no state machine. |
| **Customer wallet / store credit** | — | The referral system implies credits but there is no wallet model, no balance column on `users`, and no checkout integration for store credit. |
| **Product reviews / ratings** | — | No `reviews` table, no review model, no UI. Referenced in `design_system_chart.md` but not implemented. |
| **COD confirmation email** | `Mail/OrderConfirmationMail.php` | The mail template exists but `order-confirmation.blade.php` is a generic template. Consider making it richer with product thumbnails and an order summary table. |

### 🟡 Technical Debt — Should Be Addressed

| Item | Location | Recommendation |
|---|---|---|
| **Test coverage is minimal** | `tests/` | 5 test files exist; `PricingServiceTest.php` is a stub class with no assertions. The checkout flow (pricing engine, coupon validation, stock reservation) is the highest-risk area and should be the first test target. Use SQLite in-memory for speed. |
| **`app/Core/BaseService.php` is empty** | `app/Core/BaseService.php` | Either delete it or add shared service helpers (logging, error mapping). Leaving empty classes signals unfinished architecture. |
| **Inline Alpine.js in Blade views** | `resources/views/admin/*/` | Each admin page has its full Alpine component in a `@push('scripts')` block. As components grow, extract them to `resources/js/admin/` modules and import via `admin.js`. This enables proper linting, testing, and code splitting. |
| **No CSP nonces** | `Http/Middleware/SecureHeaders.php` | The current CSP uses `'unsafe-inline'` to support Alpine.js and inline `<script>` blocks. Migrate to a nonce-based CSP when inline scripts are extracted. `SecureHeaders.php` has a comment indicating this is a known debt. |
| **Database-backed queue/cache/sessions** | `config/queue.php`, `config/cache.php`, `config/session.php` | The `.env.example` defaults to `database` for all three. Switched to Redis is documented as production-required in `docs/phase-11-performance.md`. No code change needed — purely operational. |
| **`combo_name_snapshot` missing on `order_items`** | `database/migrations/2026_02_27_153938_create_order_items_table.php` | `SendConversionEvents` and the checkout session payload use `$item->combo_name_snapshot ?? $item->product_name_snapshot` but `combo_name_snapshot` is not a column. GA4 `item_name` is `null` for combo order items. Add the column and populate it in `OrderService` at order creation time. |
| **`IntelligenceService` / recommendation engine** | `app/Domains/Intelligence/Services/` | Recommendations exist but are based on product relations (manually curated). There is no algorithmic engine. Consider order co-occurrence analysis once order volume is sufficient. |
| **No `order.csv` import validation** | `AdminOrderController@importBulk` | Bulk CSV import has basic validation but no dry-run preview before committing. The `importTemplate` route provides a blank template but no row-level error reporting on upload. |
| **`queue:work` via scheduler (shared hosting workaround)** | `routes/console.php:38` | Running `queue:work --stop-when-empty` every minute via the scheduler is a pragmatic approach for shared hosting. On a VPS, replace with a persistent Supervisor-managed worker — this avoids the ~60-second delay between job dispatch and execution at low traffic. |
| **Settings model bypasses Eloquent casting on write** | `Admin/Models/Setting.php@set()` | `Setting::set()` stores all values as `(string) $value`. Reading back a `boolean` setting uses `filter_var()`. This is correct but fragile — if a developer saves a boolean `true` it becomes the string `'1'`, and `false` becomes `''` (empty string). Document this or add explicit boolean serialization. |
| **No integration tests for event listeners** | `tests/` | All 12 queued listeners fire asynchronously. There are no tests asserting that placing an order dispatches the correct jobs or that the jobs call the correct external services. Add `Queue::fake()` + `Event::fake()` assertions for the checkout flow. |

### 🟢 Future Features (not yet started)

| Feature | Notes |
|---|---|
| **WhatsApp order templates** | Currently sends plain text. WhatsApp Business API supports structured message templates for order confirmation — higher delivery rate and branded formatting. |
| **Product bundles (dynamic)** | Combos are static (fixed items). A future "build your own bundle" feature would allow customers to choose variants within a bundle structure. |
| **Subscription / recurring orders** | No subscription model exists. Would require: a `subscriptions` table, payment tokenization, and a scheduler for renewal order creation. |
| **Multi-currency / i18n** | All prices are hard-coded BDT. Currency is passed as a literal `'BDT'` string in Meta CAPI and GA4 payloads. |
| **Inventory restocking alerts** | No low-stock threshold exists. A `reorder_point` column on `product_variants` and a daily check command would enable purchase order triggers. |
| **Admin mobile app / PWA** | The admin panel is Blade-rendered — not a SPA. A future dedicated mobile app could consume the existing admin JSON API directly. |
| **Advanced analytics dashboard** | `DashboardStatsService` provides basic KPIs. An analytics domain with cohort analysis, retention, and LTV calculations would require denormalized reporting tables or a data warehouse integration. |

---

*End of Developer Guide — last updated 2026-05-04 following 12-phase production audit.*
