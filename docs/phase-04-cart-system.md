# Phase 4 — Cart System

**Date:** 2026-05-04  
**Auditor:** Claude (Senior Engineer Review)  
**Status:** ✅ Complete — all issues fixed

---

## Scope

| Area | Files Reviewed |
|---|---|
| Controllers | `CartController`, `PublicCartController` |
| Services | `CartService`, `CartPricingService`, `CartMergeService` |
| Models | `Cart`, `CartItem` |
| Resources | `CartItemResource` |

---

## Findings & Fixes

### 🟠 HIGH — `releaseReservedStock` has no row-level locks

**File:** `app/Domains/Cart/Services/CartService.php`

**Problem:** Every other stock mutation in `CartService` uses `ProductVariant::lockForUpdate()` to prevent race conditions — `addItem`, `removeItem`, `clearCart`, `updateItemQuantity` all lock the variant rows. `releaseReservedStock` was the sole exception: it called `$ci->variant->decrement(...)` on a relation that was loaded without a lock.

`releaseReservedStock` is called by `CartMergeService::merge()` for both the guest and user carts at login, and by `reserveStock` during checkout. Under concurrent login requests (e.g., double-tap), two threads could both read the same `reserved_stock` value before either write completes, resulting in an incomplete release and permanently inflated reserved stock.

**Fix applied:** Rewrote the method to collect all affected variant IDs first, then issue a single `lockForUpdate` batch query — the same pattern used in `clearCart`:
```php
$variants = ProductVariant::whereIn('id', $variantIds->unique())
    ->lockForUpdate()
    ->get()
    ->keyBy('id');
```
Also added `min($variant->reserved_stock, ...)` floor guards to prevent negative reserved stock from stale data.

---

### 🟠 HIGH — Inactive variant/combo can be added to cart

**File:** `app/Domains/Cart/Controllers/CartController.php`

**Problem:** The `add()` and `addCombo()` validation rules used `exists:product_variants,id` and `exists:combos,id` respectively — these only verify the record exists, not that it's active. An attacker who knows a deactivated variant's ID (e.g., from a cached product page, old cart payload, or enumeration) could add it to the cart. The `CartService::addItem` does not check `is_active` either, delegating entirely to the controller validation.

**Fix applied:** Changed both validations to `Rule::exists(...)->where('is_active', true)`:
```php
// add():
'variant_id' => [
    'required',
    Rule::exists('product_variants', 'id')->where('is_active', true)
],

// addCombo():
'combo_id' => [
    'required',
    Rule::exists('combos', 'id')->where('is_active', true)
],
```

---

### 🟡 MEDIUM — `CartItemResource` tiers missing incentive fields

**File:** `app/Domains/Cart/Resources/CartItemResource.php`

**Problem:** The `tiers` array in the cart item resource only included `qty`, `type`, `value`. The cart page JS uses this data to render tier incentive prompts ("Add 2 more for free delivery!"). Without `has_free_delivery`, `gift_variant_id`, etc., these prompts had no data.

This was the third location where tier incentive fields were missing (previously fixed in `ProductTierResource` and `ProductVariant::toFrontend()`).

**Fix applied:** Extended the tier mapping to include all incentive fields:
```php
'free_delivery'   => (bool)  $t->has_free_delivery,
'delivery_zones'  =>         $t->free_delivery_zones ?? [],
'gift_variant_id' =>         $t->gift_product_variant_id,
'gift_qty'        =>         $t->gift_quantity,
```

---

### 🟡 MEDIUM — N+1 query for gift item images in cart payload

**File:** `app/Domains/Cart/Controllers/CartController.php`

**Problem:** `payload()` built the auto-gift display items with a loop that issued one `ProductVariant::with('product')->find($gift['variant_id'])` per gift. With 3 active gifts, that's 3 separate DB round-trips on every cart view/update response. This is a classic N+1 pattern.

**Fix applied:** Batch-loaded all gift variants in a single query before the loop using `whereIn` + `keyBy`:
```php
$giftVariants = ProductVariant::with('product')
    ->whereIn('id', $giftVariantIds)
    ->get()
    ->keyBy('id');
```

---

## Observations (No Fix Required)

### ✅ Stock reservation is atomic — CORRECT
All add/update/remove operations wrap the stock mutation (increment/decrement on `reserved_stock`) inside `DB::transaction()` with `lockForUpdate()` on the variant row. This prevents double-booking under concurrent requests.

### ✅ `getCart()` guest cart guard — CORRECT
`CartService::getCart()` throws `InvalidArgumentException` if `$sessionToken` is null for a guest request. This prevents the catastrophic case where all anonymous users share a single null-token cart row. The `HandleCartSession` middleware ensures a token is always set before the controller runs.

### ✅ `updateItemQuantity` recalculates tier price — CORRECT
When a user changes quantity, `pricingService->calculate($variant, $newQty)` is called with the new total quantity, so the tier discount is re-evaluated at the correct threshold. The `unit_price_snapshot` is updated accordingly.

### ✅ `syncCartPrices` — safe pre-checkout price refresh
Called on cart view and before merge. Compares stored snapshot vs recalculated price with `0.001` epsilon tolerance to avoid floating-point jitter. Returns `$anyPriceChanged` boolean so the frontend can notify the user of price changes.

### ✅ `CartMergeService::merge` — quantity union on login
When a guest logs in, item quantities are summed (not replaced). Price snapshot is recalculated with the new combined quantity, so tier pricing activates at the right threshold after merge.

### ✅ `CartPricingService` delegates to `CheckoutPricingService` — single source of truth
The cart totals display uses the same pricing engine as checkout by calling `CheckoutPricingService::calculate(items, coupon=null, zone=null)`. This guarantees the cart totals match what checkout would compute, avoiding "price changed at checkout" surprises.

### ✅ Cart ownership validation — CORRECT
`update()` and `remove()` use a custom validation rule that queries `cart_items WHERE id=? AND cart_id=?`. A user cannot modify or remove another user's cart items by guessing item IDs.

### ✅ `clearCart` uses batch lock — CORRECT
Collects all variant IDs, issues one `lockForUpdate` query, then decrements. Matches the pattern now applied to `releaseReservedStock`.

### ℹ️ `CartService::addItem` double stock check
Lines 90 and 99 both call `$variant->hasStock($qty)` — the first is correct pre-insert; the second in the update branch is redundant (the same qty was already checked). No bug (both checks protect the same invariant), but the second check is dead code in the update path since the stock was already validated.

---

## Summary

| Severity | Found | Fixed |
|---|---|---|
| 🟠 High | 2 | 2 |
| 🟡 Medium | 2 | 2 |
| ✅ Pass | 8 | — |
| ℹ️ Info | 1 | — |

**All issues resolved. Cart system is race-condition safe and production-grade.**

---

*Next: Phase 5 — Checkout & Orders (Backend)*
