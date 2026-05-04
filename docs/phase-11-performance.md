# Phase 11 — Performance, Caching & Production Readiness

**Date:** 2026-05-04  
**Auditor:** Claude (Senior Engineer Review)  
**Status:** ✅ Complete — all issues fixed

---

## Scope

| Area | Files / Migrations Reviewed |
|---|---|
| Database indexes | `create_orders_table`, `create_order_items_table`, `add_performance_indexes`, `add_price_index_to_product_variants` |
| Cache strategy | `HomeController`, `CatalogController`, `ProductPageController`, `ComboPageController`, `ViewServiceProvider`, `Setting` model |
| Eager loading | All public and admin controllers |
| Rate limiting | `routes/web.php`, `routes/public.php` |
| Queue / session / cache config | `config/queue.php`, `config/cache.php`, `config/session.php` |
| Production config | `.env.example` |
| Dependencies | `composer.json` |
| Admin query patterns | `AdminTransactionController`, `AdminOrderController`, `DashboardStatsService` |

---

## Findings & Fixes

### 🟠 HIGH — Missing `payment_status` index on `orders` table

**Migration created:** `database/migrations/2026_05_04_130000_add_payment_status_index_to_orders_table.php`

**Problem:** `AdminTransactionController` runs at minimum six separate `WHERE payment_status = X` queries on the `orders` table:

1. Dashboard revenue stats — `CASE WHEN payment_status = 'paid' THEN ...` aggregate over all orders
2. Daily revenue chart (30-day window) — `WHERE payment_status = 'paid'`
3. Reconciliation listing — `WHERE (payment_status = 'paid' AND ...) OR (payment_status = 'unpaid' AND ...) OR payment_status = 'failed'`
4. Discrepancy count summary — same multi-branch WHERE
5. Reconciliation export — same pattern across all orders
6. Admin order index — `WHERE payment_status = ?` (optional filter)

The `orders` table has a composite index `[order_status, customer_phone]` and `[placed_at, delivered_at]` — neither covers `payment_status` queries. As order volume grows, every one of these queries is a full table scan.

**Fix applied:** New idempotent migration adding `orders_payment_status_index` on `payment_status`.

---

### 🟡 MEDIUM — `LOG_LEVEL=debug` in `.env.example` missing production warning

**File:** `.env.example` — line 8

**Problem:** `LOG_LEVEL=debug` is the default in the example file with no production comment. In production, debug-level logging writes every database query value, HTTP request detail, and full stack frame to the log channel — high disk usage and potential exposure of sensitive data (customer emails, phone numbers in query bindings) in log files.

**Fix applied:** Added production comment above `LOG_LEVEL`:

```
# PRODUCTION: Set to 'error' or 'warning'. 'debug' logs every query value and
# stack frame — high disk usage and leaks internal details to log files.
```

---

### 🟢 LOW — `SESSION_DRIVER`, `QUEUE_CONNECTION`, `CACHE_STORE` missing Redis upgrade guidance

**File:** `.env.example`

**Problem:** All three default to `database` with no comment explaining when to upgrade. `APP_DEBUG` and `SESSION_ENCRYPT` already had production comments; these three did not.

- `SESSION_DRIVER=database` — under concurrent load, Laravel writes to `sessions` table on every request, creating write contention.
- `QUEUE_CONNECTION=database` — multiple workers competing on the same `jobs` table under load causes row-level lock waits.
- `CACHE_STORE=database` — every `Cache::remember()` hit issues a SELECT; every cache write issues a UPSERT. At hundreds of requests/second, a Redis swap is 100× faster.

**Fix applied:** Added production-scope comments to all three entries in `.env.example`.

---

## Observations (No Fix Required)

### ✅ Eager loading — comprehensive across all controllers — CORRECT
Every controller audited uses `->with(...)` for all relationship accesses. No N+1 query patterns detected anywhere:
- `HomeController`: `variants.tierPrices`, `items.variant.product`
- `CatalogController`: `variants.tierPrices`, `category`
- `ProductPageController`: `category`, `variants.tierPrices`, `certifications`, `upsells`, `crossSells`
- `ComboPageController`: `items.variant.product`, `items.variant.tierPrices`
- `AdminOrderController`: `withCount('items')`, `with(['zone', 'user', 'shipments'])` on index; full nested load on show

### ✅ Cache strategy — correct TTLs and invalidation — CORRECT
- Hero banners / categories: 24-hour TTL, invalidated by `HeroBannerObserver` / `CategoryObserver`
- Trending / catalog products: 6-hour TTL
- Product and combo pages: 2-hour TTL
- Global category view composer: 24-hour TTL via `ViewServiceProvider`
- Settings: 1-hour TTL via `Setting::remember()`, bust on `set()`

Cache keys are consistent between `HomeController` (`categories:active`) and `ViewServiceProvider` (`categories:active`) — both read the same cached value. No stale-read risk.

### ✅ Existing performance indexes — comprehensive — CORRECT
`2026_04_19_073635_add_performance_indexes.php` covers all high-frequency public query patterns:
- `[is_active, is_trending]`, `[is_active, category_id]`, `[is_active, created_at]` on `products`
- `[product_id, is_active]` on `product_variants`
- `[slug, is_active]` on `landing_pages`
- `[is_active, sort_order]` on `categories` and `hero_banners`
- `[is_active, created_at]` on `combos`
- `[cart_id, variant_id]` on `cart_items`

`2026_04_26_040000_add_price_index_to_product_variants.php` adds `[is_active, price]` for price-range filtering. All migrations are idempotent.

### ✅ Rate limiting — all sensitive endpoints throttled — CORRECT
- Login (web + API): `throttle:10,1`
- Register (web + API): `throttle:5,1`
- Forgot password / reset: `throttle:3,1`
- Checkout: `throttle:10,1`
- Checkout preview: `throttle:30,1`
- Coupon validate: `throttle:20,1`
- Cart operations: `throttle:60,1`
- Landing page checkout: `throttle:20,1`
- Admin login: `throttle:5,1`

### ✅ Debugbar in `require-dev` — will not run in production — CORRECT
`fruitcake/laravel-debugbar` is in `require-dev`, not `require`. It is excluded from `composer install --no-dev` production builds. No debugbar config file is published. No risk of the toolbar appearing in production.

### ✅ `orders.order_status` — covered by composite prefix — CORRECT
`WHERE order_status = ?` queries benefit from the `[order_status, customer_phone]` composite index since `order_status` is the leftmost column. MySQL uses composite indexes for prefix lookups, so single-column `order_status` filters do use this index. No additional standalone index is needed.

### ✅ `orders.user_id` — indexed via FK constraint — CORRECT
`foreignId('user_id')->constrained()` in the orders migration emits a MySQL FK constraint which implicitly creates a B-tree index. Customer order history (`WHERE user_id = X ORDER BY placed_at DESC`) uses this index.

### ✅ `Model::all()` — not used in hot paths — CORRECT
Searched codebase. No `Model::all()` calls on large tables (`Order`, `OrderItem`, `Product`, `ProductVariant`). The two `->all()` calls found are on small in-memory collection chains, not Eloquent model fetches.

---

## Production Checklist (Phase 11 Actions)

| Item | Action |
|---|---|
| Run new migration | `php artisan migrate` — adds `orders_payment_status_index` |
| Switch to Redis before go-live | Set `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`, `CACHE_STORE=redis` in production `.env` |
| Set `LOG_LEVEL=error` | Change in production `.env` |
| Confirm `APP_DEBUG=false` | Already commented in `.env.example` — verify production `.env` |
| Confirm `SESSION_ENCRYPT=true` | Already commented in `.env.example` — verify production `.env` |
| Run `composer install --no-dev` | Excludes Debugbar from production build |
| Warm cache on first deploy | Hit the home page and category pages once after deploy to seed the 24-hour cache entries |

---

## Summary

| Severity | Found | Fixed |
|---|---|---|
| 🟠 High | 1 | 1 |
| 🟡 Medium | 1 | 1 |
| 🟢 Low | 1 | 1 |
| ✅ Pass | 9 | — |

**All actionable issues resolved. Performance foundation is production-grade. Remaining steps (Redis, LOG_LEVEL, SESSION_ENCRYPT) are deployment-time configuration, not code changes.**

---

*Next: Phase 12 — End-to-End QA & Gap Closure*
