# Phase 6 — Order Management Admin (Backend)

**Date:** 2026-05-04  
**Auditor:** Claude (Senior Engineer Review)  
**Status:** ✅ Complete — all issues fixed

---

## Scope

| Area | Files Reviewed |
|---|---|
| Controllers | `AdminOrderController`, `AdminTransactionController` |
| Services | `AdminOrderCreationService` |
| Requests | `UpdateOrderStatusRequest`, `StoreTransactionRequest`, `UpdatePaymentStatusRequest` |
| Resources | `OrderResource`, `TransactionResource` |

---

## Findings & Fixes

### 🔴 CRITICAL — Admin order creation never reserves gift stock

**File:** `app/Domains/Order/Services/AdminOrderCreationService.php` — `create()`

**Problem:** Step 5 of `create()` only iterates `$data['items']` (user-submitted items) when reserving stock. Auto-gift line items injected by the pricing engine live in `$pricing->lineItems` with `discount_type_snapshot = 'Free Gift'` and are never in `$data['items']`. As a result, every gift-tier admin order left gift variants' `reserved_stock` unreserved.

This is the exact same class of bug fixed in `OrderService` during Phase 5. `AdminOrderCreationService` was missed because it has its own create path.

**Consequence:** Gift stock appeared available to other buyers even after being committed to an admin order. Under concurrent orders, the same gift unit could be promised to multiple customers.

**Fix applied:** Added step 5b to iterate `$pricing->lineItems` filtering for gift items:
```php
foreach ($pricing->lineItems as $lineItem) {
    if (($lineItem['discount_type_snapshot'] ?? null) === 'Free Gift') {
        $giftVariant = $pricing->lockedVariants->get($lineItem['variant_id']);
        if ($giftVariant) {
            $giftVariant->increment('reserved_stock', $lineItem['quantity']);
        }
    }
}
```

---

### 🟠 HIGH — `store()` allows inactive variants/combos in admin orders

**File:** `app/Domains/Order/Controllers/AdminOrderController.php` — `store()`

**Problem:** Item validation used bare `exists:product_variants,id` and `exists:combos,id` with no `is_active` check. An admin could create an order referencing a deactivated variant or combo — it would pass validation, go to the pricing engine, and create an order for an item no longer meant to be sold.

This is the same class of issue fixed in CartController (Phase 4), CheckoutRequest and CheckoutPreviewRequest (Phase 5).

**Fix applied:** Changed to `Rule::exists()->where('is_active', true)`:
```php
'items.*.variant_id' => ['nullable', 'integer', Rule::exists('product_variants', 'id')->where('is_active', true)],
'items.*.combo_id'   => ['nullable', 'integer', Rule::exists('combos', 'id')->where('is_active', true)],
```

---

### 🟠 HIGH — `importBulk` N+1 SKU query

**File:** `app/Domains/Order/Controllers/AdminOrderController.php` — `importBulk()`

**Problem:** Inside the CSV `while (fgetcsv)` loop, for each row:
```php
$variant = ProductVariant::where('sku', $sku)->first();
```
One SELECT per CSV row. A 500-row import fires 500 queries just for SKU resolution. On top of that, `zone_id` values from the CSV were cast to `(int)` but never validated against the database — an invalid zone ID would fail only when the pricing engine ran, producing a confusing error message and wasting a full transaction attempt per bad row.

**Fix applied:** Two-pass approach:
1. **Pass 1** — collect all rows and all SKUs without touching the database.
2. **Batch queries** — one `whereIn('sku', ...)` for all unique SKUs, one `whereIn('id', ...)` for all zone IDs. Both keyed for O(1) lookup.
3. **Pass 2** — build groups from in-memory maps. Zero per-row queries.

SKU lookup now also filters `is_active = true` so deactivated variants are silently excluded from the import (same policy as the regular checkout endpoints).

---

### 🟡 MEDIUM — `shippingZones()` returns inactive zones

**File:** `app/Domains/Order/Controllers/AdminOrderController.php` — `shippingZones()`

**Problem:** The dropdown endpoint fetched all zones with no `is_active` filter. Admins could select a deactivated zone from the create/edit form dropdowns, then be confused when the pricing engine rejected it or applied unexpected shipping costs.

**Fix applied:**
```php
$zones = ShippingZone::where('is_active', true)
    ->orderBy('sort_order')
    ->get(['id', 'name', 'base_charge', 'free_shipping_threshold']);
```

---

### 🟡 MEDIUM — `AdminTransactionController::summary` runs 7+ separate scalar queries

**File:** `app/Domains/Order/Controllers/AdminTransactionController.php` — `summary()`

**Problem:** The dashboard summary fired 7 independent `SELECT SUM/COUNT` queries against the `orders` table:
- `totalRevenue` — full table paid sum
- `todayRevenue` — today's paid sum
- `weekRevenue` — this week's paid sum
- `monthRevenue` — this month's paid sum
- `unpaidCount` — count
- `unpaidTotal` — sum
- `failedCount` — count

All 7 hit the same table with different `WHERE` clauses. Under load or large datasets, this is 7 sequential full (or index) scans.

**Fix applied:** Replaced with a single `selectRaw` pass using conditional aggregates:
```sql
SELECT
  SUM(CASE WHEN payment_status = 'paid' THEN grand_total ELSE 0 END)                                  AS total_revenue,
  SUM(CASE WHEN payment_status = 'paid' AND DATE(placed_at) = CURDATE() THEN grand_total ELSE 0 END)  AS today_revenue,
  SUM(CASE WHEN payment_status = 'paid' AND placed_at >= ?  THEN grand_total ELSE 0 END)              AS week_revenue,
  SUM(CASE WHEN payment_status = 'paid' AND placed_at >= ?  THEN grand_total ELSE 0 END)              AS month_revenue,
  SUM(CASE WHEN payment_status = 'unpaid' THEN grand_total ELSE 0 END)                                AS unpaid_total,
  SUM(CASE WHEN payment_status = 'unpaid' THEN 1 ELSE 0 END)                                          AS unpaid_count,
  SUM(CASE WHEN payment_status = 'failed'  THEN 1 ELSE 0 END)                                         AS failed_count
```
One query replaces seven.

---

### 🟢 LOW — Redundant `method_exists` guard in `searchProducts`

**File:** `app/Domains/Order/Controllers/AdminOrderController.php` — `searchProducts()`

**Problem:** The catch block contained:
```php
if (method_exists(ApiResponse::class, 'error')) {
    return ApiResponse::error(...);
}
return response()->json(['success' => false, ...], 500);
```
`ApiResponse::error` has existed since day one of the project and the fallback `response()->json` returns a different response shape than every other endpoint. The guard was copy-paste defensive code that adds no value and creates inconsistency.

**Fix applied:** Removed the guard. Catch block now calls `ApiResponse::error` directly with the correct signature.

---

## Observations (No Fix Required)

### ✅ `AdminOrderCreationService` — coupon restriction correctly waived — CORRECT
Admin creates bypass the `auth-required-for-coupon` check that guards the customer checkout flow. The service doc-block calls this out. The coupon atomic increment guard still protects usage limits.

### ✅ `importBulk` wraps each group in its own transaction — CORRECT
Each phone group's order creation runs in its own `DB::transaction`. A failure for one customer does not roll back other groups. Failed rows are returned in the `errors` array.

### ✅ `OrderEditService::applyEdit` called from `applyEdit()` controller — CORRECT
The controller delegates fully to `OrderEditService`. No business logic in the controller. Edit permission check (`canAdminEdit()`) is handled inside `OrderEditService::applyEdit`.

### ✅ `updatePaymentStatus` fires `OrderPaymentUpdated` event — CORRECT
Both the controller-level `updatePaymentStatus` and the transaction controller version fire the event, ensuring downstream jobs (GA4, Meta CAPI) are notified of payment changes.

### ✅ `reconciliation` uses a single join query — CORRECT
`getDiscrepancyCount()` uses a single LEFT JOIN between `orders` and `order_transactions` rather than per-order PHP iteration.

### ✅ `summary` daily chart fills zero-revenue dates — CORRECT
The PHP loop from 29 days ago to today ensures the chart never has gaps even on days with no paid orders.

### ✅ `TransactionResource` exposes `metadata` — INFO
The `metadata` JSON column is fully exposed in `TransactionResource`. This is intentional for the admin ledger but should not be surfaced in any customer-facing API response.

---

## Production Checklist (Phase 6 Actions)

| Item | Action |
|---|---|
| Add index on `orders.payment_status, placed_at` | Optimize the conditional aggregate query for large tables |
| Add index on `orders.order_status` | Used by status filter in `index()` |
| Import template: document zone IDs | Add a second sheet or header row explaining zone_id values |

---

## Summary

| Severity | Found | Fixed |
|---|---|---|
| 🔴 Critical | 1 | 1 |
| 🟠 High | 2 | 2 |
| 🟡 Medium | 2 | 2 |
| 🟢 Low | 1 | 1 |
| ✅ Pass | 6 | — |

**All issues resolved. Admin order management is production-grade.**

---

*Next: Phase 7 — Marketing & Intelligence (Landing pages, Coupons, GTM/Meta tracking)*
