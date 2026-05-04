# Project Features & Logic Flows

**Author:** Senior Backend Architect Review  
**Date:** 2026-05-04  
**Application:** LaLaDia — Laravel 12 / PHP 8.2 DDD E-Commerce Platform

---

## Table of Contents

1. [Feature Inventory](#1-feature-inventory)
   - 1.1 Storefront — Public Browsing
   - 1.2 Cart System
   - 1.3 Checkout & Order Placement
   - 1.4 Customer Account
   - 1.5 Coupon & Discount System
   - 1.6 Landing Page Direct Checkout
   - 1.7 Authentication (Storefront & API)
   - 1.8 Admin — Product Management
   - 1.9 Admin — Combo Management
   - 1.10 Admin — Order Management
   - 1.11 Admin — Shipping Zone Management
   - 1.12 Admin — Coupon Management
   - 1.13 Admin — Customer Management
   - 1.14 Admin — Landing Page Builder
   - 1.15 Admin — Hero Banner Management
   - 1.16 Admin — Courier & Shipment Management
   - 1.17 Admin — Transaction Reconciliation
   - 1.18 Admin — Webhook Management
   - 1.19 Admin — Access Control (Roles & Permissions)
   - 1.20 Admin — Notifications & Queue Health
   - 1.21 Admin — Settings & System Health
   - 1.22 Async Event Pipeline (Post-Order)
   - 1.23 Scheduled Commands

2. [Deep Dive: Cart-to-Order Process Flow](#2-deep-dive-cart-to-order-process-flow)
   - Step 0 — Session Bootstrap
   - Step 1 — Add Item to Cart
   - Step 2 — View Cart
   - Step 3 — Checkout Preview (Pricing)
   - Step 4 — Submit Checkout
   - Step 5 — Order Creation Transaction
   - Step 6 — Post-Order Async Pipeline
   - Step 7 — Order Success Page

---

## 1. Feature Inventory

### Mapping Legend

| Column | Meaning |
|---|---|
| **Frontend** | Blade view or JS module that initiates the interaction |
| **API Route** | HTTP verb + path (all API routes prefixed `/api/v1`) |
| **Controller** | Entry-point controller method |
| **Service / Logic** | Domain service(s) that execute the business logic |

---

### 1.1 Storefront — Public Browsing

| Feature | Frontend | API Route / Web Route | Controller | Service |
|---|---|---|---|---|
| Homepage | `resources/views/store/home.blade.php` | `GET /` | `HomeController::index` | — |
| Product Catalogue | `resources/views/store/catalog.blade.php` | `GET /products` · `GET /api/v1/products` | `CatalogController::index` · `PublicProductController::index` | `ProductSearchService` |
| Category Filter | `resources/views/store/catalog.blade.php` | `GET /category/{slug}` · `GET /api/v1/categories` | `CatalogController::category` · `PublicCategoryController::index` | — |
| Product Detail Page | `resources/views/store/product.blade.php` | `GET /product/{slug}` · `GET /api/v1/products/{slug}` | `ProductPageController::show` · `PublicProductController::show` | — |
| Product Search | Alpine.js search dropdown | `GET /api/v1/products/search` | `ProductSearchController::search` | `ProductSearchService` |
| Product Recommendations | Product detail page | `GET /api/v1/products/{id}/recommendations` | `ProductRecommendationController::show` | — |
| Combo Pages | `resources/views/store/combos.blade.php` | `GET /combos` · `GET /combos/{slug}` | `ComboPageController::index` / `show` | — |
| Landing Pages | `resources/views/store/landing/*.blade.php` | `GET /product-page/{slug}` · `GET /page/{slug}` · `GET /api/v1/landing/{slug}` | `LandingPageController::show` · `ProductLandingController::show` | — |
| Informational Pages | Blade views | `GET /about`, `/contact`, `/faq`, `/privacy-policy`, `/terms`, `/blog`, `/gallery` | `PageController` | — |

---

### 1.2 Cart System

| Feature | Frontend | API Route | Controller | Service |
|---|---|---|---|---|
| View Cart | `resources/views/store/cart.blade.php` + Alpine.js `CartManager` | `GET /api/v1/cart` | `CartController::view` | `CartService::syncCartPrices`, `CartPricingService::calculate` |
| Add Product Variant | Add-to-cart button | `POST /api/v1/cart/add` | `CartController::add` | `CartService::addItem`, `PricingService::calculate` |
| Add Combo Bundle | Combo product page | `POST /api/v1/cart/add-combo` | `CartController::addCombo` | `CartService::addCombo` |
| Update Item Quantity | Cart page stepper | `POST /api/v1/cart/update` | `CartController::update` | `CartService::updateItemQuantity`, `PricingService::calculate` |
| Remove Item | Cart page remove button | `POST /api/v1/cart/remove` | `CartController::remove` | `CartService::removeItem` |
| Clear Cart | Cart page clear all | `DELETE /api/v1/cart/clear` | `CartController::clear` | `CartService::clearCart` |

**Session mechanism:** `HandleCartSession` middleware generates / reads a `bionic_cart_token` cookie (30-day expiry). The token is stored in the `carts.session_token` column for guest carts. Authenticated users are identified by `user_id` instead.

---

### 1.3 Checkout & Order Placement

| Feature | Frontend | API Route | Controller | Service |
|---|---|---|---|---|
| Checkout Page | `resources/views/store/checkout.blade.php` | `GET /checkout` | `CheckoutController::index` | — |
| Pricing Preview | Inline JS on checkout page | `POST /api/v1/checkout/preview` | `CheckoutController::preview` | `CheckoutPricingService::calculate` |
| Place Order | Checkout form submit | `POST /checkout` (web) · `POST /api/v1/checkout` (API) | `CheckoutController::store` | `OrderService::create`, `CheckoutPricingService::calculate`, `CartService::clearCart` |
| Validate Coupon | Checkout coupon input | `POST /api/v1/coupon/validate` | `PublicCouponController::validateCoupon` | `CouponValidationService::validate` |
| Shipping Zones | Checkout zone dropdown | `GET /api/v1/shipping-zones` | `PublicShippingZoneController::index` | — |
| Order Success | Redirected after checkout | `GET /order-success/{order}` | Inline route closure | — |
| Order Failed | Redirected on error | `GET /order-failed` | `OrderController::failed` | — |

---

### 1.4 Customer Account

| Feature | Frontend | Web Route | Controller | Service |
|---|---|---|---|---|
| Dashboard | `resources/views/customer/dashboard.blade.php` | `GET /account/dashboard` | `CustomerDashboard::index` | — |
| Order History | `resources/views/customer/orders.blade.php` | `GET /account/orders` | `CustomerDashboard::orders` | — |
| Order Detail | `resources/views/customer/order-details.blade.php` | `GET /account/orders/{order}` | `CustomerDashboard::orderDetails` | — |
| Profile | `resources/views/customer/profile.blade.php` | `GET /account/profile` | `CustomerDashboard::profile` | — |
| Generate Referral Code | Profile page | `POST /account/referral-code` | `CustomerDashboard::generateReferralCode` | — |

---

### 1.5 Coupon & Discount System

| Feature | Who Uses It | Logic Owner |
|---|---|---|
| Coupon validation | Checkout + Landing Checkout | `CouponValidationService::validate` → `Coupon::isValidForUser`, min_purchase check, `lockForUpdate` |
| Coupon usage recording | `OrderService::create` (inside transaction) | Atomic `Coupon::increment('used_count')` + `CouponUsage::create` |
| Tier-based pricing | Per-item in `CheckoutPricingService` | `PricingService::calculate` — matches `product_tier_prices` by `min_quantity ≤ qty` |
| Free shipping via tier | `CheckoutPricingService::processVariantItem` | If active tier has `has_free_delivery = true`, `ShippingCalculator` is bypassed |
| Auto-gift via tier | `CheckoutPricingService::processVariantItem` | Gift variant injected as `discount_type_snapshot = 'Free Gift'` line item |
| Coupon expiry (scheduled) | `ExpireCoupons` artisan command | Fires `CouponExpired` event → `DeactivateExpiredCoupons` listener sets `is_active = false` |
| Bulk coupon generation | Admin | `AdminCouponController::bulkGenerate` · `POST /api/v1/admin/coupons/bulk-generate` |

---

### 1.6 Landing Page Direct Checkout

| Feature | Frontend | API Route | Controller | Service |
|---|---|---|---|---|
| Landing Page Display | `resources/views/store/landing/` | `GET /page/{slug}` or `GET /product-page/{slug}` | `LandingPageController::show` | — |
| Landing Pricing Preview | Embedded checkout form | `POST /api/v1/landing/{slug}/preview` | `LandingCheckoutController::preview` | `LandingCheckoutService::preview` → `CheckoutPricingService::calculate` |
| Landing Order Placement | Embedded checkout form | `POST /api/v1/landing/{slug}/checkout` | `LandingCheckoutController::checkout` | `LandingCheckoutService::checkout` → `OrderService::create` |

**Note:** Landing page checkout bypasses the cart entirely. Items are submitted directly in the request body.

---

### 1.7 Authentication (Storefront & API)

| Feature | Channel | Route | Controller |
|---|---|---|---|
| Register (Web / Session) | Blade form | `POST /register` | `WebAuthController::register` |
| Login (Web / Session) | Blade form | `POST /login` | `WebAuthController::login` |
| Logout (Web) | Blade button | `POST /logout` | `WebAuthController::logout` |
| Register (API / Token) | JSON API | `POST /api/v1/register` | `AuthController::register` |
| Login (API / Token) | JSON API | `POST /api/v1/login` | `AuthController::login` |
| Logout (API) | JSON API | `POST /api/v1/logout` | `AuthController::logout` |
| Me (API) | JSON API | `GET /api/v1/me` | `AuthController::me` |
| Forgot Password | Blade form | `POST /api/v1/forgot-password` | `ForgotPasswordController::sendResetLink` |
| Reset Password | Blade form | `POST /api/v1/password/reset` | `ForgotPasswordController::reset` |
| Admin Login | Blade form | `POST /admin/login` | `AdminAuthController::login` |
| Admin Logout | Admin layout | `POST /admin/logout` | `AdminAuthController::logout` |

**Dual guard:** Web routes use `auth:sanctum` with stateful session. API routes use Sanctum token. All admin routes require `auth:sanctum` + `admin` middleware (which checks for a non-Customer role).

---

### 1.8 Admin — Product Management

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| List Products | `admin/products/index.blade.php` | `GET /api/v1/admin/products` | `AdminProductController::index` |
| Create Product | `admin/products/create.blade.php` | `POST /api/v1/admin/products` | `AdminProductController::store` |
| Edit Product | `admin/products/edit.blade.php` | `GET/PUT /api/v1/admin/products/{id}` | `AdminProductController::show` / `update` |
| Toggle Active | Inline toggle | `PATCH /api/v1/admin/products/{id}/toggle-active` | `AdminProductController::toggleActive` |
| Toggle Landing | Inline toggle | `PATCH /api/v1/admin/products/{id}/toggle-landing-status` | `AdminProductController::toggleLanding` |
| Delete Product | Modal confirm | `DELETE /api/v1/admin/products/{id}` | `AdminProductController::destroy` |
| Manage Tier Prices | Product edit form | `POST/DELETE /api/v1/admin/products/{variant}/tier-prices` | `ProductTierPriceController::store` / `destroy` |
| Product Relations (Upsells) | Product edit form | `POST/DELETE /api/v1/admin/product-relations` | `ProductRelationController::store` / `destroy` |
| Search Products | Admin order create | `GET /api/v1/admin/products/search` | `AdminProductController::searchProducts` |

---

### 1.9 Admin — Combo Management

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| List Combos | `admin/combos/index.blade.php` | `GET /api/v1/admin/combos` | `AdminComboController::index` |
| Create Combo | `admin/combos/create.blade.php` | `POST /api/v1/admin/combos` | `AdminComboController::store` |
| Edit Combo | `admin/combos/edit.blade.php` | `GET/PUT /api/v1/admin/combos/{id}` | `AdminComboController::show` / `update` |
| Toggle Active | Inline toggle | `PATCH /api/v1/admin/combos/{id}/toggle-active` | `AdminComboController::toggleActive` |
| Delete Combo | Modal confirm | `DELETE /api/v1/admin/combos/{id}` | `AdminComboController::destroy` |

---

### 1.10 Admin — Order Management

| Feature | Admin UI | API Route | Controller | Service |
|---|---|---|---|---|
| Order List | `admin/orders/index.blade.php` | `GET /api/v1/admin/orders` | `AdminOrderController::index` | — |
| Order Detail | `admin/orders/show.blade.php` | `GET /api/v1/admin/orders/{id}` | `AdminOrderController::show` | — |
| Create Order (Admin) | `admin/orders/create.blade.php` | `POST /api/v1/admin/orders` | `AdminOrderController::store` | `AdminOrderCreationService::create` |
| Update Status | Order detail | `PATCH /api/v1/admin/orders/{id}/status` | `AdminOrderController::updateStatus` | `OrderStatusService::changeStatus` |
| Update Payment Status | Order detail | `PATCH /api/v1/admin/orders/{id}/payment-status` | `AdminOrderController::updatePaymentStatus` | — |
| Add Note | Order detail | `POST /api/v1/admin/orders/{id}/notes` | `AdminOrderController::addNote` | — |
| Edit Order (Items/Address/Zone) | Order detail | `GET/POST/PUT /api/v1/admin/orders/{id}/edit-data`, `preview-edit`, no suffix | `AdminOrderController::editData` / `previewEdit` / `applyEdit` | `OrderEditService` |
| Bulk Export | Orders list | `GET /api/v1/admin/orders/export-bulk` | `AdminOrderController::exportBulk` | — |
| Bulk Import | Orders list | `POST /api/v1/admin/orders/import-bulk` | `AdminOrderController::importBulk` | — |

---

### 1.11 Admin — Shipping Zone Management

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| List Zones | `admin/shipping/index.blade.php` | `GET /api/v1/admin/shipping-zones` | `AdminShippingZoneController::index` |
| Create / Update / Delete | Inline modals | `POST/PUT/DELETE /api/v1/admin/shipping-zones` | `AdminShippingZoneController` |
| Reorder Zones | Drag-and-drop | `PATCH /api/v1/admin/shipping-zones/reorder` | `AdminShippingZoneController::reorder` |

---

### 1.12 Admin — Coupon Management

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| List Coupons | `admin/coupons/index.blade.php` | `GET /api/v1/admin/coupons` | `AdminCouponController::index` |
| Coupon Stats | Same page | `GET /api/v1/admin/coupons/stats` | `AdminCouponController::stats` |
| Create / Update / Delete | Modal on index | `POST/PUT/DELETE /api/v1/admin/coupons` | `AdminCouponController` |
| Bulk Generate Coupons | Same page | `POST /api/v1/admin/coupons/bulk-generate` | `AdminCouponController::bulkGenerate` |

---

### 1.13 Admin — Customer Management

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| List Customers | `admin/customers/index.blade.php` | `GET /api/v1/admin/customers` | `AdminCustomerController::index` |
| Customer Profile | `admin/customers/show.blade.php` | `GET /api/v1/admin/customers/{id}` | `AdminCustomerController::show` |
| Create / Update / Delete | Modals | `POST/PUT/DELETE /api/v1/admin/customers` | `AdminCustomerController` |
| Toggle Active | Inline | `PATCH /api/v1/admin/customers/{id}/toggle-active` | `AdminCustomerController::toggleActive` |
| Change Password | Modal | `PATCH /api/v1/admin/customers/{id}/change-password` | `AdminCustomerController::changePassword` |

---

### 1.14 Admin — Landing Page Builder

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| List Landing Pages | `admin/landing-pages/index.blade.php` | `GET /api/v1/admin/landing-pages` | `AdminLandingPageController::index` |
| Create / Edit | Dedicated create/edit views | `POST/PUT /api/v1/admin/landing-pages` | `AdminLandingPageController::store` / `update` |
| Toggle Active | Inline | `PATCH /api/v1/admin/landing-pages/{id}/toggle-active` | `AdminLandingPageController::toggleActive` |
| Delete | Modal | `DELETE /api/v1/admin/landing-pages/{id}` | `AdminLandingPageController::destroy` |

---

### 1.15 Admin — Hero Banner Management

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| List / Create / Edit | `admin/hero-banners/index.blade.php` | `GET/POST/PUT /api/v1/admin/hero-banners` | `AdminHeroBannerController` |
| Toggle Active | Inline | `PATCH /api/v1/admin/hero-banners/{id}/toggle-active` | `AdminHeroBannerController::toggleActive` |

---

### 1.16 Admin — Courier & Shipment Management

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| List Courier Drivers | Order detail | `GET /api/v1/admin/courier/drivers` | `AdminCourierController::drivers` |
| Assign Courier | Order detail | `POST /api/v1/admin/courier/assign` | `AdminCourierController::assign` |
| Bulk Assign | Orders list | `POST /api/v1/admin/courier/bulk-assign` | `AdminCourierController::bulkAssign` |
| View Shipments | Order detail | `GET /api/v1/admin/courier/shipments/{order}` | `AdminCourierController::orderShipments` |
| Sync Shipment Status | Order detail | `POST /api/v1/admin/courier/shipments/{shipment}/sync` | `AdminCourierController::syncStatus` |
| Cancel Shipment | Order detail | `POST /api/v1/admin/courier/shipments/{shipment}/cancel` | `AdminCourierController::cancel` |
| Pathao Location Cascade | Courier assign modal | `GET /api/v1/admin/courier/pathao/cities` + `zones/{city}` + `areas/{zone}` | `AdminCourierController` |

---

### 1.17 Admin — Transaction Reconciliation

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| Transaction List | `admin/transactions/index.blade.php` | `GET /api/v1/admin/transactions` | `AdminTransactionController::index` |
| Summary Stats | Same page | `GET /api/v1/admin/transactions/summary` | `AdminTransactionController::summary` |
| Reconciliation View | Dedicated tab | `GET /api/v1/admin/transactions/reconciliation` | `AdminTransactionController::reconciliation` |
| Per-Order Transactions | Order detail | `GET /api/v1/admin/transactions/order/{order}` | `AdminTransactionController::orderTransactions` |
| Add Transaction | Order detail | `POST /api/v1/admin/transactions/order/{order}` | `AdminTransactionController::store` |
| Update Payment Status | Order detail | `PATCH /api/v1/admin/transactions/order/{order}/payment-status` | `AdminTransactionController::updatePaymentStatus` |

---

### 1.18 Admin — Webhook Management

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| List / Create / Delete | `admin/webhooks/index.blade.php` | `GET/POST/DELETE /api/v1/admin/webhooks` | `AdminWebhookController` |
| Toggle Active | Inline | `PATCH /api/v1/admin/webhooks/{id}/toggle` | `AdminWebhookController::toggle` |
| Test Webhook | Button | `POST /api/v1/admin/webhooks/{id}/test` | `AdminWebhookController::test` |

Outgoing payloads are signed with HMAC-SHA256 (`X-Bionic-Signature` header). Supported event types: `order.created`, `order.status_changed`, `order.payment_updated`, `shipment.status_updated`, `coupon.expired`, `customer.registered`.

---

### 1.19 Admin — Access Control (Roles & Permissions)

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| Role List + Permission Matrix | `admin/access-control/index.blade.php` | `GET /api/v1/admin/access-control/roles` · `/matrix` | `AdminRoleController::index` / `matrix` |
| Create / Update / Delete Role | Modals | `POST/PUT/DELETE /api/v1/admin/access-control/roles` | `AdminRoleController` |
| Sync Permissions to Role | Matrix save | `PUT /api/v1/admin/access-control/roles/{id}/permissions` | `AdminRoleController::syncPermissions` |
| Manage Admin Staff | Same page | `GET/POST/PUT/DELETE /api/v1/admin/access-control/admin-users` | `AdminRoleController` |
| Assign Role to User | Same page | `PATCH /api/v1/admin/access-control/admin-users/{id}/role` | `AdminRoleController::assignRole` |
| Permission CRUD | Same page | `GET/POST/PUT/DELETE /api/v1/admin/access-control/permissions` | `AdminPermissionController` |

**6 built-in roles:** `SuperAdmin`, `Admin`, `Manager`, `Sales`, `Support`, `Customer`.  
**40 granular permissions** across: `product`, `category`, `order`, `coupon`, `shipping`, `customer`, `role`, `permission`, `staff`, `notification`, `hero`, `system`, `landing-pages`.

---

### 1.20 Admin — Notifications & Queue Health

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| Stats (sent / failed) | `admin/notifications/index.blade.php` | `GET /api/v1/admin/notifications/stats` | `AdminNotificationController::stats` |
| Failed Jobs List | Same page | `GET /api/v1/admin/notifications/failed-jobs` | `AdminNotificationController::failedJobs` |
| Retry Failed Job | Inline | `POST /api/v1/admin/notifications/failed-jobs/{uuid}/retry` | `AdminNotificationController::retryJob` |
| Retry All Failed | Bulk | `POST /api/v1/admin/notifications/failed-jobs/retry-all` | `AdminNotificationController::retryAllFailed` |
| Delete Failed Job | Inline | `DELETE /api/v1/admin/notifications/failed-jobs/{uuid}` | `AdminNotificationController::deleteFailedJob` |
| Send Notification | Modal | `POST /api/v1/admin/notifications/send` | `AdminNotificationController::send` |

---

### 1.21 Admin — Settings & System Health

| Feature | Admin UI | API Route | Controller |
|---|---|---|---|
| Read / Write Settings | `admin/settings/index.blade.php` | `GET/PUT /api/v1/admin/settings` | `AdminSettingsController::index` / `update` |
| System Health | Same page | `GET /api/v1/admin/settings/health` | `AdminSettingsController::health` |
| Maintenance Status | Same page | `GET /api/v1/admin/settings/maintenance-status` | `AdminSettingsController::maintenanceStatus` |
| Toggle Maintenance | Same page | `POST /api/v1/admin/settings/toggle-maintenance` | `AdminSettingsController::toggleMaintenance` |
| Clear Cache | Same page | `POST /api/v1/admin/settings/clear-cache` | `AdminSettingsController::clearCache` |
| Optimize App | Same page | `POST /api/v1/admin/settings/optimize` | `AdminSettingsController::optimizeApp` |

---

### 1.22 Async Event Pipeline (Post-Order)

Triggered by `OrderCreated` event (implements `ShouldDispatchAfterCommit` — only fires after DB commit):

| Listener | Queue? | Action |
|---|---|---|
| `SendOrderConfirmationEmail` | ✅ ShouldQueue (`$afterCommit = true`) | Sends HTML confirmation email via `noreply` mailer if `customer_email` is set |
| `DispatchOrderCreatedWebhook` | ✅ ShouldQueue (`$afterCommit = true`) | Dispatches `SendWebhookJob` for `order.created` to all active webhook endpoints |
| `NotifyAdminOnNewOrder` | ✅ ShouldQueue (`$afterCommit = true`) | Sends plain-text raw email to admin address |
| `SendOrderSMSListener` | ✅ ShouldQueue | Sends SMS confirmation to customer via configured SMS driver |
| `SendOrderWhatsAppListener` | ✅ ShouldQueue | Sends WhatsApp confirmation message |
| `CreateReferralCommissionListener` | ✅ ShouldQueue | Records referral commission if customer was referred |

Triggered by `OrderStatusChanged` event:

| Listener | Queue? | Action |
|---|---|---|
| `SendOrderStatusEmail` | ✅ ShouldQueue (`$afterCommit = true`) | Notifies customer of status change by email |
| `OrderStatusNotificationListener` | ✅ ShouldQueue (`$afterCommit = true`) | Sends FCM push notification to customer device |
| `DispatchOrderStatusChangedWebhook` | ✅ ShouldQueue (`$afterCommit = true`) | Fires `order.status_changed` webhook |
| `CreateCourierShipmentListener` | ✅ ShouldQueue | Automatically creates courier shipment when status → `processing` |

---

### 1.23 Scheduled Commands

| Command | Schedule | Purpose |
|---|---|---|
| `app:abandon-expired-carts` | Every hour | Releases `reserved_stock` for expired guest carts; marks them `abandoned` |
| `coupon:expire` | Daily | Fires `CouponExpired` event for each past-`expires_at` coupon → `DeactivateExpiredCoupons` listener |
| `orders:check-cod-cancellations` | Every hour | Dispatches `SendConversionEvents` job for COD orders approved ≥48h ago with `conversion_fired = false` |
| `queue:work --stop-when-empty` | Every minute (withoutOverlapping 5) | Drains the queue on shared hosting |

---

## 2. Deep Dive: Cart-to-Order Process Flow

This section traces the complete journey from a user clicking **"Add to Cart"** on a product page through to the **order success page**, covering every function call, database write, and API response along the way.

---

### Step 0 — Session Bootstrap (Middleware)

**Trigger:** Any HTTP request to a cart route (`/api/v1/cart/*` or `POST /checkout`)  
**Endpoint:** All routes in the `cart.session` middleware group  
**Middleware:** `App\Http\Middleware\HandleCartSession`

**Logic:**

```php
// HandleCartSession::handle()
$token = $request->cookie('bionic_cart_token')
    ?? $request->header('X-Session-Token')
    ?? (string) Str::uuid();   // generate new if none exists

$request->attributes->set('cart_token', $token);

// After response is built:
return $response->withCookie(
    cookie()->make('bionic_cart_token', $token, 43200, '/', null, $secure, false)
    // httpOnly=false — JS must read it to send as X-Session-Token on login
);
```

**Database:** No DB read/write at this stage. The token only maps to a `carts` row when `CartService::getCart()` is called.

**Response:** Token is stamped into `$request->attributes` and returned as a 30-day cookie.

---

### Step 1 — Add Item to Cart

**Trigger:** User clicks "Add to Cart" on a product page  
**Endpoint:** `POST /api/v1/cart/add`  
**Controller:** `CartController::add`

#### 1a. Request Validation

```php
$request->validate([
    'variant_id' => ['required', Rule::exists('product_variants', 'id')->where('is_active', true)],
    'quantity'   => 'required|integer|min:1',
]);
```

**Database READ:** `product_variants` — verifies `id` exists AND `is_active = 1`. Returns 422 if invalid.

#### 1b. Cart Resolution

```php
// CartController::resolveCart()
if (Auth::check()) {
    return CartService::getCart(Auth::id(), null);
}
$sessionToken = $request->attributes->get('cart_token');
return CartService::getCart(null, $sessionToken);
```

```php
// CartService::getCart()
// Authenticated user:
Cart::firstOrCreate(['user_id' => $userId, 'status' => 'active']);

// Guest:
Cart::firstOrCreate(
    ['session_token' => $token, 'user_id' => null, 'status' => 'active'],
    ['expires_at' => now()->addDays(7)]
);
```

**Database READ/WRITE:** `carts` — `SELECT` first; `INSERT` on first visit.

#### 1c. Add Item Transaction

```php
// CartService::addItem() — wraps everything in DB::transaction()
$variant = ProductVariant::lockForUpdate()->findOrFail($variantId);
// ↑ SELECT ... FOR UPDATE on product_variants row
```

**Stock Check:**
```php
if (!$variant->hasStock($qty)) {
    throw new Exception("Only {$variant->available_stock} left");
    // available_stock = stock - reserved_stock (computed attribute)
}
```

**Pricing Calculation:**
```php
$pricing = $this->pricingService->calculate($variant, $qty);
// PricingService checks product_tier_prices for min_quantity <= qty
// Returns: unit_price (after tier discount), discount_amount, discount_type
```

**Database READ:** `product_tier_prices` — finds best matching tier for quantity.

**Cart Item Upsert:**
```php
// If item already in cart:
$item->update([
    'quantity'              => $newQty,
    'unit_price_snapshot'   => $pricing['unit_price'],
    'product_name_snapshot' => $variant->product->name,
    'variant_title_snapshot'=> $variant->title,
]);
$variant->increment('reserved_stock', $qty);

// If new item:
$cart->items()->create([...]);
$variant->increment('reserved_stock', $qty);
```

**Database WRITES:**
- `cart_items` — `INSERT` or `UPDATE`
- `product_variants` — `UPDATE reserved_stock = reserved_stock + qty`

#### 1d. Response

```json
{
  "success": true,
  "message": "Item added",
  "data": {
    "items": [
      {
        "id": 42,
        "variant_id": 7,
        "combo_id": null,
        "quantity": 2,
        "product_name_snapshot": "Alphonso Mango",
        "variant_title_snapshot": "1 KG",
        "unit_price": 280.00,
        "original_unit_price": 320.00,
        "tier_saving": 40.00,
        "tiers": [{ "qty": 2, "type": "fixed", "value": 40, "free_delivery": false }],
        "subtotal": 560.00,
        "image_url": "https://..."
      }
    ],
    "totals": {
      "total_qty": 2,
      "subtotal": 640.00,
      "discount": 80.00,
      "total": 560.00,
      "auto_gifts": []
    },
    "cart_id": 15
  }
}
```

HTTP `201 Created`.

---

### Step 2 — View Cart

**Trigger:** User opens the cart drawer or navigates to `/cart`  
**Endpoint:** `GET /api/v1/cart`  
**Controller:** `CartController::view`

#### 2a. Price Sync

```php
// CartService::syncCartPrices()
// For every item in the cart, re-runs PricingService::calculate()
// and updates unit_price_snapshot if it has changed by > 0.001
DB::transaction(function () use ($cart, &$anyPriceChanged) {
    foreach ($cart->items as $item) {
        $newPrice = $pricingService->calculate($item->variant, $item->quantity)['unit_price'];
        if (abs($currentPrice - $newPrice) > 0.001) {
            $item->update(['unit_price_snapshot' => $newPrice]);
            $anyPriceChanged = true;
        }
    }
});
```

**Database READ:** `product_variants`, `product_tier_prices`  
**Database WRITE:** `cart_items` — only if price changed

#### 2b. Totals Calculation

`CartPricingService::calculate()` delegates to the single `CheckoutPricingService::calculate()` engine with `couponCode = null`, `zoneId = null`, `withLock = false`. This calculates subtotal, tier discounts, and auto-gifts without locking rows.

#### 2c. Response

```json
{
  "success": true,
  "message": "Cart loaded",
  "data": {
    "items": [ /* CartItemResource array */ ],
    "totals": {
      "total_qty": 3,
      "subtotal": 960.00,
      "discount": 120.00,
      "total": 840.00,
      "auto_gifts": [
        {
          "variant_id": 22,
          "quantity": 1,
          "product_name_snapshot": "Bonus Mango Sample",
          "variant_title_snapshot": "250g"
        }
      ]
    },
    "cart_id": 15,
    "prices_updated": false
  }
}
```

If `prices_updated: true`, the frontend displays a "Prices have been updated" banner.

---

### Step 3 — Checkout Preview (Pricing)

**Trigger:** User opens the checkout page, changes their shipping zone, or applies/removes a coupon  
**Endpoint:** `POST /api/v1/checkout/preview`  
**Controller:** `CheckoutController::preview`  
**Request:** `CheckoutPreviewRequest` (validated `items[]`, `zone_id`, `coupon_code`)

#### 3a. Pricing Engine Call

```php
$result = DB::transaction(fn() => $this->pricingService->calculate(
    items: $validated['items'],
    couponCode: $validated['coupon_code'] ?? null,
    zoneId: $validated['zone_id'] ?? null,
    user: Auth::guard('web')->user(),
    withLock: false,   // preview — no row-level locks needed
));
```

#### 3b. `CheckoutPricingService::calculate()` — Full Logic

```
1. loadVariants()
   ├─ Collect all variant_ids from items array
   ├─ For each combo_id: JOIN combo_items → collect component variant_ids
   ├─ For all variant_ids: JOIN product_tier_prices → collect gift_product_variant_ids
   └─ SELECT product_variants WITH product,tierPrices [FOR UPDATE if withLock]

2. For each item in $items:
   ├─ If combo_id → processComboItem()
   │   ├─ Combo::with('items')->findOrFail()
   │   ├─ Check each component variant hasStock(qty)
   │   └─ Returns line_item with combo.final_price × qty
   └─ If variant_id → processVariantItem()
       ├─ hasStock(qty) check
       ├─ PricingService::calculate(variant, qty, tierPrices)
       │   └─ Match tier: WHERE min_quantity <= qty ORDER BY min_quantity DESC
       │       ├─ percentage tier → discount = base_price × (value/100) × qty
       │       └─ fixed tier     → discount = value × qty
       ├─ Check for free_shipping_override from tier
       └─ Check for gift_product_variant_id on tier → collect auto_gifts

3. Inject auto-gift line items (discount_type_snapshot = 'Free Gift', unit_price = 0)
   └─ Skip if gift variant is out of stock → push to skippedGifts[]

4. Coupon (if couponCode present):
   ├─ CouponValidationService::validate()
   │   ├─ Coupon::where('code')->lockForUpdate()->first()
   │   ├─ coupon->isValidForUser($user) — checks is_active, expires_at, usage_limit
   │   ├─ min_purchase check vs discounted subtotal
   │   └─ calculateDiscount(): percentage → (amount × value/100), fixed → min(value, amount)
   └─ couponDiscount = result

5. Shipping:
   └─ ShippingCalculator::calculate(zone, discountedSubtotal)
       ├─ If zone.free_shipping_threshold && amount >= threshold → 0
       └─ Else → zone.base_charge

6. grandTotal = max(0, discountedSubtotal - couponDiscount) + shippingCost

7. Return CheckoutPricingResult (immutable DTO)
```

**Database READs:**
- `product_variants` + `product_tier_prices` + `products`
- `combos` + `combo_items` (if combos in cart)
- `coupons` (if coupon provided)
- `shipping_zones` (if zone provided)

#### 3c. Response

```json
{
  "success": true,
  "message": "Pricing calculated",
  "data": {
    "line_items": [
      {
        "variant_id": 7,
        "product_name_snapshot": "Alphonso Mango",
        "variant_title_snapshot": "1 KG",
        "quantity": 2,
        "original_unit_price": 320.00,
        "unit_price": 280.00,
        "total_price": 560.00,
        "discount_type_snapshot": "fixed",
        "discount_value_snapshot": 40
      }
    ],
    "subtotal": 640.00,
    "tier_discount": 80.00,
    "coupon_discount": 0.00,
    "coupon_code": null,
    "shipping_cost": 60.00,
    "grand_total": 620.00,
    "skipped_gifts": []
  }
}
```

---

### Step 4 — Submit Checkout

**Trigger:** User clicks "Place Order" on the checkout page  
**Endpoint:** `POST /checkout` (web form) or `POST /api/v1/checkout` (SPA/API)  
**Controller:** `CheckoutController::store`  
**Request:** `CheckoutRequest` (FormRequest)

#### 4a. `CheckoutRequest::prepareForValidation()`

```php
// Auto-populates items from cart if not submitted explicitly:
if (!$this->has('items') || count($items) === 0) {
    $cart = CartService::getCart(...);
    $this->merge(['items' => $cart->items->map(fn($i) => [
        'variant_id' => $i->variant_id,
        'combo_id'   => $i->combo_id,
        'quantity'   => $i->quantity,
    ])]);
}

// Auto-generates checkout_token for guests (idempotency key):
if (!$this->filled('checkout_token') && !Auth::check()) {
    $this->merge(['checkout_token' => (string) Str::uuid()]);
}
```

#### 4b. Validation Rules

```
customer_name    required|string|max:255
customer_phone   required|string|max:20
customer_email   nullable|email
address_line     required|string|max:500
city             required|string|max:100
zone_id          required|exists:shipping_zones,id
payment_method   required|in:cod         (sslcommerz disabled)
items            required|array|min:1
items.*.variant_id  nullable|exists:product_variants,id WHERE is_active=1
items.*.combo_id    nullable|exists:combos,id WHERE is_active=1
items.*.quantity    required|integer|min:1
coupon_code      nullable|string|max:50
checkout_token   required (guest) / nullable (auth) | min:32
```

**Custom after-validation rule:** Each item must have at least one of `variant_id` or `combo_id`.

**Database READ:** `shipping_zones` (zone_id exists check), `product_variants` (variant_id exists), `combos` (combo_id exists).

#### 4c. Tracking Data Merge

```php
$data = array_merge($request->validated(), [
    'ip_address'       => $request->ip(),
    'fbp'              => $request->cookie('_fbp'),      // Meta Pixel cookie
    'fbc'              => $request->cookie('_fbc'),      // Meta Click ID
    'ga_client_id'     => $request->input('ga_client_id') ?? $request->cookie('_ga'),
    'event_source_url' => $request->header('Referer'),
    'user_agent'       => $request->userAgent(),
    'test_mode'        => !app()->isProduction(),
]);
```

#### 4d. Cart Resolution

```php
private function resolveCheckoutCart(CheckoutRequest $request, ?User $authUser): ?Cart
{
    if ($authUser) return CartService::getCart($authUser->id, null);

    $token = $request->attributes->get('cart_token')
        ?? $request->header('X-Session-Token')
        ?? $request->cookie('bionic_cart_token')
        ?? $request->input('checkout_token');

    return $token ? CartService::getCart(null, $token) : null;
}
```

---

### Step 5 — Order Creation Transaction

**Service:** `OrderService::create(array $data, ?Cart $cart, ?User $user)`

All 9 sub-steps below execute inside a single `DB::transaction()`.

#### 5a. Guest Coupon Gate

```php
if (!empty($data['coupon_code']) && !$user) {
    throw new Exception('Please log in to apply a coupon code.');
}
// Checked BEFORE the transaction opens — no resources are locked for a doomed request
```

#### 5b. Idempotency Guard

```php
if (!empty($data['checkout_token'])) {
    $existing = Order::where('checkout_token', $data['checkout_token'])->latest('id')->first();
    if ($existing) {
        if ($this->isSameCheckoutAttempt($existing, $data)) {
            return $existing;   // network retry — return the same order silently
        }
        $data['checkout_token'] = (string) Str::uuid();  // different attempt — rotate token
    }
}
```

**Database READ:** `orders` — checks for existing `checkout_token`.

#### 5c. Clear Cart Reserved Stock

```php
if ($cart) {
    $this->cartService->clearCart($cart);
    // CartService::clearCart() acquires lockForUpdate on all variant rows,
    // then decrements reserved_stock for every cart item (regular + combo components),
    // then deletes all cart_items rows.
}
```

**Database WRITES:**
- `product_variants` — `UPDATE reserved_stock = reserved_stock - qty` (per item)
- `cart_items` — `DELETE WHERE cart_id = ?`

**Rationale:** The cart has pre-reserved stock. Releasing it here prevents double-reservation: the pricing engine will re-reserve the exact same quantity in Step 5g below.

#### 5d. Run Pricing Engine (Authoritative, With Locks)

```php
$pricing = $this->pricingService->calculate(
    items: $items,
    couponCode: $data['coupon_code'] ?? null,
    zoneId: $data['zone_id'],
    user: $user,
    withLock: true,    // ← SELECT ... FOR UPDATE on all variant rows
);
```

Same full pricing pipeline as Step 3b, but:
- `lockForUpdate: true` — acquires row-level exclusive locks on all `product_variants` rows
- `couponService->validate()` also calls `lockForUpdate` on the `coupons` row
- Re-validates stock at this exact moment under locks (protects against race conditions)

**Database READs (with row-level locks):**
- `product_variants` `FOR UPDATE`
- `coupons` `FOR UPDATE` (if coupon present)
- `product_tier_prices`, `products`, `shipping_zones`, `combo_items`

#### 5e. Create Order Record

```php
$order = Order::create([
    // ... all $data fields
    'user_id'              => $user?->id,
    'order_number'         => 'BNC-' . now()->format('Ymd') . '-' . strtoupper(Str::random(10)),
    'subtotal'             => $pricing->subtotal,
    'discount_total'       => $pricing->tierDiscountTotal + $pricing->couponDiscount,
    'shipping_cost'        => $pricing->shippingCost,
    'grand_total'          => $pricing->grandTotal,
    'coupon_id'            => $pricing->coupon?->id,
    'coupon_code_snapshot' => $pricing->coupon?->code,
    'coupon_discount'      => $pricing->couponDiscount,
    'payment_status'       => 'unpaid',
    'order_status'         => 'pending',
    'placed_at'            => now(),
]);
```

**Database WRITE:** `orders` — `INSERT`

#### 5f. Create Shipping Address

```php
$order->shippingAddress()->create([
    'type'           => 'shipping',
    'customer_name'  => $data['customer_name'],
    'customer_phone' => $data['customer_phone'],
    'address_line'   => $data['address_line'],
    'city'           => $data['city'],
]);
```

**Database WRITE:** `order_addresses` — `INSERT`

#### 5g. Create Order Items + Reserve Stock

```php
foreach ($pricing->lineItems as $lineItem) {
    $order->items()->create($lineItem);
    // lineItem includes: variant_id/combo_id, product_name_snapshot,
    // unit_price, original_unit_price, discount_type_snapshot, quantity, etc.
}

// Re-reserve stock for all items (including auto-gifts and combo components)
foreach ($pricing->lineItems as $lineItem) {
    if (!empty($lineItem['combo_id'])) {
        foreach ($combo->items as $comboItem) {
            $pricing->lockedVariants->get($comboItem->product_variant_id)
                ->increment('reserved_stock', $comboItem->quantity * $lineItem['quantity']);
        }
    } elseif (!empty($lineItem['variant_id'])) {
        $pricing->lockedVariants->get($lineItem['variant_id'])
            ->increment('reserved_stock', $lineItem['quantity']);
    }
}
```

**Database WRITES:**
- `order_items` — `INSERT` (one row per line item including gifts)
- `product_variants` — `UPDATE reserved_stock = reserved_stock + qty`

#### 5h. Record Coupon Usage

```php
if ($pricing->coupon) {
    // Atomic increment WITH limit check (race-condition safe):
    $affected = Coupon::where('id', $coupon->id)
        ->where(fn($q) => $q->whereNull('usage_limit')
                           ->orWhereColumn('used_count', '<', 'usage_limit'))
        ->increment('used_count');

    if (!$affected) throw new Exception('Coupon limit has been reached.');

    CouponUsage::create([
        'coupon_id'       => $coupon->id,
        'user_id'         => $user->id,
        'order_id'        => $order->id,
        'discount_amount' => $pricing->couponDiscount,
    ]);
}
```

**Database WRITES:**
- `coupons` — `UPDATE used_count = used_count + 1` (only if `used_count < usage_limit`)
- `coupon_usages` — `INSERT`

#### 5i. Dispatch `OrderCreated` Event

```php
$order->load('items');
event(new OrderCreated($order));
// OrderCreated implements ShouldDispatchAfterCommit
// → will not fire listeners until after DB::transaction() commits
```

The `DB::transaction()` now commits. All 9 sub-steps are atomic.

---

### Step 6 — Post-Order Async Pipeline

After the transaction commits, the `OrderCreated` event fires. The following listeners execute asynchronously via the queue worker:

```
OrderCreated event
│
├─ SendOrderConfirmationEmail (queued, $afterCommit=true)
│   └─ Mail::mailer('noreply')->to($order->customer_email)->send(OrderConfirmationMail)
│      Tables READ: orders, order_items, order_addresses
│
├─ DispatchOrderCreatedWebhook (queued, $afterCommit=true)
│   └─ SendWebhookJob::dispatch('order.created', [...payload...])
│       → Finds all active webhook_endpoints with event='order.created'
│       → HTTP POST with X-Bionic-Signature HMAC header
│
├─ NotifyAdminOnNewOrder (queued, $afterCommit=true)
│   └─ Mail::mailer('noreply')->raw($emailBody) → admin email
│
├─ SendOrderSMSListener (queued)
│   └─ SMS to customer_phone via configured SMS driver
│
├─ SendOrderWhatsAppListener (queued)
│   └─ WhatsApp message to customer_phone
│
└─ CreateReferralCommissionListener (queued)
    └─ If customer was referred: INSERT commissions record
```

---

### Step 7 — Order Success Page

**Back in `CheckoutController::store()`**, after `$order` is returned:

```php
// Store GA4 purchase event data in session for client-side dataLayer push
$request->session()->put('pending_purchase_event', [
    'transaction_id' => $order->order_number,
    'value'          => (float) $order->grand_total,
    'currency'       => 'BDT',
    'items'          => $order->items->map(fn($item) => [...])->toArray(),
]);

$redirectUrl = route('order.success', ['order' => $order->order_number]);

// Non-JSON (web form): redirect
return redirect()->to($redirectUrl);

// JSON (SPA/API): return order + redirect URL
return ApiResponse::success([
    ...(new OrderResource($order))->toArray($request),
    'redirect_url' => $redirectUrl,
], 'Order placed successfully', 201);
```

**Order Success Route** (`GET /order-success/{order}`):

```php
$order = Order::with(['items', 'shippingAddress'])
    ->where('order_number', $orderNumber)
    ->firstOrFail();

// session()->pull() — atomic read + delete (fires once only)
$purchaseEvent = session()->pull('pending_purchase_event');

return view('store.order-success', compact('order', 'purchaseEvent'));
```

The Blade view uses `$purchaseEvent` to push a GA4 `purchase` event into the `dataLayer` (client-side). The Meta Pixel `Purchase` event is fired via the server-side `SendConversionEvents` job (dispatched by `orders:check-cod-cancellations` after 48h approval for COD orders).

**Database READ:** `orders`, `order_items`, `order_addresses`

---

### Full Flow Summary

```
Browser                            Server                              Database
──────────────────────────────────────────────────────────────────────────────────
[GET page load]                 HandleCartSession middleware
                                → reads/creates bionic_cart_token cookie

[POST /api/v1/cart/add]
  variant_id=7, quantity=2  ──► CartController::add
                                  └─ CartService::addItem()
                                      ├─ SELECT product_variants FOR UPDATE       carts
                                      ├─ hasStock() check                         cart_items
                                      ├─ PricingService::calculate()              product_variants
                                      ├─ cart_items UPSERT                        product_tier_prices
                                      └─ reserved_stock += qty
                          ◄── { items[], totals{}, cart_id }  201

[GET /api/v1/cart]         ──► CartController::view
                                  └─ syncCartPrices() + CartPricingService
                          ◄── { items[], totals{}, prices_updated: false }  200

[POST /api/v1/checkout/preview]
  items[], zone_id, coupon  ──► CheckoutController::preview
                                  └─ CheckoutPricingService::calculate(withLock=false)
                                      ├─ SELECT variants, tiers, gifts
                                      ├─ CouponValidationService (no lock)
                                      └─ ShippingCalculator
                          ◄── { line_items[], subtotal, tier_discount,
                                 coupon_discount, shipping_cost, grand_total }  200

[POST /checkout]
  customer info + items[]   ──► CheckoutController::store
                                  ├─ CheckoutRequest::prepareForValidation()
                                  │    └─ auto-populate items from cart if empty   cart_items
                                  └─ OrderService::create()
                                      ├─ [GUARD] coupon requires auth
                                      ├─ Idempotency: check checkout_token        orders
                                      ├─ CartService::clearCart()                 cart_items
                                      │    └─ reserved_stock -= qty               product_variants
                                      ├─ CheckoutPricingService (withLock=true)
                                      │    ├─ SELECT variants FOR UPDATE          product_variants
                                      │    ├─ SELECT coupon FOR UPDATE            coupons
                                      │    ├─ Tier pricing + gift injection
                                      │    └─ ShippingCalculator                  shipping_zones
                                      ├─ INSERT order                             orders
                                      ├─ INSERT order_address                     order_addresses
                                      ├─ INSERT order_items (×N)                  order_items
                                      ├─ reserved_stock += qty (×N)               product_variants
                                      ├─ used_count++ + INSERT coupon_usage        coupons/coupon_usages
                                      └─ event(OrderCreated) → [COMMIT]
                          ◄── redirect /order-success/BNC-20260504-XXXX  302

[Async queue workers — after commit]
  SendOrderConfirmationEmail  → SMTP noreply mailer
  DispatchOrderCreatedWebhook → HTTP POST to webhook endpoints
  NotifyAdminOnNewOrder       → Admin email
  SendOrderSMSListener        → SMS to customer
  CreateReferralCommission    → commissions INSERT (if referred)

[GET /order-success/{order}]   ──► Inline route closure
                                    ├─ SELECT order with items + address          orders
                                    └─ session()->pull('pending_purchase_event')
                               ◄── Blade view renders
                                    └─ GA4 dataLayer.push({ event: 'purchase' })
```

---

*End of FLOWS.md*
