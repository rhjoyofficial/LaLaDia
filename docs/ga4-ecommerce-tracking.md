# Complete GA4 eCommerce Tracking — Developer's Reference Guide

> **Project:** LaLaDia — Laravel + Vanilla JS storefront  
> **Stack:** Laravel, GTM, GA4, Vanilla JS (store), Alpine.js (landing templates only)  
> **Last Updated:** 2026-05-08

---

## Table of Contents

1. [What GA4 eCommerce Is and Why It Matters](#1-what-ga4-ecommerce-is-and-why-it-matters)
2. [The Complete GA4 Event List](#2-the-complete-ga4-event-list)
3. [Architecture — How All Pieces Connect](#3-architecture--how-all-pieces-connect)
4. [Data Flow for Every Event](#4-data-flow-for-every-event)
5. [Every File That Was Changed](#5-every-file-that-was-changed)
6. [The GA4 Item Schema Reference](#6-the-ga4-item-schema-reference)
7. [How to Create a New Landing Template](#7-how-to-create-a-new-landing-template)
8. [Events Still Left to Implement](#8-events-still-left-to-implement)
9. [How to Test and Verify](#9-how-to-test-and-verify)
10. [Common Mistakes and Protections](#10-common-mistakes-and-protections)
11. [Quick Reference Map](#11-quick-reference-map)

---

## 1. What GA4 eCommerce Is and Why It Matters

Google Analytics 4 uses a concept called **Enhanced eCommerce**. Every meaningful user action on a shopping site gets reported as a named "event" with structured product data. GA4 collects these events and builds a **funnel report** — it can show you how many users saw a product, added to cart, started checkout, and completed purchase, and where they dropped off.

### How the data travels

```
Your website JavaScript
        │
        ▼
window.dataLayer.push({ event: 'add_to_cart', ecommerce: { ... } })
        │
        ▼
Google Tag Manager (GTM)
  reads the dataLayer, decides what to forward
        │
        ▼
Google Analytics 4
  stores events, builds funnel reports
```

GTM is the "middleman router." It listens to `dataLayer.push()` calls and forwards them to GA4. You never talk to GA4 directly from your site's JS — you always go through `dataLayer → GTM → GA4`.

### The one rule

> **Only `AnalyticsManager.js` is allowed to call `dataLayer.push()`.  
> Nothing else in the codebase should touch it.**

---

## 2. The Complete GA4 Event List

### Tier 1 — Core Funnel (CRITICAL)

| Event | When It Fires | Status |
|---|---|---|
| `view_item` | User opens a product / combo / landing page | ✅ Done |
| `add_to_cart` | User clicks any Add to Cart button | ✅ Done |
| `view_cart` | User visits `/cart` page | ✅ Done |
| `begin_checkout` | User lands on `/checkout` page | ✅ Done |
| `purchase` | Order confirmed on success page | ✅ Done + Enhanced |

### Tier 2 — Additional Funnel (RECOMMENDED, not yet done)

| Event | When It Fires | Notes |
|---|---|---|
| `view_item_list` | User sees a grid of products (shop, category) | Useful for knowing which list position drives sales |
| `select_item` | User clicks a product card from a list | Pairs with `view_item_list` |
| `remove_from_cart` | User removes an item from cart | Good for cart abandonment analysis |
| `add_shipping_info` | User selects a delivery zone on checkout | Adds a funnel step between begin_checkout and purchase |
| `add_payment_info` | User selects a payment method | Adds a step before purchase |

### Tier 3 — Promotional (NICE TO HAVE)

| Event | When It Fires |
|---|---|
| `view_promotion` | A banner or promo is visible to the user |
| `select_promotion` | User clicks a promotional banner |
| `refund` | An order is refunded |

> **Current focus:** Tier 1 is fully done and gives you 90% of the value. Implement `remove_from_cart` and `add_shipping_info` next — they complete the checkout funnel picture.

---

## 3. Architecture — How All Pieces Connect

Think of it as a **three-layer system:**

```
┌─────────────────────────────────────────────────────────────┐
│  LAYER 1: PHP (Laravel Controllers)                         │
│  Knows everything about products at render time.            │
│  Computes item_id, item_name, item_category, price.         │
│  Injects data as window.__ga4__ or HTML data-* attributes.  │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────┐
│  LAYER 2: Blade Templates (The Bridge)                      │
│  Renders PHP data into HTML.                                │
│  Puts item data on buttons as data-ga-item='{"price":350}'  │
│  Injects window.__ga4__ via partials/datalayer.blade.php    │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────┐
│  LAYER 3: JavaScript (Fires the Events)                     │
│                                                             │
│  AnalyticsManager.js  ← ONLY file that calls dataLayer.push │
│  CartManager.js       ← fires add_to_cart on API success    │
│  CheckoutManager.js   ← fires begin_checkout after preview  │
│  app.js               ← fires view_cart, calls autoFire()   │
└─────────────────────────────────────────────────────────────┘
```

### File Map

```
resources/
├── js/
│   ├── analytics/
│   │   └── AnalyticsManager.js       ← NEW — central event hub
│   ├── cart/
│   │   ├── CartManager.js            ← modified: add_to_cart hook
│   │   ├── AddToCartBinder.js        ← unchanged (already passes button)
│   │   └── product-card.js           ← modified: keeps data-ga-item in sync
│   ├── managers/
│   │   └── CheckoutManager.js        ← modified: begin_checkout
│   └── app.js                        ← modified: import, autoFire, view_cart
│
├── views/
│   ├── partials/
│   │   └── datalayer.blade.php       ← modified: injects window.__ga4__
│   ├── components/
│   │   └── product-card.blade.php    ← modified: data-ga-item on button
│   ├── store/
│   │   ├── product.blade.php         ← modified: data-ga-item on button
│   │   └── combo.blade.php           ← modified: data-ga-item on button
│   └── landing/templates/
│       └── listing-default.blade.php ← modified: fires add_to_cart in Alpine
│
└── app/Domains/
    ├── Store/Controllers/
    │   ├── ProductPageController.php  ← modified: passes $ga4 to view
    │   └── ComboPageController.php    ← modified: passes $ga4 to view
    ├── Landing/Controllers/
    │   └── LandingPageController.php  ← modified: passes $ga4 to view
    └── Order/Controllers/
        └── CheckoutController.php     ← modified: adds item_category to purchase
```

---

## 4. Data Flow for Every Event

### `view_item` (product or combo page loads)

```
ProductPageController::show()
  └─ builds $ga4 = ['event' => 'view_item', 'item' => [...]]
      └─ passes $ga4 to Blade view
          └─ datalayer.blade.php → window.__ga4__ = { event, item }
              └─ app.js calls Analytics.autoFire()
                  └─ reads window.__ga4__.event === 'view_item'
                      └─ AnalyticsManager.viewItem(item)
                          └─ dataLayer.push({ event:'view_item', ecommerce:{...} })
```

### `add_to_cart` (user clicks any Add button)

```
User clicks button
  The button has: data-ga-item='{"item_id":"SKU-1","item_name":"Fish","price":350}'
        │
        ▼
AddToCartBinder.js (or inline click handler)
  └─ window.Cart.add(variantId, qty, button)
        │
        ▼
CartManager._performCartAction()
  └─ fetch('/api/v1/cart/add') — API call to server
        │
        ▼
  SUCCESS → reads button.dataset.gaItem → JSON.parse()
  └─ window.Analytics.addToCart(item, qty)
      └─ dataLayer.push({ event:'add_to_cart', ecommerce:{...} })
```

### `view_cart` (/cart page loads)

```
User visits /cart
  └─ app.js boots CartManager → CartManager fetches cart from API
      └─ CartManager fires 'cart:updated' DOM event
          └─ app.js listener (active only on /cart page):
              └─ converts window.Cart.state.items → GA4 items array
                  └─ Analytics.viewCart(ga4Items, totalValue)
                      └─ dataLayer.push({ event:'view_cart', ecommerce:{...} })
```

### `begin_checkout` (/checkout page loads)

```
User visits /checkout
  └─ app.js boots CheckoutManager
      └─ CheckoutManager.init()
          ├─ waitForCart()      → waits for cart state
          ├─ renderItems()      → draws items on screen
          ├─ loadZones()        → loads delivery zones
          ├─ loadCarriedCoupon()
          ├─ bindEvents()
          └─ fetchPreview()     → server confirms items & prices
              └─ _fireBeginCheckout()   ← NEW
                  └─ _checkoutItems()  → cart items OR buy-now item
                      └─ Analytics.beginCheckout(items, value, coupon)
                          └─ dataLayer.push({ event:'begin_checkout', ... })
```

> **Key point:** `_checkoutItems()` automatically handles BOTH normal cart checkout AND buy-now (landing page direct checkout). You don't need separate code for each path.

### `purchase` (order success page loads)

```
Step 1 — Order placement
  POST /api/v1/checkout
    └─ CheckoutController::store()
        ├─ creates Order in database
        ├─ $order->load(['items.variant.product.category', 'items.combo'])
        └─ session()->put('pending_purchase_event', [
               'transaction_id' => $order->order_number,
               'value'          => $order->grand_total,
               'currency'       => 'BDT',
               'coupon'         => $couponCode,
               'items'          => [...with item_category, item_sku, item_variant...]
           ])
        └─ redirects to /order-success/{order_number}

Step 2 — Success page load
  Route: /order-success/{order}
    └─ $purchaseEvent = session()->pull('pending_purchase_event')
        (pull = read + delete atomically — prevents double-firing on refresh)
        └─ passes $purchaseEvent to Blade view
            └─ order-success.blade.php @push('scripts'):
                └─ dataLayer.push({ event:'purchase', ecommerce: $purchaseEvent })
```

---

## 5. Every File That Was Changed

### `resources/js/analytics/AnalyticsManager.js` — NEW FILE

The centralized tracking module. **Every `dataLayer.push()` in the entire project goes through this file and nowhere else.**

```js
const Analytics = {
    push(payload)                    // internal — clears ecommerce, then pushes
    viewItem(item)                   // fires view_item
    addToCart(item, qty)             // fires add_to_cart
    viewCart(items, value)           // fires view_cart
    beginCheckout(items, value, coupon) // fires begin_checkout
    _cartItemToGa4(item, index)      // converts CartItemResource → GA4 schema
    autoFire()                       // reads window.__ga4__, fires the right event
}
```

**Why `_cartItemToGa4()` exists:** Cart state (from CartManager) uses field names like `product_name_snapshot`, `combo_name_snapshot`, `unit_price`. GA4 wants `item_name`, `price`. This helper does that mapping in one place. Every part of the app that needs to convert cart items calls this same helper — no duplication.

**Why `push()` clears ecommerce first:**
```js
push(payload) {
    window.dataLayer = window.dataLayer || [];
    if ('ecommerce' in payload) {
        window.dataLayer.push({ ecommerce: null }); // ← GA4 spec requirement
    }
    window.dataLayer.push(payload);
}
```
GA4 specification requires clearing the previous `ecommerce` object before pushing a new one. If you don't do this, item data from the previous event bleeds into the next. For example, an `add_to_cart` event for Product A would contaminate a subsequent `begin_checkout` event. The `push()` wrapper handles this automatically so callers never have to think about it.

---

### `resources/js/app.js` — MODIFIED

Three additions:

```js
// 1. Import at top
import Analytics from "./analytics/AnalyticsManager";

// 2. Inside DOMContentLoaded, before everything else:
window.Analytics = Analytics;   // expose globally for Blade scripts and Alpine

// 3. After CartManager boots:
Analytics.autoFire();           // fires view_item if page has window.__ga4__

// 4. view_cart boot (only runs on /cart page):
if (document.getElementById("pageCartItems")) {
    // ... CartPage boot ...

    const _fireViewCart = () => {
        const items = window.Cart?.state?.items ?? [];
        if (!items.length) return;
        const ga4Items = items
            .filter((i) => !i.is_gift)
            .map((i, idx) => Analytics._cartItemToGa4(i, idx));
        const value = items
            .filter((i) => !i.is_gift)
            .reduce((s, i) => s + parseFloat(i.unit_price ?? 0) * i.quantity, 0);
        Analytics.viewCart(ga4Items, value);
    };

    if (window.Cart?.initialized) {
        _fireViewCart();
    } else {
        window.addEventListener("cart:updated", _fireViewCart, { once: true });
    }
}
```

**Why `window.Analytics = Analytics`:** Landing page templates and other Blade `<script>` blocks need to call `window.Analytics.addToCart()` without going through the JS module system (ES modules and `<script>` tags are separate scopes). Exposing it on `window` makes it available everywhere.

**Why `view_cart` is in `app.js` and not `CartManager`:** CartManager doesn't know which page it's on — it's a generic cart service. `app.js` is the page-level orchestrator that knows the page context. This separation of concerns keeps CartManager focused on cart operations only.

**Why `{ once: true }` on the event listener:** Ensures `view_cart` fires exactly once. Without it, every time the cart state updates (item added, item removed) it would fire again.

---

### `resources/js/cart/CartManager.js` — MODIFIED

Added `qty` as 4th parameter to `_performCartAction()` and a GA4 hook after success:

```js
async _performCartAction(endpoint, data, button, qty = 1) {
    // ... existing lock/unlock logic ...
    try {
        const res = await this.api(endpoint, data);
        this.setState(res.data);
        window.flash?.("Item Added to cart", "success", 2000);
        this.animateFlyToCart(button);

        // ← NEW: read GA4 item data from the button, fire event
        if (button?.dataset?.gaItem) {
            try {
                const gaItem = JSON.parse(button.dataset.gaItem);
                window.Analytics?.addToCart(gaItem, qty);
            } catch { /* malformed data-ga-item — skip silently */ }
        }
    } catch (e) { ... }
}
```

**Why the inner `try/catch`:** If `data-ga-item` is malformed JSON (a developer typo in a Blade template), the catch prevents the analytics error from crashing the cart. The user can still add items; analytics just won't fire for that one action.

**Why `window.Analytics?.addToCart` with `?.`:** Optional chaining. If Analytics failed to load (ad blocker, script error), this silently does nothing instead of throwing `Cannot read property 'addToCart' of undefined`.

**Why this is the right centralization point:** Every single add-to-cart action in the entire store goes through `_performCartAction`. Product page, shop grid, related products, combo page — all of them. One hook here catches everything.

---

### `resources/js/cart/product-card.js` — MODIFIED

Inside the `render(v)` function (runs on every variant selection change):

```js
// existing: update variant ID on button
addBtn.dataset.variant = v.id;

// NEW: keep GA4 item data in sync with the selected variant
addBtn.dataset.gaItem = JSON.stringify({
    item_id:       card.dataset.productSku || String(v.id),
    item_name:     card.dataset.productName ?? '',
    item_category: card.dataset.productCategory ?? null,
    price:         parseFloat(v.final_price ?? 0),
});
```

**Why this is needed:** A product card on the shop page might have "500g — ৳350" and "1kg — ৳650" variants. When the user switches to 1kg, the price changes. Without this update, `data-ga-item` would still have the 500g price (৳350) set by PHP at page load, and GA4 would log the wrong price.

**Why `card.dataset.productSku` instead of `v.sku`:** The `ProductVariant::toFrontend()` method (which builds the `data-variants` JSON) does not include `sku` — it was designed for cart functionality, not analytics. Rather than modifying that model method (which could have unintended side-effects), we read the product-level SKU from the card's `data-product-sku` attribute (set by the Blade component from PHP).

---

### `resources/js/managers/CheckoutManager.js` — MODIFIED

Added at the end of `init()`:

```js
async init() {
    await this.waitForCart();
    // ... existing code ...
    await this.fetchPreview();

    this._fireBeginCheckout(); // ← NEW
}

// NEW method:
_fireBeginCheckout() {
    if (!window.Analytics) return;
    const items = this._checkoutItems().filter((i) => !i.is_gift);
    if (!items.length) return;

    const ga4Items = items.map((i, idx) =>
        window.Analytics._cartItemToGa4(i, idx)
    );
    const value = items.reduce(
        (s, i) => s + parseFloat(i.unit_price ?? 0) * (i.quantity ?? 1),
        0
    );
    window.Analytics.beginCheckout(ga4Items, value, this.coupon?.code ?? null);
}
```

**Why AFTER `fetchPreview()`:** `fetchPreview()` is when the server confirms that the cart contents are valid (items in stock, prices current). Firing `begin_checkout` before this could report items that were later rejected. Firing after means the data is server-confirmed.

**Why `_checkoutItems()` handles both modes:** This existing method already returns either `[this.buyNowItem]` (landing page buy-now) or `window.Cart.state.items` (regular cart). So `_fireBeginCheckout` works for both paths automatically.

**Why filter `!i.is_gift`:** Free gifts should not be reported as items the user is purchasing. Including them would inflate item counts and distort analytics. They have zero value, which would also skew your average order value reports.

---

### `app/Domains/Store/Controllers/ProductPageController.php` — MODIFIED

Added after the `$relatedProducts` computation:

```php
$defaultVariant = $product->variants->first();
$ga4 = [
    'event' => 'view_item',
    'item'  => [
        'item_id'       => $product->sku ?? (string) ($defaultVariant?->id ?? $product->id),
        'item_name'     => $product->name,
        'item_category' => $product->category?->name,
        'price'         => (float) ($defaultVariant?->final_price ?? $product->base_price),
        'quantity'      => 1,
    ],
];

return view('store.product', [
    'product'         => $product,
    'relatedProducts' => $relatedProducts,
    'ga4'             => $ga4,   // ← NEW
]);
```

**Why PHP and not JS:** Product name, category, and price are available in PHP at render time. Computing them here means zero extra API calls in JS. The data is baked into the HTML before the browser runs any JavaScript.

**Why the `??` chain for `item_id`:** Priority order for a stable, unique identifier:
1. `$product->sku` — best: stable, human-readable (e.g., "HILSA-500G")
2. `$defaultVariant->id` — fallback: numeric, stable
3. `$product->id` — last resort

**Why `price` is the first variant's price:** `view_item` fires once on page load showing the default (first) variant. When the user switches variants, `data-ga-item` on the button updates — but `view_item` is not re-fired. GA4 convention is to report the initially displayed price for `view_item`.

---

### `app/Domains/Store/Controllers/ComboPageController.php` — MODIFIED

Same pattern as ProductPageController. `item_id` uses `combo_{id}` because combos don't have SKUs — they are a bundle construct, not a standalone variant. Prefixing with `combo_` avoids ID collision with regular product variants.

---

### `app/Domains/Landing/Controllers/LandingPageController.php` — MODIFIED

Added `buildGa4Context()` private method and passes `$ga4` to the view:

```php
private function buildGa4Context(LandingPage $landing, array $data): ?array
{
    if ($landing->type === LandingPage::TYPE_PRODUCT && isset($data['product'])) {
        $product = $data['product'];
        $variant = $product->variants->first();
        return [
            'event' => 'view_item',
            'item'  => [
                'item_id'       => $product->sku ?? (string) ($variant?->id ?? $product->id),
                'item_name'     => $product->name,
                'item_category' => $product->category?->name,
                'price'         => (float) ($variant?->final_price ?? $product->base_price),
                'quantity'      => 1,
            ],
        ];
    }

    if ($landing->type === LandingPage::TYPE_COMBO && isset($data['combo'])) {
        $combo = $data['combo'];
        return [
            'event' => 'view_item',
            'item'  => [
                'item_id'       => 'combo_' . $combo->id,
                'item_name'     => $combo->name,
                'item_category' => 'Combo',
                'price'         => (float) $combo->final_price,
                'quantity'      => 1,
            ],
        ];
    }

    return null; // TYPE_SALES and TYPE_LISTING — multiple items, no single view_item
}
```

**Why `null` for TYPE_SALES and TYPE_LISTING:** These pages show multiple products simultaneously. `view_item` is for a single product detail view. For multi-product grids you'd use `view_item_list` (a future implementation).

**No extra DB queries:** The controller already eager-loads `$product->variants` and `$product->category` via `buildProductData()`. Accessing `$data['product']->variants->first()` is free — the data is already in memory.

---

### `app/Domains/Order/Controllers/CheckoutController.php` — MODIFIED

The `pending_purchase_event` session payload was enhanced:

```php
// ← NEW: eager-load relations needed for item_category (one query, no N+1)
$order->load(['items.variant.product.category', 'items.combo']);

$request->session()->put('pending_purchase_event', [
    'transaction_id' => $order->order_number,
    'value'          => (float) $order->grand_total,
    'currency'       => 'BDT',
    'coupon'         => $request->input('coupon_code'), // ← NEW
    'items'          => $order->items->map(fn ($item) => [
        'item_id'       => $item->sku_snapshot              // ← was missing
                            ?? ($item->variant_id ? (string) $item->variant_id : null)
                            ?? ('combo_' . $item->combo_id),
        'item_name'     => $item->combo_name_snapshot ?? $item->product_name_snapshot,
        'item_variant'  => $item->variant_title_snapshot,   // ← NEW
        'item_category' => $item->combo_id                  // ← NEW
                            ? 'Combo'
                            : ($item->variant?->product?->category?->name),
        'price'         => (float) $item->unit_price,
        'quantity'      => $item->quantity,
    ])->toArray(),
]);
```

**Why `$order->load([...])` here:** The `$order->items` are already loaded from order creation. But the items' `variant → product → category` chain is not. `->load()` eager-loads all of them in one SQL query (Laravel resolves the entire chain efficiently). Without this, accessing `$item->variant->product->category->name` in the loop would trigger one query per item (N+1 problem).

**Why `sku_snapshot` for `item_id`:** When the order was created, the variant's current SKU was snapshotted into `OrderItem.sku_snapshot`. If the product's SKU changes in the future (rebranding), the historical purchase event still has the correct SKU from the moment of purchase. This is why order items "snapshot" data rather than referencing live product records.

**Why `coupon` at the event level:** GA4 accepts `coupon` both at the ecommerce level (order-wide coupon) and at the item level (per-item coupon). LaLaDia applies coupons to the whole order, so it belongs at the ecommerce level.

---

### `resources/views/partials/datalayer.blade.php` — MODIFIED

```blade
<script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        'user_type':   '{{ auth()->check() ? "logged_in" : "guest" }}',
        'environment': '{{ app()->environment() }}',
        'page_type':   '{{ $pageType ?? "other" }}'
    });

    {{-- NEW: inject page-level event data set by controllers --}}
    @if (!empty($ga4))
    window.__ga4__ = @json($ga4);
    @endif
</script>
```

**Where this partial is included:** Inside `layouts/app.blade.php`, which every page extends. So every page in the store gets this code. When a controller passes `$ga4` to a view, it automatically appears in `window.__ga4__` without any additional Blade changes.

**Why `@json()` instead of manual encoding:** `@json()` is Laravel's Blade directive that JSON-encodes the PHP array AND escapes it for safe inline JS embedding. It handles quotes, special characters, and Unicode (critical for Bengali product names like "ইলিশ মাছ").

**Why `!empty($ga4)` instead of `isset($ga4)`:** `empty()` returns true for `null`, `[]`, `false`, and unset variables. This prevents `window.__ga4__ = null` or `window.__ga4__ = []` from being injected (which would cause `autoFire()` to silently skip, which is the correct behaviour).

---

### `resources/views/components/product-card.blade.php` — MODIFIED

**Added to the card `<div>`:**
```blade
data-product-name="{{ $product->name }}"
data-product-category="{{ $product->category?->name }}"
data-product-sku="{{ $product->sku }}"
```

**Added to the Add to Cart button:**
```blade
data-ga-item='@json([
    "item_id"       => $product->sku ?? (string) $first?->id,
    "item_name"     => $product->name,
    "item_category" => $product->category?->name,
    "price"         => (float) $first?->final_price,
])'
```

**Why `data-product-*` on the card element:** When the user changes the variant dropdown, `product-card.js` needs access to the product name, category, and SKU to rebuild `data-ga-item`. It can reach the parent card element from the button via `btn.closest('.product-card')`.

**Why `@json()` inside a single-quoted HTML attribute:** If the product name contains a single quote (e.g., "Chef's Choice"), wrapping the attribute in single quotes would break the HTML. `@json()` produces a double-quoted JSON string, so single quotes inside are safe.

---

### `resources/views/store/product.blade.php` — MODIFIED

**Two additions:**

**1. Initial `data-ga-item` on the Add to Cart button (PHP/Blade):**
```blade
data-ga-item='@json([
    "item_id"       => $product->sku ?? (string) ($initialVariant["id"] ?? ""),
    "item_name"     => $product->name,
    "item_category" => $product->category?->name,
    "price"         => (float) ($initialVariant["final_price"] ?? 0),
])'
```

**2. Variant-change GA4 sync (inside the inline `<script>`):**
```js
// This runs every time the user picks a different variant
addToCartBtn.dataset.variant = v.id;
if (mobileStickyCartBtn) mobileStickyCartBtn.dataset.variant = v.id;

// NEW: keep GA4 data in sync
const _gaItem = JSON.stringify({
    item_id:       '{{ addslashes($product->sku ?? '') }}' || String(v.id),
    item_name:     '{{ addslashes($product->name) }}',
    item_category: '{{ addslashes($product->category?->name ?? '') }}' || null,
    price:         parseFloat(v.final_price ?? 0),
});
addToCartBtn.dataset.gaItem = _gaItem;
if (mobileStickyCartBtn) mobileStickyCartBtn.dataset.gaItem = _gaItem;
```

**Why `addslashes()` for PHP strings inside JS string literals:** The product name is a PHP string embedded inside a JavaScript string. If the product name has a single quote (e.g., "Farmer's Honey"), it would break the JS string. `addslashes()` escapes it to `Farmer\'s Honey`. Note: for the Blade `data-*` attributes we use `@json()` instead — this pattern (`addslashes` inside JS string literal) is only for values embedded inside JS string delimiters (`'...'`).

**Why update `mobileStickyCartBtn` too:** The product page has two Add to Cart buttons — the main button and a sticky button at the bottom of the screen on mobile. Both must have the correct `data-ga-item` or the sticky button would report stale/wrong data.

---

### `resources/views/store/combo.blade.php` — MODIFIED

Added `data-ga-item` to the `comboAddToCartBtn`:

```blade
<button id="comboAddToCartBtn"
        data-ga-item='@json([
            "item_id"       => "combo_" . $combo->id,
            "item_name"     => $combo->name,
            "item_category" => "Combo",
            "price"         => (float) $combo->final_price,
        ])'>
```

Combos don't have variant selection, so there's no JS updater needed. The button click handler (`addToCartBtn?.addEventListener(...)`) already passes the button element to `window.Cart.addCombo()`, which means CartManager reads `data-ga-item` automatically.

---

### `resources/views/landing/templates/listing-default.blade.php` — MODIFIED

This template uses **Alpine.js** for reactive UI (quantity steppers, tier pricing display). The challenge: the effective price is computed dynamically by Alpine (`effectivePrice()`), so a static `data-ga-item` attribute would contain a stale price when bulk-tier discounts apply.

**Solution:** Pass `itemName`, `itemCategory`, `itemId` into the Alpine component and fire `window.Analytics.addToCart()` directly inside the `addToCart()` method after success:

```js
// In the x-data initialization:
function listingItem({ isVariant, variantId, comboId, basePrice, tierPrices,
                        itemName, itemCategory, itemId }) {
    return {
        // ... existing state ...
        itemName, itemCategory, itemId,

        async addToCart(btn) {
            if (this.adding || !window.cart) return;
            this.adding = true;
            try {
                if (this.isVariant) {
                    await window.cart.add(this.variantId, this.quantity, btn);
                } else {
                    await window.cart.addCombo(this.comboId, this.quantity, btn);
                }
                // GA4: fire with live effective price (not a static attribute)
                window.Analytics?.addToCart({
                    item_id:       this.itemId,
                    item_name:     this.itemName,
                    item_category: this.itemCategory,
                    price:         this.effectivePrice(),
                }, this.quantity);
                // ... existing success flash ...
            } finally {
                this.adding = false;
            }
        },
    };
}
```

The Blade template passes the values in:
```blade
<div x-data="listingItem({
    isVariant:    {{ $isVariant ? 'true' : 'false' }},
    variantId:    {{ $item->product_variant_id ?? 'null' }},
    comboId:      {{ $item->combo_id ?? 'null' }},
    basePrice:    {{ $price }},
    tierPrices:   @json(...),
    itemName:     @json($name),
    itemCategory: @json($isVariant ? ($item->variant->product->category->name ?? null) : 'Combo'),
    itemId:       @json($isVariant ? (string)$item->product_variant_id : 'combo_'.$item->combo_id),
})">
```

---

## 6. The GA4 Item Schema Reference

Every object inside an `items[]` array must follow this schema:

```js
{
    // REQUIRED by GA4
    item_id:        "SKU-123",         // unique, stable product identifier
    item_name:      "Hilsa Fish",      // product display name

    // HIGHLY RECOMMENDED (GA4 segmentation relies on these)
    item_category:  "Fish",            // top-level category name
    price:          350.00,            // unit price shown to user
    quantity:       1,                 // quantity

    // OPTIONAL BUT USEFUL
    item_variant:   "500g",            // variant name (weight/size/color)
    item_brand:     "LaLaDia",         // brand name
    item_category2: "Dry Fish",        // sub-category (GA4 supports up to 5)
    coupon:         "SAVE10",          // item-level coupon (if any)
    discount:       35.00,             // discount amount on this item
    index:          0,                 // position in list (0-based)
    item_list_name: "Related Products",// which list this item appeared in
    item_list_id:   "related_products",
}
```

### What `price` should be

The effective price the user sees and pays — **after variant-level discounts but before order-level coupons**. The coupon discount is reported separately at the event level as `ecommerce.coupon`.

### What `value` (event-level) should be

Sum of `price × quantity` for all non-gift items. Do **not** include shipping in `value`.

### Free Gifts

Items where `is_gift === true` are filtered out of all GA4 events. They are:
- Zero-price items that distort average order value
- Not something the user chose to purchase
- Excluded by `items.filter(i => !i.is_gift)` in all event builders

### Combos vs Products

| | Products | Combos |
|---|---|---|
| `item_id` | `$product->sku` | `"combo_{id}"` |
| `item_category` | `$product->category->name` | `"Combo"` (hardcoded) |
| `item_variant` | `$variant->title` (e.g. "500g") | not applicable |

---

## 7. How to Create a New Landing Template

When you create a new landing template, you have three tracking checkpoints to handle.

### Checkpoint 1 — `view_item` on page load

**Automatic for TYPE_PRODUCT and TYPE_COMBO:** The `LandingPageController::buildGa4Context()` method already handles these. The `$ga4` variable is passed to the view, `datalayer.blade.php` injects `window.__ga4__`, and `autoFire()` fires `view_item`. **You don't need to do anything extra.**

**For new types (e.g., a custom single-product landing):** Add a new case to `buildGa4Context()` in `LandingPageController.php`:

```php
if ($landing->type === LandingPage::TYPE_YOUR_NEW_TYPE && isset($data['product'])) {
    return [
        'event' => 'view_item',
        'item'  => [
            'item_id'       => $data['product']->sku ?? (string) $data['product']->id,
            'item_name'     => $data['product']->name,
            'item_category' => $data['product']->category?->name,
            'price'         => (float) $data['product']->variants->first()?->final_price,
            'quantity'      => 1,
        ],
    ];
}
```

### Checkpoint 2 — `add_to_cart` when user adds something

#### Pattern A — Vanilla JS (recommended for new templates)

Add `data-ga-item` to every Add to Cart button. CartManager reads it automatically after the API call succeeds. You don't need any extra JS.

**For product variants:**
```blade
<button
    class="addToCartBtn"
    data-variant="{{ $variant->id }}"
    data-ga-item='@json([
        "item_id"       => $product->sku ?? (string) $variant->id,
        "item_name"     => $product->name,
        "item_category" => $product->category?->name,
        "price"         => (float) $variant->final_price,
    ])'>
    Add to Cart
</button>
```

**For combos:**
```blade
<button
    class="addComboBtn"
    data-combo="{{ $combo->id }}"
    data-ga-item='@json([
        "item_id"       => "combo_" . $combo->id,
        "item_name"     => $combo->name,
        "item_category" => "Combo",
        "price"         => (float) $combo->final_price,
    ])'>
    Add to Cart
</button>
```

> `addToCartBtn` and `addComboBtn` are CSS class names listened to by `AddToCartBinder.js`. Always use one of these classes.

#### Pattern B — Dynamic price (Alpine.js with tier pricing)

If the price changes dynamically (tier pricing, quantity-based), fire `window.Analytics.addToCart()` directly inside your add-to-cart JS handler after the cart API call succeeds:

```js
async addToCart() {
    await window.Cart.add(this.variantId, this.quantity);

    window.Analytics?.addToCart({
        item_id:       'your-stable-item-id',
        item_name:     'Product Name',
        item_category: 'Category',
        price:         this.computedPrice, // live value at time of click
    }, this.quantity);
}
```

### Checkpoint 3 — `begin_checkout` and `purchase`

**These are fully automatic. You do not need to do anything.**

- If the landing page uses a **Buy Now button** (stores to `sessionStorage('bionic_buy_now')` and goes to `/checkout?buyNow`): `CheckoutManager._checkoutItems()` automatically picks up the buy-now item and fires `begin_checkout` after the checkout page loads.
- The `purchase` event is built by `CheckoutController::store()` using the order data. It fires on the success page regardless of where the checkout originated.

### New Landing Template Checklist

```
☐ 1. Single product/combo shown?
      TYPE_PRODUCT or TYPE_COMBO → view_item fires automatically
      New type → add a case to LandingPageController::buildGa4Context()

☐ 2. Add to Cart buttons present?
      Vanilla JS → add data-ga-item to every button + use addToCartBtn/addComboBtn class
      Alpine.js with dynamic price → call window.Analytics?.addToCart() after success

☐ 3. Direct checkout (buy-now)?
      begin_checkout and purchase are automatic — nothing to do

☐ 4. Multiple products shown?
      view_item_list not yet implemented — document it as a future TODO

☐ 5. Bengali product names?
      Always use @json() in Blade attributes
      In JS string literals, use addslashes() on the PHP side

☐ 6. Does the template use window.cart (lowercase)?
      listing-default uses window.cart — check your template uses the same reference
      as app.js (which sets window.Cart — note the capital C)
```

> **Note on `window.cart` vs `window.Cart`:** The main app sets `window.Cart` (capital C). The `listing-default.blade.php` template was written using `window.cart` (lowercase). Check which one your template references and be consistent. In new templates, prefer `window.Cart`.

---

## 8. Events Still Left to Implement

### `remove_from_cart`

**When:** User removes an item from cart (on `/cart` page or cart drawer).

**Where:** `CartManager.remove()` in `resources/js/cart/CartManager.js`.

```js
async remove(cartItemId) {
    // Capture item data BEFORE the API call removes it
    const removedItem = this.state.items.find(i => i.id === cartItemId);
    try {
        const res = await this.api("/remove", { cart_item_id: cartItemId });
        this.setState(res.data);

        if (removedItem && window.Analytics) {
            window.Analytics.push({
                event: 'remove_from_cart',
                ecommerce: {
                    currency: 'BDT',
                    value: parseFloat(removedItem.unit_price) * removedItem.quantity,
                    items: [window.Analytics._cartItemToGa4(removedItem)],
                }
            });
        }
    } catch (e) { ... }
}
```

---

### `add_shipping_info`

**When:** User selects a delivery zone on the checkout page.

**Where:** `CheckoutManager.js`, inside the zone selection handler.

```js
// Find where this.selectedZone is updated and add after it:
window.Analytics?.push({
    event: 'add_shipping_info',
    ecommerce: {
        currency:      'BDT',
        value:         this.previewData?.grand_total ?? 0,
        shipping_tier: this.selectedZone.name,
        items: this._checkoutItems()
                   .filter(i => !i.is_gift)
                   .map((i, idx) => window.Analytics._cartItemToGa4(i, idx)),
    }
});
```

---

### `view_item_list`

**When:** User sees a product grid on `/products`, `/category/{slug}`, or `/combos`.

**Where:** `CatalogController::index()` and `CatalogController::category()`, plus a new case in `AnalyticsManager.autoFire()`.

Add to `AnalyticsManager.autoFire()`:
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

Build the `$ga4` payload in the controller:
```php
$ga4 = [
    'event'     => 'view_item_list',
    'list_id'   => 'shop_page',
    'list_name' => 'Shop',
    'items'     => $products->map(fn($p, $idx) => [
        'item_id'       => $p->sku ?? (string) $p->id,
        'item_name'     => $p->name,
        'item_category' => $p->category?->name,
        'price'         => (float) $p->variants->first()?->final_price,
        'index'         => $idx,
    ])->toArray(),
];
```

---

### `add_payment_info`

**When:** User selects a payment method on checkout (COD vs SSLCommerz).

**Where:** `CheckoutManager.js`, inside the payment method selection handler.

---

## 9. How to Test and Verify

### Chrome DevTools — Quick Check

```js
// Paste in browser console on any page:

// 1. Is Analytics loaded?
console.log(typeof window.Analytics?.push === 'function'); // should be: true

// 2. What events have fired so far?
console.log(window.dataLayer);

// 3. What is the last event?
console.log(window.dataLayer[window.dataLayer.length - 1]);

// 4. Manually fire a test event (won't affect production data — GTM filters by environment):
window.Analytics.addToCart({
    item_id: 'TEST-001',
    item_name: 'Test Product',
    item_category: 'Test',
    price: 100
}, 1);
```

### Page-by-Page Test Script

| Page | Expected Event | How to Verify |
|---|---|---|
| `/product/{slug}` | `view_item` | Open console → `window.dataLayer` → last item should have `event: 'view_item'` |
| Click "Add to Cart" | `add_to_cart` | A new item appears in `window.dataLayer` |
| `/cart` | `view_cart` | Check `window.dataLayer` after page loads |
| `/checkout` | `begin_checkout` | Check `window.dataLayer` after page loads |
| `/order-success/{id}` | `purchase` | Check `window.dataLayer` — should have `transaction_id` |

### GTM Preview Mode (Most Reliable)

1. Open Google Tag Manager → your container → **Preview**
2. Enter your site URL
3. A GTM debug panel appears at the bottom of your site
4. Every `dataLayer.push()` appears in the left panel with full event data
5. Click any event to see the exact data GA4 will receive

### Checking Item Data Quality

In the console after visiting a product page:
```js
// Should show your view_item event with item data
window.dataLayer.find(e => e.event === 'view_item')?.ecommerce?.items
```

Expected output:
```js
[{
    item_id: "HILSA-500G",       // or a numeric ID if no SKU
    item_name: "Hilsa Fish",
    item_category: "Fish",
    price: 350,
    quantity: 1,
    index: 0
}]
```

---

## 10. Common Mistakes and Protections

| Mistake | What Goes Wrong | Protection in This Implementation |
|---|---|---|
| Not clearing `ecommerce: null` before push | Previous event's items bleed into next event — GA4 sees combined/wrong data | `push()` always does `{ ecommerce: null }` first, automatically |
| `purchase` fires twice on page refresh | Double-counted revenue in GA4 | `session()->pull()` deletes the key after first read — atomic read+delete |
| `price` includes shipping | Inflated revenue in reports | `value` is computed from `unit_price × quantity` only; shipping is separate |
| Tracking adds that fail (out of stock) | False add_to_cart events | Event fires inside `try` block, after `this.setState(res.data)` succeeds |
| Free gifts counted as purchases | Inflated item counts, zero-price items distorting AOV | All event builders filter `!item.is_gift` |
| Analytics crash breaking cart | Users can't add to cart | All calls use optional chaining: `window.Analytics?.addToCart()` |
| Bengali text breaking JSON | JS syntax error, page breaks | `@json()` handles all Unicode encoding in Blade; `addslashes()` in JS strings |
| Wrong price reported when variant changes | Misleading add_to_cart price in GA4 | `product-card.js` updates `data-ga-item` in `render(v)` on every variant change |
| GA4 item_category missing | Cannot segment purchases by category | Purchase event eager-loads `items.variant.product.category` before building payload |

---

## 11. Quick Reference Map

### Where Is Each Event Fired?

| Event | Fired In | File | Line (approx) |
|---|---|---|---|
| `view_item` | `autoFire()` | [AnalyticsManager.js](../resources/js/analytics/AnalyticsManager.js) | `autoFire()` method |
| `add_to_cart` (store) | `_performCartAction()` | [CartManager.js](../resources/js/cart/CartManager.js) | After API success |
| `add_to_cart` (listing page) | `addToCart()` Alpine method | [listing-default.blade.php](../resources/views/landing/templates/listing-default.blade.php) | After cart.add() |
| `view_cart` | `cart:updated` listener | [app.js](../resources/js/app.js) | /cart page boot block |
| `begin_checkout` | `_fireBeginCheckout()` | [CheckoutManager.js](../resources/js/managers/CheckoutManager.js) | End of `init()` |
| `purchase` | Inline `<script>` | [order-success.blade.php](../resources/views/store/order-success.blade.php) | `@push('scripts')` |

### Where Is Data Injected?

| Data | Set By | Consumed By |
|---|---|---|
| `window.__ga4__` | PHP controllers via `$ga4` variable | `AnalyticsManager.autoFire()` |
| `data-ga-item` (initial) | Blade templates (`@json([...])`) | `CartManager._performCartAction()` |
| `data-ga-item` (on change) | `product-card.js` `render(v)` | `CartManager._performCartAction()` |
| `data-product-name/category/sku` | `product-card.blade.php` | `product-card.js` when rebuilding `data-ga-item` |
| `pending_purchase_event` session | `CheckoutController::store()` | `/order-success` route closure |

### Controller → `$ga4` → Event Table

| Controller Method | `$ga4` Event | Fires When |
|---|---|---|
| `ProductPageController::show()` | `view_item` | User opens `/product/{slug}` |
| `ComboPageController::show()` | `view_item` | User opens `/combos/{slug}` |
| `LandingPageController::show()` (TYPE_PRODUCT) | `view_item` | User opens a product landing page |
| `LandingPageController::show()` (TYPE_COMBO) | `view_item` | User opens a combo landing page |
| `LandingPageController::show()` (TYPE_SALES/LISTING) | `null` | Not applicable — multiple items |
| `PublicCartController::view()` | none — JS handles it | `/cart` page |
| `CheckoutController::index()` | none — JS handles it | `/checkout` page |
| `CheckoutController::store()` | `purchase` via session | Order success page |
