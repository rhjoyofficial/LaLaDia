# Phase 5 — Checkout & Orders (Backend)

**Date:** 2026-05-04  
**Auditor:** Claude (Senior Engineer Review)  
**Status:** ✅ Complete — all issues fixed

---

## Scope

| Area | Files Reviewed |
|---|---|
| Controllers | `CheckoutController`, `OrderController`, `OrderTrackingController` |
| Services | `OrderService`, `CheckoutPricingService`, `OrderEditService`, `OrderStatusService`, `AdminOrderCreationService` |
| DTOs | `CheckoutPricingResult` |
| Requests | `CheckoutRequest`, `CheckoutPreviewRequest` |
| Resources | `OrderResource` |
| Models | `Order`, `OrderItem`, `OrderAddress`, `OrderNote`, `OrderTransaction` |
| Enums | `OrderStatus` |

---

## Findings & Fixes

### 🔴 CRITICAL — Order idempotency breaks when tier gifts are active

**File:** `app/Domains/Order/Services/OrderService.php` — `isSameCheckoutAttempt()`

**Problem:** The idempotency guard prevents duplicate orders on retry by comparing the existing order's items against the incoming request items. However, the comparison included **auto-gift line items** from the existing order (those with `discount_type_snapshot = 'Free Gift'`), while the incoming payload only contains user-submitted items (gifts are injected by the pricing engine, not the user).

**Result:** With any active gift tier, `isSameCheckoutAttempt()` always returned `false` (item count mismatch), so the guard never triggered. Every checkout retry when a gift tier was active created a **new duplicate order** instead of returning the existing one. Under network issues or browser-back behaviour, a customer could place 2–3 identical orders.

**Fix applied:** Filter out gift line items before the comparison:
```php
$existingItems = $existing->items
    ->filter(fn($item) => $item->discount_type_snapshot !== 'Free Gift')
    ->map(fn($item) => [...])
```

---

### 🟠 HIGH — `CheckoutRequest` / `CheckoutPreviewRequest` allow inactive variants/combos

**Files:** `app/Domains/Order/Requests/CheckoutRequest.php`, `CheckoutPreviewRequest.php`

**Problem:** Both requests validated `items.*.variant_id` with `exists:product_variants,id` and `items.*.combo_id` with `exists:combos,id` — no check on `is_active`. A deactivated variant or combo could be submitted in the checkout payload and would pass validation, proceed to the pricing engine, and complete as an order.

This is the same class of issue fixed in Phase 4 for the cart add endpoints. The cart add fix prevents inactive items from entering the cart, but a direct API call to `/checkout` bypasses the cart entirely.

**Fix applied:** Both requests now use `Rule::exists(...)->where('is_active', true)`:
```php
'items.*.variant_id' => ['nullable', Rule::exists('product_variants', 'id')->where('is_active', true)],
'items.*.combo_id'   => ['nullable', Rule::exists('combos', 'id')->where('is_active', true)],
```

---

### 🟠 HIGH — `OrderStatusService` N+1 queries on status change with combos

**File:** `app/Domains/Order/Services/OrderStatusService.php`

**Problem:** `changeStatus()` re-fetches the order with `lockForUpdate()` but loads no relations. Then `fulfillStock()` and `releaseStock()` iterate `$order->items`, accessing `$item->combo` and `$item->combo->items` — each a separate lazy load. For an order with 3 combo items, this results in:
- 1 query for `items`
- 3 queries for each `$item->combo`
- 3 queries for each `$item->combo->items`
= **7+ queries** instead of 2

This fires on every `Shipped` or `Cancelled` status change. Under load or bulk operations, this degrades performance significantly.

**Fix applied:** Eager-load the full combo chain before calling inventory methods:
```php
if ($newStatus === OrderStatus::Shipped || $newStatus === OrderStatus::Cancelled) {
    $order->load(['items.combo.items']);
}
```

---

### 🟡 MEDIUM — `ga_client_id` never collected at checkout

**File:** `app/Domains/Order/Controllers/CheckoutController.php`

**Problem:** The `orders.ga_client_id` column exists, is in `Order::$fillable`, and is used by the GA4 conversion tracking job (`SendConversionEvents`). However, `CheckoutController::store()` collected `fbp`, `fbc`, `ip_address`, `user_agent` but not `ga_client_id`. As a result, every order had a null `ga_client_id`, and the GA4 Measurement Protocol conversion events sent without a client ID — meaning GA4 couldn't deduplicate server-side events against client-side events, causing double-counting.

**Fix applied:** Collect from the request payload first (frontend can send it explicitly), falling back to the `_ga` cookie:
```php
'ga_client_id' => $request->input('ga_client_id') ?? $request->cookie('_ga'),
```
Also added `'ga_client_id' => 'nullable|string|max:100'` to `CheckoutRequest::rules()` so the frontend can send it as a validated field.

---

## Observations (No Fix Required)

### ✅ Pricing engine is the single source of truth — CORRECT
`CheckoutPricingService::calculate()` is used by all three entry points: checkout (with locks), preview (without locks), and cart totals (no zone/coupon). No separate pricing logic exists anywhere. All totals on the `orders` record come from the pricing engine output, not from client-submitted values.

### ✅ Coupon usage is atomic — CORRECT
`recordCouponUsage()` uses a conditional `increment('used_count')` with a `WHERE used_count < usage_limit` clause. Two concurrent requests using the same coupon at the limit boundary will only one succeed — the other gets zero `$affected` rows and throws an exception. The transaction wraps both the increment and the `CouponUsage` insert.

### ✅ Guest coupon gate — CORRECT
The coupon-requires-auth check throws **before** the DB transaction opens. This avoids acquiring variant locks for a request that will fail anyway. The inner `recordCouponUsage` has a redundant guard as a safety net — correct defensive design.

### ✅ Order idempotency (checkout_token) — NOW CORRECT (after fix)
The `checkout_token` UUID is generated in `CheckoutRequest::prepareForValidation()` for guests, or expected from the authenticated user's frontend state. The guard checks token → compares meta + items → returns existing or rotates token on collision.

### ✅ Stock reservation order — CORRECT
In `OrderService::create`:
1. `clearCart()` releases cart reserved stock
2. `pricingService->calculate(withLock=true)` re-validates and locks variants
3. Order + items created
4. Stock re-reserved from locked variants

This ensures the transition from "cart reserved" to "order reserved" is atomic. No window where stock is double-reserved or un-reserved.

### ✅ `OrderEditService::applyEdit` gift stock reservation — CORRECT
Step 6b explicitly reserves stock for auto-gift line items separately from user-submitted items. Gift variants are not in `$newItems`, so without 6b their stock would never be reserved after an edit.

### ✅ `OrderStatusService` state machine — CORRECT
Valid transitions are explicitly defined. The map prevents invalid jumps (e.g., `delivered → cancelled`). The lock-then-re-read pattern prevents race conditions where two admins change status simultaneously.

### ✅ `fulfillStock` uses `min()` floor guards — CORRECT
```php
$variant->decrement('stock', min($variant->stock, $qty));
$variant->decrement('reserved_stock', min($variant->reserved_stock, $qty));
```
Prevents stock or reserved_stock going negative under any race condition.

### ✅ `CheckoutController::preview` uses `withLock: false` — CORRECT
Preview calls run in a read transaction without acquiring row locks. This allows multiple concurrent preview requests (zone change, coupon apply) without blocking each other or the checkout path.

### ✅ `OrderResource` `is_editable` — CORRECT
`canAdminEdit()` is evaluated per-request using the loaded `shipments` relation if available, falling back to a DB query. This means the admin panel always shows the current edit-ability state without stale data.

### ✅ SSLCommerz integration stub is clearly marked — INFO
The `resolveRedirectUrl` method has a large commented stub for SSLCommerz. The current `payment_method` validation in `CheckoutRequest` is intentionally set to `'required|in:cod'` (SSLCommerz is commented out). COD-only is a safe production state. The stub is clean and self-documenting.

### ℹ️ Cart is cleared before pricing on checkout
If `CheckoutPricingService` throws (e.g., stock just ran out), the user's cart has already been cleared by `clearCart()`. The user would need to rebuild their cart. This is standard e-commerce behaviour (prevents double-reservation) but is worth noting for customer support awareness.

---

## Production Checklist (Phase 5 Actions)

| Item | Action |
|---|---|
| Frontend: send `ga_client_id` at checkout | Extract from `ga.getAll()[0].get('clientId')` and send in payload |
| Frontend: send `ga_client_id` in `CheckoutManager.js` | Add to the POST body alongside `fbp`/`fbc` |
| SSLCommerz integration | Implement `resolveRedirectUrl` stub when payment gateway is live |
| Monitor `skipped_gifts` in checkout response | Surface warnings to customer when gift stock is exhausted |

---

## Summary

| Severity | Found | Fixed |
|---|---|---|
| 🔴 Critical | 1 | 1 |
| 🟠 High | 2 | 2 |
| 🟡 Medium | 1 | 1 |
| ✅ Pass | 10 | — |
| ℹ️ Info | 1 | — |

**All issues resolved. Checkout and order system is production-grade.**

---

*Next: Phase 6 — Order Management Admin*
