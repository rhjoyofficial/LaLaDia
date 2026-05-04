# Phase 7 — Marketing & Intelligence

**Date:** 2026-05-04  
**Auditor:** Claude (Senior Engineer Review)  
**Status:** ✅ Complete — all issues fixed

---

## Scope

| Area | Files Reviewed |
|---|---|
| Landing Pages | `LandingPageController`, `AdminLandingPageController`, `LandingCheckoutController`, `LandingCheckoutService`, `LandingPage`, `LandingPageItem`, `LandingPageObserver`, `LandingPageResource` |
| Coupons | `AdminCouponController`, `PublicCouponController`, `CouponValidationService`, `Coupon`, `CouponResource`, `StoreCouponRequest`, `UpdateCouponRequest`, `BulkGenerateCouponRequest` |
| Marketing | `GTMEventService`, `MetaConversionService` (stubs — empty files) |
| Intelligence | `RecommendationService`, `FraudScoreService`, `UpsellSuggestionService`, `DynamicPricingService`, `InventoryPredictionService`, `SegmentationService` (stubs — empty files) |

---

## Findings & Fixes

### 🔴 CRITICAL — `LandingCheckoutService` never reserves gift stock

**File:** `app/Domains/Landing/Services/LandingCheckoutService.php` — `checkout()`

**Problem:** Step 6 of `checkout()` iterates `$data['items']` (user-submitted items) to reserve stock. Auto-gift line items injected by the pricing engine live in `$pricing->lineItems` with `discount_type_snapshot = 'Free Gift'` and are never in `$data['items']`. Gift variants' `reserved_stock` was never incremented, leaving them available to other buyers even after commitment.

This is the **third occurrence** of the same bug pattern:
- Phase 5: fixed in `OrderService::create()`
- Phase 6: fixed in `AdminOrderCreationService::create()`
- Phase 7: found in `LandingCheckoutService::checkout()` — now fixed

Each order-creation path must independently handle gift stock reservation. The pattern is identical each time.

**Fix applied:** Added step 6b after the existing stock loop:
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

### 🟠 HIGH — Landing checkout endpoints allow inactive variants/combos

**File:** `app/Domains/Landing/Controllers/LandingCheckoutController.php` — `preview()` and `checkout()`

**Problem:** Both endpoints validated item variant/combo IDs with bare `exists:product_variants,id` and `exists:combos,id` — no `is_active` check. A deactivated variant or combo in the landing page form would pass validation and proceed to the pricing engine.

This is the **fourth occurrence** of the same gap:
- Phase 4: CartController
- Phase 5: CheckoutRequest + CheckoutPreviewRequest
- Phase 6: AdminOrderController::store
- Phase 7: LandingCheckoutController (both endpoints)

**Fix applied:** Both endpoints now use `Rule::exists()->where('is_active', true)`:
```php
'items.*.variant_id' => ['nullable', 'integer', Rule::exists('product_variants', 'id')->where('is_active', true)],
'items.*.combo_id'   => ['nullable', 'integer', Rule::exists('combos', 'id')->where('is_active', true)],
```

---

### 🟠 HIGH — `LandingPageObserver` never clears `listing` type data cache

**File:** `app/Domains/Landing\Observers\LandingPageObserver.php`

**Problem:** `clearCache()` forgets cache keys for three of the four landing page types (`product`, `combo`, `sales`) but not `listing`. The `LandingPageController::show()` caches all four types under `landing:data:{slug}:{type}` with a 2-hour TTL.

When an admin updates or deletes a `listing` type landing page, its product/stock data cache is never invalidated. For up to 2 hours, the store front serves stale items, prices, and stock levels for listing pages — including potentially showing items that were removed from the page or have since gone out of stock.

**Fix applied:**
```php
Cache::forget("landing:data:{$landingPage->slug}:listing");
```

---

### 🟡 MEDIUM — `AdminLandingPageController` selects nonexistent `name` column on Combo

**File:** `app/Domains/Landing/Controllers/AdminLandingPageController.php` — `show()`, `store()`, `update()`

**Problem:** The `combos` table has a `title` column (confirmed in migration `2026_02_27_153806_create_combos_table.php`), not `name`. Three load calls used `combo:id,name,image` or `combo:id,name`:

```php
// show()
'combo:id,name,image'     // ← wrong column
'items.combo:id,name'     // ← wrong column

// store() and update()
'combo:id,name'           // ← wrong column
```

MySQL returns `null` for a selected column that doesn't exist in the constrained column list when using Eloquent's eager-load column syntax. So `$combo->name` was always `null` in the admin API responses for `show`, `store`, and `update`.

Also affected: `LandingPageResource` read `$this->combo->name` (null) instead of `$this->combo->title`.

Note: `AdminLandingPageController::index()` already used `combo:id,title,image` correctly — the inconsistency was only in the other three methods.

**Fix applied:** Changed all occurrences to `combo:id,title` / `combo:id,title,image` and updated `LandingPageResource` to read `$this->combo->title`.

---

### 🟡 MEDIUM — `PublicCouponController::validateCoupon()` calls `lockForUpdate` outside a transaction

**File:** `app/Domains/Coupon/Controllers/PublicCouponController.php`

**Problem:** `CouponValidationService::validate()` calls `Coupon::where(...)->lockForUpdate()->first()`. In MySQL, `SELECT ... FOR UPDATE` outside an explicit transaction is a no-op — the lock is acquired and released immediately, providing zero protection against concurrent reads.

The service code even documents this: *"lockForUpdate requires the caller to be inside a DB transaction (OrderService provides this)"* — but `PublicCouponController` did not provide the transaction.

The practical risk is limited because this is a read-only preview endpoint (no order is created). However, the `isValidForUser()` user-usage-count check (`->usages()->where('user_id', ..)->count()`) can race on a coupon near its `limit_per_user` boundary if two preview calls arrive simultaneously. More importantly, the code's stated intent (locking) was not being fulfilled.

**Fix applied:** Wrapped `validate()` call in `DB::transaction()`:
```php
$result = DB::transaction(fn() => $service->validate(
    $request->code,
    (float) $request->order_amount,
));
```

---

## Observations (No Fix Required)

### ✅ `LandingCheckoutService` — coupon auth gate correct — CORRECT
The coupon-requires-auth check throws before the DB transaction opens, consistent with the main `OrderService` pattern.

### ✅ `LandingCheckoutService` — pricing engine is single source of truth — CORRECT
Uses `CheckoutPricingService::calculate()` with `withLock: false` for preview and `withLock: true` for checkout — same pattern as all other checkout paths.

### ✅ `LandingCheckoutService::calcLandingDiscount()` — `discount_amount` takes precedence over `discount_percent` — CORRECT
Explicitly documented and tested at the boundary (flat wins over percent). Prevents double-discount if both config keys are set.

### ✅ `LandingCheckoutService::applyLandingShippingRules()` — amount check before qty check — CORRECT
Amount threshold is checked first (higher-value rule). Once free shipping is granted, qty check is irrelevant. First-match-wins is the cleaner design.

### ✅ `LandingPageController::show()` — does not check `is_landing_enabled` — CORRECT (by design)
Intentionally bypasses `is_landing_enabled` on the `products` table. The landing page's own `is_active` flag is the authoritative gatekeeper. The code comments explain this explicitly.

### ✅ `AdminCouponController::bulkGenerate()` — deduplicates against DB before insert — CORRECT
Fetches existing codes via `whereIn` before inserting, plus the DB unique constraint on `coupons.code` acts as the final safety net. Concurrent generation race is caught by the constraint exception.

### ✅ `CouponValidationService::calculateDiscount()` — caps fixed discount at order amount — CORRECT
`min($coupon->value, $amount)` prevents `discount_total > subtotal` for fixed-value coupons.

### ✅ `Coupon::isValid()` vs `isValidForUser()` — two distinct methods — CORRECT
`isValid()` is for display/listing purposes (no per-user check). `isValidForUser()` is for checkout (includes per-user usage limit). Used appropriately in `CouponResource` (`isValid()`) and `CouponValidationService` (`isValidForUser()`).

### ℹ️ Intelligence & Marketing services are empty stubs — INFO
`GTMEventService`, `MetaConversionService`, `RecommendationService`, `FraudScoreService`, `UpsellSuggestionService`, `DynamicPricingService`, `InventoryPredictionService`, `SegmentationService` — all are 0-byte stub files. No audit required until implemented. Flag for Phase 12 gap closure.

---

## Production Checklist (Phase 7 Actions)

| Item | Action |
|---|---|
| `LandingPageObserver` — add `created` hook | Currently only `updated` and `deleted` clear cache. On creation (new page), no cache exists yet so this isn't urgent, but symmetry is good |
| Implement `GTMEventService` / `MetaConversionService` | Currently empty — no server-side GA4/Meta events for landing page orders; landing orders will miss conversion tracking until implemented |
| Intelligence stubs | Implement before enabling any AI-driven upsell, fraud, or recommendation features |

---

## Summary

| Severity | Found | Fixed |
|---|---|---|
| 🔴 Critical | 1 | 1 |
| 🟠 High | 2 | 2 |
| 🟡 Medium | 2 | 2 |
| ✅ Pass | 8 | — |
| ℹ️ Info | 1 | — |

**All issues resolved. Marketing & Intelligence layer is production-grade for current feature set.**

---

*Next: Phase 8 — Storefront Frontend (Blade templates, JS files)*
