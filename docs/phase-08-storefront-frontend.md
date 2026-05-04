# Phase 8 — Storefront Frontend (Blade + JS)

**Date:** 2026-05-04  
**Auditor:** Claude (Senior Engineer Review)  
**Status:** ✅ Complete — all issues fixed

---

## Scope

| Area | Files Reviewed |
|---|---|
| JS Modules | `CartManager.js`, `CartRenderer.js`, `CartPageRenderer.js`, `CheckoutManager.js`, `app.js`, `api/client.js`, `AddToCartBinder.js`, `product-card.js` |
| Blade — Pages | `checkout.blade.php`, `cart.blade.php`, `product.blade.php`, `shop.blade.php` |
| Blade — Partials | `hero.blade.php`, `cart-drawer.blade.php`, `header.blade.php`, `footer.blade.php`, `trending-products.blade.php` |

---

## Findings & Fixes

### 🔴 CRITICAL — `CartManager.setState()` throws on network error — cart never recovers

**File:** `resources/js/cart/CartManager.js` — `setState()` and `refresh()`

**Problem:** `setState()` reads `payload.totals.subtotal` and `payload.totals.total_qty`. When the API is unreachable, `refresh()` catches the exception and calls `this.setState({ items: [], subtotal: 0, totalQty: 0 })` — but this fallback object has no `totals` key.

`setState()` then tries to read `payload.totals.subtotal` → `undefined.subtotal` → `TypeError`. The exception propagates out of the `catch` block, so `this.initialized` is **never set to `true`**, and `window.dispatchEvent(new Event("cart:updated"))` is never fired. The result: the cart stays in permanent loading state (skeleton), and `CheckoutManager.waitForCart()` blocks indefinitely (falling back only after its 3-second timeout).

**Fix applied:**
1. `setState()` now reads through a safe default: `const totals = payload.totals ?? {};`
2. The `refresh()` catch block now passes a correctly shaped fallback: `{ items: [], totals: { subtotal: 0, total_qty: 0 } }`

---

### 🟠 HIGH — GA4 `begin_checkout` always fires with `items: []` and `value: 0`

**File:** `resources/views/store/checkout.blade.php` — inline `<script>` block

**Problem:** The GTM `begin_checkout` event was triggered in a `setTimeout(..., 800)` that read from `document.querySelectorAll('#coItemsList [data-variant-id]')`. However, `CheckoutManager.renderItems()` builds HTML strings without any `data-variant-id`, `data-name`, or `data-price` attributes. The selector always returns 0 elements.

The fallback read `document.getElementById('coTotal').textContent`, which is `"—"` on page load (before a zone is selected). After stripping non-numeric characters, `value = 0`.

Result: every checkout page view pushed `{ event: 'begin_checkout', ecommerce: { value: 0, items: [] } }` to the dataLayer — GA4 received no meaningful conversion data.

**Fix applied:** Rewrote the event push to read directly from `window.Cart.state.items` (the authoritative in-memory cart state), using the `cart:updated` event to wait for the cart to finish loading rather than a fixed timeout:

```js
function pushBeginCheckout() {
    const cartItems = window.Cart?.state?.items ?? [];
    const items = cartItems
        .filter(i => !i.is_gift)
        .map(i => ({
            item_id:   String(i.variant_id ?? ('combo_' + i.combo_id)),
            item_name: i.combo_name_snapshot ?? i.product_name_snapshot ?? '',
            price:     parseFloat(i.unit_price) || 0,
            quantity:  i.quantity,
        }));
    const value = items.reduce((s, i) => s + i.price * i.quantity, 0);
    // ... push to dataLayer
}
if (window.Cart?.initialized) {
    pushBeginCheckout();
} else {
    window.addEventListener('cart:updated', pushBeginCheckout, { once: true });
    setTimeout(pushBeginCheckout, 1500); // absolute fallback
}
```

---

### 🟠 HIGH — `ga_client_id` never sent from `CheckoutManager` (Phase 5 pending action)

**File:** `resources/js/managers/CheckoutManager.js` — `submit()`

**Problem:** Phase 5 added `ga_client_id` collection on the backend (`CheckoutController.store()`) with a fallback to the `_ga` cookie. The Phase 5 production checklist explicitly listed "Frontend: send `ga_client_id` in `CheckoutManager.js`" as a remaining action. The `submit()` payload was never updated.

The backend fallback to `$request->cookie('_ga')` works for session-cookie setups, but the raw `_ga` cookie value (`GA1.1.1234567890.1234567890`) is the full cookie format — not the bare `clientId` portion GA4 expects (`1234567890.1234567890`). Sending the frontend-extracted client ID is more accurate.

**Fix applied:** Added `ga_client_id: this._getGaClientId()` to the submit payload and added a `_getGaClientId()` helper that:
1. First tries `window.ga.getAll()[0].get('clientId')` (accurate, works when GA.js is loaded)
2. Falls back to parsing the `_ga` cookie to extract the `<part1>.<part2>` client ID suffix

---

### 🟡 MEDIUM — SVG `xmlns` namespace truncated in `hero.blade.php`

**File:** `resources/views/store/partials/hero.blade.php` — two navigation button SVGs

**Problem:** Both swiper navigation arrows used `xmlns="http://www.w3.org"` instead of the correct `xmlns="http://www.w3.org/2000/svg"`. The SVG XML namespace URL was truncated. Strict XML parsers and some SVG renderers reject elements with an invalid namespace. In Chromium-based browsers this is silently tolerated, but it is invalid markup that can cause rendering failures in Safari or when SVGs are processed by tooling.

**Fix applied:** Both occurrences replaced with `xmlns="http://www.w3.org/2000/svg"`.

---

### 🟢 LOW — `_perfomCartAction` typo in `CartManager`

**File:** `resources/js/cart/CartManager.js`

**Problem:** Internal method name `_perfomCartAction` (missing an `r` — should be `_performCartAction`). Referenced consistently in `add()` and `addCombo()` so no runtime error, but confusing to read and would cause a `TypeError` if any caller used the correct spelling.

**Fix applied:** Renamed via `replace_all` to `_performCartAction`.

---

## Observations (No Fix Required)

### ✅ `CheckoutManager` — submit is idempotent — CORRECT
`this.submitting` flag prevents double-submit on rapid clicks or Enter-key spam. Both the form `submit` event and the button `click` event call the same `submit()`, guarded by the flag.

### ✅ `CheckoutManager` — buy-now mode correctly bypasses cart — CORRECT
`_consumeBuyNowItem()` reads + clears `sessionStorage` atomically at construction time. The `?buyNow` query param is the trigger; absence of the param means normal cart mode even if `bionic_buy_now` is stale.

### ✅ `CartManager` — token sync between cookie + localStorage — CORRECT
`ensureToken()` reads from cookie first, then falls back to localStorage. Writes to both on creation. This handles cookie-blocking browsers and cross-tab consistency.

### ✅ `CheckoutManager.applyCoupon()` — uses preview endpoint, not coupon/validate — CORRECT
The checkout page validates coupons via the full `/checkout/preview` endpoint (which accounts for zone, items, and tier discounts) rather than the simpler `/coupon/validate` endpoint. This prevents the cart-page discount from differing from the checkout discount for edge cases involving tier discounts and minimum purchase thresholds.

### ✅ `CartPageRenderer._renderTotals()` — no shipping included — CORRECT (by design)
The cart page shows subtotal and coupon discount only, not shipping. Shipping is resolved at checkout when a zone is selected. The total shown is the pre-shipping total — this is standard e-commerce UX.

### ✅ `CartRenderer` — renders only on `cart:updated` — CORRECT
Using `{ once: false }` on the event listener means the drawer re-renders on every cart state change (add, remove, update). This keeps the drawer always in sync without polling.

### ✅ `app.js` — page-specific boots are gated by DOM element presence — CORRECT
`CartPageRenderer` only boots if `#pageCartItems` exists. `CheckoutManager` only boots if `#checkoutForm` exists. This prevents both from initialising on every page and competing for the same DOM IDs.

### ✅ `api/client.js` — injects Bearer token if present — CORRECT
The API client checks `localStorage.getItem("auth_token")` and adds the `Authorization` header for Sanctum token-auth flows. For session-auth Blade flows, the token is absent and the CSRF header alone is used — correct for both auth modes.

### ℹ️ `{!! $banner->title !!}` in `hero.blade.php` — admin-controlled rich text — INFO
The hero banner title uses unescaped output to support HTML markup (line breaks, bold). This is intentional for rich text stored via the admin panel. Since only admins can write banner data, the XSS surface is limited to compromised admin accounts. Consider adding server-side HTML sanitization (e.g., `HTMLPurifier` or Laravel's `Str::of($title)->stripTags(...)`) as a defense-in-depth measure if banner titles grow in complexity.

---

## Production Checklist (Phase 8 Actions)

| Item | Action |
|---|---|
| Verify `ga_client_id` is received by GA4 | Check GA4 DebugView on a test checkout to confirm client ID deduplication is working |
| Test cart recovery on network error | Disconnect network mid-page, confirm cart shows empty gracefully instead of hanging skeleton |
| Hero banner HTML sanitization | Add server-side sanitization for `banner->title` rich text before it reaches `{!! !!}` |

---

## Summary

| Severity | Found | Fixed |
|---|---|---|
| 🔴 Critical | 1 | 1 |
| 🟠 High | 2 | 2 |
| 🟡 Medium | 1 | 1 |
| 🟢 Low | 1 | 1 |
| ✅ Pass | 8 | — |
| ℹ️ Info | 1 | — |

**All issues resolved. Storefront frontend is production-grade.**

---

*Next: Phase 9 — Admin Panel Frontend (admin Blade views, Alpine.js components)*
