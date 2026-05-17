# GA4 eCommerce Tracking — Developer Reference

> **Project:** LaLaDia — Laravel + Vanilla JS storefront  
> **Stack:** Laravel 11, GTM, GA4, Vanilla JS (store), Alpine.js (landing templates)  
> **Last Updated:** 2026-05-17  
> **For the full multi-platform guide** (Meta CAPI, Consent Mode, deduplication): see [`docs/tracking-guide.md`](./tracking-guide.md)

---

## Table of Contents

1. [Architecture — How the Three Layers Connect](#1-architecture--how-the-three-layers-connect)
2. [Event Status — What Is Done and What Is Not](#2-event-status--what-is-done-and-what-is-not)
3. [Data Flow for Every Implemented Event](#3-data-flow-for-every-implemented-event)
4. [The GA4 Item Schema](#4-the-ga4-item-schema)
5. [File-by-File Reference](#5-file-by-file-reference)
6. [How to Add a New Landing Template](#7-how-to-add-a-new-landing-template)
7. [Events Still to Implement — Ready-to-Paste Code](#7-events-still-to-implement--ready-to-paste-code)
8. [Testing & Verification](#8-testing--verification)
9. [Quick Reference Map](#9-quick-reference-map)

---

## 1. Architecture — How the Three Layers Connect

```
┌─────────────────────────────────────────────────────────────────┐
│  LAYER 1: PHP Controllers                                        │
│  Knows product data at render time (name, category, price, SKU) │
│  Builds $ga4 array and passes to Blade view                     │
│  Builds pending_purchase_event session payload after order      │
└───────────────────────────┬─────────────────────────────────────┘
                            │ $ga4 array  /  session payload
┌───────────────────────────▼─────────────────────────────────────┐
│  LAYER 2: Blade Templates                                        │
│  datalayer.blade.php → window.__ga4__ = @json($ga4)             │
│  order-success.blade.php → dataLayer.push({ event: 'purchase' })│
│  product-card buttons → data-ga-item='@json([...])'             │
└───────────────────────────┬─────────────────────────────────────┘
                            │ window.__ga4__  /  data-ga-item
┌───────────────────────────▼─────────────────────────────────────┐
│  LAYER 3: JavaScript                                             │
│                                                                  │
│  AnalyticsManager.js ← ONLY file that calls dataLayer.push()    │
│    autoFire()       reads window.__ga4__, fires view_item       │
│    addToCart()      fired by CartManager after API success       │
│    viewCart()       fired by app.js on /cart page               │
│    beginCheckout()  fired by CheckoutManager after preview       │
│                                                                  │
│  purchase event is pushed directly in order-success.blade.php   │
└─────────────────────────────────────────────────────────────────┘
                            │
                    window.dataLayer
                            │
                    Google Tag Manager
                            │
                  Google Analytics 4
```

### The one rule

> **Only `AnalyticsManager.js` pushes ecommerce events to `dataLayer`.**  
> Direct `dataLayer.push()` in other JS files is not allowed (except the purchase event in Blade, which runs in a separate script context without module imports).

---

## 2. Event Status — What Is Done and What Is Not

### Tier 1 — Core Funnel

| Event | Status | Fired By | Trigger |
|---|---|---|---|
| `view_item` | ✅ Done | `AnalyticsManager.autoFire()` | PHP controller sets `window.__ga4__`, JS reads it on DOMContentLoaded |
| `add_to_cart` | ✅ Done | `CartManager._performCartAction()` + listing Alpine | After cart API call succeeds |
| `view_cart` | ✅ Done | `app.js` — cart:updated listener | `/cart` page, once after cart state hydrates |
| `begin_checkout` | ✅ Done | `CheckoutManager._fireBeginCheckout()` | After `fetchPreview()` confirms items server-side |
| `purchase` | ✅ Done | `order-success.blade.php` inline script | Session payload read once (pull = atomic delete) |

### Tier 2 — Recommended

| Event | Status | Notes |
|---|---|---|
| `remove_from_cart` | ❌ Not done | See [Section 7](#7-events-still-to-implement--ready-to-paste-code) |
| `add_shipping_info` | ❌ Not done | See [Section 7](#7-events-still-to-implement--ready-to-paste-code) |
| `add_payment_info` | ❌ Not done | See [Section 7](#7-events-still-to-implement--ready-to-paste-code) |
| `view_item_list` | ❌ Not done | See [Section 7](#7-events-still-to-implement--ready-to-paste-code) |
| `select_item` | ❌ Not done | Implement after `view_item_list` |

### Tier 3 — Nice to Have

| Event | Status |
|---|---|
| `view_promotion` | ❌ Not done |
| `select_promotion` | ❌ Not done |
| `refund` | ❌ Not done |

---

## 3. Data Flow for Every Implemented Event

### `view_item`

```
ProductPageController::show()  (or ComboPageController, LandingPageController)
  └─ builds: $ga4 = ['event' => 'view_item', 'item' => ['item_id', 'item_name',
                                                         'item_category', 'item_brand', 'price', 'quantity: 1']]
      └─ passes $ga4 to Blade view
          └─ partials/datalayer.blade.php:
                window.__ga4__ = { event: 'view_item', item: { ... } }
              └─ app.js DOMContentLoaded:
                    Analytics.autoFire()
                      └─ reads window.__ga4__.event === 'view_item'
                          └─ Analytics.viewItem(cfg.item)
                              └─ dataLayer.push({ event: 'view_item', ecommerce: { currency: 'BDT', value: ..., items: [item] } })
```

**Files:**
- Controllers: [`ProductPageController.php`](../app/Domains/Store/Controllers/ProductPageController.php), [`ComboPageController.php`](../app/Domains/Store/Controllers/ComboPageController.php), [`LandingPageController.php`](../app/Domains/Landing/Controllers/LandingPageController.php)
- Bridge: [`resources/views/partials/datalayer.blade.php`](../resources/views/partials/datalayer.blade.php)
- Fires: [`resources/js/analytics/AnalyticsManager.js`](../resources/js/analytics/AnalyticsManager.js) — `autoFire()` / `viewItem()`

---

### `add_to_cart`

**Path A — Store (any Add to Cart button)**

```
User clicks button with data-ga-item='{"item_id":"...", "item_name":"...", "price":350}'
  └─ AddToCartBinder.js (or inline handler) → window.Cart.add(variantId, qty, button)
      └─ CartManager._performCartAction('/add', data, button, qty)
          └─ fetch('/api/v1/cart/add')  ← server call
              └─ SUCCESS:
                  └─ reads button.dataset.gaItem → JSON.parse()
                      └─ window.Analytics.addToCart(gaItem, qty)
                          └─ dataLayer.push({ event: 'add_to_cart', ecommerce: { ... } })
```

**Path B — Listing landing page (Alpine.js with tier pricing)**

```
User clicks Add to Cart in listing-default.blade.php
  └─ listingItem.addToCart(btn)
      └─ window.Cart.add(variantId, qty, btn)  OR  window.Cart.addCombo(...)
          └─ SUCCESS:
              └─ window.Analytics?.addToCart({
                     item_id:       this.itemId,
                     item_name:     this.itemName,
                     item_category: this.itemCategory,
                     price:         this.effectivePrice(),   ← live tier-discounted price
                 }, this.quantity)
```

**Why Path B is different:** The listing template has tier pricing (buy 2, get cheaper price). The effective price is computed by Alpine at click time — it can't be baked into a static `data-ga-item` attribute. Path B fires the event directly inside the Alpine method so the live price is captured.

**Files:**
- [`resources/js/cart/CartManager.js`](../resources/js/cart/CartManager.js) — `_performCartAction()`
- [`resources/views/landing/templates/listing-default.blade.php`](../resources/views/landing/templates/listing-default.blade.php) — `addToCart()` Alpine method
- `data-ga-item` set on buttons in: `product-card.blade.php`, `product.blade.php`, `combo.blade.php`

---

### `view_cart`

```
User visits /cart
  └─ app.js boots CartManager → CartManager.init() → fetches cart from API
      └─ CartManager.setState() fires: window.dispatchEvent(new CustomEvent('cart:updated'))
          └─ app.js listener (only active when #pageCartItems element exists on page):
              └─ _fireViewCart()
                  └─ reads window.Cart.state.items
                      └─ filters out is_gift items
                          └─ maps each item through Analytics._cartItemToGa4()
                              └─ Analytics.viewCart(ga4Items, totalValue)
                                  └─ dataLayer.push({ event: 'view_cart', ecommerce: { ... } })
```

**Why `{ once: true }` on the listener:** Ensures `view_cart` fires exactly once. Without it, any subsequent cart state update (remove item, update qty) would re-fire the event.

**File:** [`resources/js/app.js`](../resources/js/app.js) — `/cart` page boot block

---

### `begin_checkout`

```
User visits /checkout
  └─ app.js boots CheckoutManager.init()
      └─ init() sequence:
          1. waitForCart()         → waits for CartManager to hydrate
          2. renderItems()         → draws items on screen
          3. loadZones()           → loads delivery zone options
          4. loadCarriedCoupon()   → restores any saved coupon code
          5. bindEvents()          → attaches form listeners
          6. fetchPreview()        → POST to /api/v1/checkout/preview (server validates & prices)
              └─ _fireBeginCheckout()   ← fires AFTER preview (server-confirmed data)
                  └─ _checkoutItems()   → returns [buyNowItem] OR Cart.state.items
                      └─ filters !is_gift
                          └─ Analytics.beginCheckout(ga4Items, value, coupon)
                              └─ dataLayer.push({ event: 'begin_checkout', ecommerce: { ... } })
```

**Why after `fetchPreview()`:** The server preview validates that items are in stock and prices are current. Firing before this could report items that would later fail (e.g., out of stock). Firing after means the data is server-confirmed.

**Why `_checkoutItems()` handles both modes:** This existing method returns either `[this.buyNowItem]` (buy-now / landing page flow) or `window.Cart.state.items` (regular cart checkout). One hook covers both paths.

**File:** [`resources/js/managers/CheckoutManager.js`](../resources/js/managers/CheckoutManager.js) — `_fireBeginCheckout()`

---

### `purchase`

```
STEP 1 — Order placement (POST /checkout or POST /api/v1/landing/{slug}/checkout)
  └─ CheckoutController::store()  OR  LandingCheckoutController::checkout()
      ├─ creates Order in DB
      ├─ $order->load(['items.variant.product.category', 'items.combo'])  ← for item_category
      ├─ session()->put('last_order_id', $order->id)                      ← for ownership check
      └─ session()->put('pending_purchase_event', [
             'event_id'       => 'purchase_' . $order->id,               ← dedup key
             'transaction_id' => $order->order_number,
             'value'          => (float) $order->grand_total,
             'currency'       => 'BDT',
             'coupon'         => $couponCode,
             'items'          => [{ item_id, item_name, item_variant, item_category, price, qty }]
         ])
      └─ redirect to /order-success/{order_number}

STEP 2 — Success page load (GET /order-success/{order})
  └─ routes/web.php closure:
      ├─ ownership check (auth user or session key)
      ├─ $purchaseEvent = session()->pull('pending_purchase_event')  ← read + delete atomically
      └─ return view('store.order-success', compact('order', 'purchaseEvent'))

STEP 3 — Browser fires the event
  └─ order-success.blade.php @push('scripts'):
      └─ dataLayer.push({
             event:    'purchase',
             event_id: 'purchase_{{ $order->id }}',   ← top-level for GTM Meta tag
             ecommerce: @json($purchaseEvent),         ← contains all item data
         })
```

**The `session()->pull()` is critical:** `pull()` is an atomic read-and-delete. If the user refreshes the success page, `$purchaseEvent` is `null` on the second load, `@if (!empty($purchaseEvent))` is false, and the script block does not render. Purchase event fires exactly once.

**Files:**
- [`app/Domains/Order/Controllers/CheckoutController.php`](../app/Domains/Order/Controllers/CheckoutController.php) — `store()`
- [`app/Domains/Landing/Controllers/LandingCheckoutController.php`](../app/Domains/Landing/Controllers/LandingCheckoutController.php) — `checkout()`
- [`routes/web.php`](../routes/web.php) — `/order-success/{order}` route closure
- [`resources/views/store/order-success.blade.php`](../resources/views/store/order-success.blade.php) — `@push('scripts')`

---

## 4. The GA4 Item Schema

### Standard fields used in this project

```js
{
    item_id:        "SKU-ABC",        // stable identifier — see priority below
    item_name:      "Hilsa Fish",     // display name
    item_category:  "Fish",           // top-level category (null for view_cart/begin_checkout)
    item_brand:     "LaLaDia",        // present on view_item events
    item_variant:   "500g",           // variant name — present on purchase, null on cart events
    price:          350.00,           // effective unit price (after variant discount, before coupon)
    quantity:       1,                // units
    index:          0,                // list position (used by view_item_list / select_item)
}
```

### `item_id` priority order

This priority is consistent across every event and every file in the project:

```
1. sku_snapshot   (order items) / $product->sku (catalog)   → "HILSA-500G"
2. String variant_id                                         → "42"
3. "combo_" + combo_id                                       → "combo_7"
```

Combos always use the `combo_` prefix to avoid integer collision with variant IDs.

### `item_category` — when it is and isn't present

| Event | `item_category` present? | Reason |
|---|---|---|
| `view_item` | ✅ Yes — from PHP controller | Controller has product relation loaded |
| `add_to_cart` (store) | ✅ Yes — from `data-ga-item` on button | Set in Blade by PHP |
| `add_to_cart` (listing) | ✅ Yes — from Alpine component props | Passed as `itemCategory` prop |
| `view_cart` | ❌ Not present | Cart state API response doesn't include category |
| `begin_checkout` | ❌ Not present | Same reason — cart state doesn't carry it |
| `purchase` (browser) | ✅ Yes | PHP eager-loads `items.variant.product.category` before building session payload |
| `purchase` (server/MP) | ✅ Yes | Job eager-loads same chain in `fresh()` |

`view_cart` and `begin_checkout` not having `item_category` is acceptable — GA4's funnel reports don't require it for those events.

### Free gifts

Items where `is_gift === true` are filtered from all GA4 events with `.filter(i => !i.is_gift)`. They are zero-price items that distort revenue and item count reporting.

---

## 5. File-by-File Reference

### `resources/js/analytics/AnalyticsManager.js`

The only file in the project allowed to call `dataLayer.push()` with ecommerce events.

| Method | What it does |
|---|---|
| `push(payload)` | Clears `ecommerce: null`, then pushes. Called internally by all other methods. |
| `viewItem(item)` | Fires `view_item` event |
| `addToCart(item, qty)` | Fires `add_to_cart` event |
| `viewCart(items, value)` | Fires `view_cart` event |
| `beginCheckout(items, value, coupon)` | Fires `begin_checkout` event |
| `_cartItemToGa4(item, index)` | Converts `CartItemResource` shape → GA4 item schema. Used by view_cart and begin_checkout. |
| `autoFire()` | Reads `window.__ga4__`, fires the right event. Called once from `app.js`. |

**`_cartItemToGa4()` mapping:**

```js
_cartItemToGa4(item, index = 0) {
    const isCombo = !!item.combo_id;
    return {
        item_id:       isCombo ? `combo_${item.combo_id}` : String(item.variant_id ?? ''),
        item_name:     isCombo ? item.combo_name_snapshot : item.product_name_snapshot,
        item_variant:  isCombo ? null : item.variant_title_snapshot,
        item_category: isCombo ? 'Combo' : null,   // null for products — cart state lacks it
        price:         parseFloat(item.unit_price ?? 0),
        quantity:      item.quantity ?? 1,
        index,
    };
}
```

---

### `resources/views/partials/datalayer.blade.php`

Included in `<head>` on every page via both `app.blade.php` and `guest.blade.php`. Fires before GTM loads.

```blade
<script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        'user_type':   '{{ auth()->check() ? "logged_in" : "guest" }}',
        'user_id':     {{ auth()->check() ? auth()->id() : 'null' }},
        'environment': '{{ app()->environment() }}',
        'page_type':   '{{ $pageType ?? "other" }}'
    });

    @if (!empty($ga4))
    window.__ga4__ = @json($ga4);
    @endif
</script>
```

`$pageType` is set automatically by a View Composer in `AppServiceProvider::boot()`. You never set it in a controller.

`@json()` handles all Unicode encoding — Bengali product names, special characters, quotes — making the output safe for inline JS.

---

### Controllers that build `$ga4`

| Controller | Method | `$ga4` event | `item_brand` |
|---|---|---|---|
| `ProductPageController` | `show()` | `view_item` | ✅ `config('app.name')` |
| `ComboPageController` | `show()` | `view_item` | ✅ `config('app.name')` |
| `LandingPageController` | `buildGa4Context()` | `view_item` (TYPE_PRODUCT / TYPE_COMBO) | ✅ `config('app.name')` |
| `LandingPageController` | `buildGa4Context()` | `null` (TYPE_SALES / TYPE_LISTING) | — |

For `TYPE_SALES` and `TYPE_LISTING` landing pages, `$ga4` is `null` — these pages show multiple products and don't emit a single `view_item`. When `view_item_list` is implemented, these controllers will need to build that payload instead.

---

### `resources/views/store/order-success.blade.php`

The `@push('scripts')` block fires the `purchase` event:

```blade
@push('scripts')
    @if (!empty($purchaseEvent))
        <script>
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({
                event:    'purchase',
                event_id: 'purchase_{{ $order->id }}',
                ecommerce: @json($purchaseEvent),
            });
        </script>
    @endif
@endpush
```

`$order` is available from the route closure in `routes/web.php`. `$purchaseEvent` is from `session()->pull()` — it's `null` on any page refresh, so this block only renders once.

The `event_id` at the top level (not inside `ecommerce`) is what GTM reads for Meta deduplication.

---

### `resources/views/landing/templates/listing-default.blade.php`

This is the one template that fires `add_to_cart` directly in Alpine (not via `data-ga-item`) because of dynamic tier pricing. Key fix: uses `window.Cart` (capital C) — not `window.cart`.

```js
async addToCart(btn) {
    if (this.adding || !window.Cart) return;   // ← capital C — matches app.js export
    this.adding = true;
    try {
        if (this.isVariant) {
            await window.Cart.add(this.variantId, this.quantity, btn);
        } else {
            await window.Cart.addCombo(this.comboId, this.quantity, btn);
        }
        window.Analytics?.addToCart({
            item_id:       this.itemId,
            item_name:     this.itemName,
            item_category: this.itemCategory,
            price:         this.effectivePrice(),   // ← live computed price
        }, this.quantity);
    } finally {
        this.adding = false;
    }
}
```

---

## 6. How to Add a New Landing Template

### Checkpoint 1 — `view_item` on page load

**TYPE_PRODUCT and TYPE_COMBO:** Handled automatically. `LandingPageController::buildGa4Context()` already covers these. No action needed.

**New landing type:** Add a case to `buildGa4Context()` in [`LandingPageController.php`](../app/Domains/Landing/Controllers/LandingPageController.php):

```php
if ($landing->type === LandingPage::TYPE_YOUR_TYPE && isset($data['product'])) {
    return [
        'event' => 'view_item',
        'item'  => [
            'item_id'       => $data['product']->sku ?? (string) $data['product']->id,
            'item_name'     => $data['product']->name,
            'item_category' => $data['product']->category?->name,
            'item_brand'    => config('app.name', 'LaLaDia'),
            'price'         => (float) $data['product']->variants->first()?->final_price,
            'quantity'      => 1,
        ],
    ];
}
```

### Checkpoint 2 — `add_to_cart`

**Static price (recommended for simple templates):** Add `data-ga-item` to every button. CartManager reads it automatically. Use `addToCartBtn` or `addComboBtn` CSS class so `AddToCartBinder.js` picks up the click.

```blade
<button class="addToCartBtn"
        data-variant="{{ $variant->id }}"
        data-ga-item='@json([
            "item_id"       => $product->sku ?? (string) $variant->id,
            "item_name"     => $product->name,
            "item_category" => $product->category?->name,
            "item_brand"    => config("app.name", "LaLaDia"),
            "price"         => (float) $variant->final_price,
        ])'>
    Add to Cart
</button>
```

**Dynamic price (tier pricing, Alpine.js):** Call `window.Analytics?.addToCart()` directly after the cart API call succeeds — same pattern as `listing-default.blade.php`.

### Checkpoint 3 — `begin_checkout` and `purchase`

These are fully automatic. You do nothing. Both events are handled by `CheckoutManager` and `CheckoutController` respectively, regardless of where the checkout originated.

### New template checklist

```
☐ Single product/combo shown?
      TYPE_PRODUCT / TYPE_COMBO → view_item fires automatically
      New type → add case to LandingPageController::buildGa4Context()

☐ Add to Cart buttons present?
      Static price → data-ga-item attribute + addToCartBtn/addComboBtn class
      Dynamic price → call window.Analytics?.addToCart() after cart API success

☐ Using Alpine.js?
      Use window.Cart (capital C) — not window.cart (lowercase)
      Add ga_client_id extraction in init() if form submits directly to checkout

☐ Multiple products on page?
      view_item_list not yet implemented — note as TODO
```

---

## 7. Events Still to Implement — Ready-to-Paste Code

### `remove_from_cart`

**File:** [`resources/js/cart/CartManager.js`](../resources/js/cart/CartManager.js) — `remove()` method

```js
async remove(cartItemId) {
    // Capture item BEFORE the API call removes it from state
    const removedItem = this.state.items.find(i => i.id === cartItemId);

    const res = await this.api('/remove', { cart_item_id: cartItemId });
    this.setState(res.data);

    if (removedItem && window.Analytics) {
        window.Analytics.push({
            event: 'remove_from_cart',
            ecommerce: {
                currency: 'BDT',
                value: parseFloat(removedItem.unit_price ?? 0) * removedItem.quantity,
                items: [window.Analytics._cartItemToGa4(removedItem)],
            },
        });
    }
}
```

---

### `add_shipping_info`

**File:** [`resources/js/managers/CheckoutManager.js`](../resources/js/managers/CheckoutManager.js)

Find where `this.selectedZoneId` is updated after a zone radio change, and add after `fetchPreview()` resolves:

```js
const zoneName = this.zones?.find(z => z.id === this.selectedZoneId)?.name ?? '';
window.Analytics?.push({
    event: 'add_shipping_info',
    ecommerce: {
        currency:      'BDT',
        value:         parseFloat(this.previewData?.grand_total ?? 0),
        shipping_tier: zoneName,
        items: this._checkoutItems()
                   .filter(i => !i.is_gift)
                   .map((i, idx) => window.Analytics._cartItemToGa4(i, idx)),
    },
});
```

---

### `add_payment_info`

**File:** [`resources/js/managers/CheckoutManager.js`](../resources/js/managers/CheckoutManager.js)

Find the payment method selection handler:

```js
window.Analytics?.push({
    event: 'add_payment_info',
    ecommerce: {
        currency:     'BDT',
        value:        parseFloat(this.previewData?.grand_total ?? 0),
        payment_type: this.paymentMethod,   // 'cod' or 'sslcommerz'
        items: this._checkoutItems()
                   .filter(i => !i.is_gift)
                   .map((i, idx) => window.Analytics._cartItemToGa4(i, idx)),
    },
});
```

---

### `view_item_list`

**Two-file change:**

**File 1:** [`app/Domains/Store/Controllers/CatalogController.php`](../app/Domains/Store/Controllers/CatalogController.php) — `index()` and `category()` methods

```php
$ga4 = [
    'event'     => 'view_item_list',
    'list_id'   => 'shop_page',        // or 'category_{$category->slug}'
    'list_name' => 'Shop',             // or $category->name
    'items'     => $products->map(fn ($p, $idx) => [
        'item_id'       => $p->sku ?? (string) $p->id,
        'item_name'     => $p->name,
        'item_category' => $p->category?->name,
        'item_brand'    => config('app.name', 'LaLaDia'),
        'price'         => (float) ($p->variants->first()?->final_price ?? 0),
        'index'         => $idx,
    ])->toArray(),
];
```

**File 2:** [`resources/js/analytics/AnalyticsManager.js`](../resources/js/analytics/AnalyticsManager.js) — add case to `autoFire()`

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

## 8. Testing & Verification

### Console quick check

```js
// On any page — paste in DevTools console:

// Base push OK?
console.log(window.dataLayer[0]);
// → { user_type: 'guest', user_id: null, environment: 'local', page_type: 'home' }

// All events so far?
console.table(window.dataLayer.filter(e => e.event));

// view_item data quality (on product page):
window.dataLayer.find(e => e.event === 'view_item')?.ecommerce;
// → { currency: 'BDT', value: 350, items: [{ item_id: 'SKU-X', item_category: 'Fish', item_brand: 'LaLaDia', ... }] }

// Purchase event (on /order-success):
const p = window.dataLayer.find(e => e.event === 'purchase');
console.log(p?.event_id);         // 'purchase_123'
console.log(p?.ecommerce?.items); // array with item_category present
```

### Page-by-page test matrix

| URL | Expected events | Key checks |
|---|---|---|
| `/` | base push only | `page_type: 'home'` |
| `/product/{slug}` | base push + `view_item` | `item_category` present, `item_brand: 'LaLaDia'` |
| `/combos/{slug}` | base push + `view_item` | `item_id: 'combo_N'`, `item_category: 'Combo'` |
| Click Add to Cart | `add_to_cart` added | correct `price`, `quantity` |
| `/cart` | `view_cart` | item count matches cart, no gift items |
| `/checkout` | `begin_checkout` | fires after zone loads, `value` excludes shipping |
| `/order-success/{id}` | `purchase` | `event_id: 'purchase_N'`, all items have `item_category`, `ecommerce` absent on refresh |
| `/login` | base push | `page_type: 'other'`, no JS errors |

### GTM Preview Mode

1. GTM → Preview → enter your site URL
2. Every `dataLayer.push()` appears in the left panel
3. Click "purchase" event → Variables tab → confirm `event_id` is `purchase_N`
4. This is the value that must match your CAPI event for deduplication

---

## 9. Quick Reference Map

### Where each event is fired

| Event | File | Method / Location |
|---|---|---|
| `view_item` | [`AnalyticsManager.js`](../resources/js/analytics/AnalyticsManager.js) | `autoFire()` → `viewItem()` |
| `add_to_cart` (store) | [`CartManager.js`](../resources/js/cart/CartManager.js) | `_performCartAction()` — after API success |
| `add_to_cart` (listing) | [`listing-default.blade.php`](../resources/views/landing/templates/listing-default.blade.php) | `addToCart()` Alpine method |
| `view_cart` | [`app.js`](../resources/js/app.js) | `cart:updated` listener, `/cart` page only |
| `begin_checkout` | [`CheckoutManager.js`](../resources/js/managers/CheckoutManager.js) | `_fireBeginCheckout()` — end of `init()` |
| `purchase` | [`order-success.blade.php`](../resources/views/store/order-success.blade.php) | `@push('scripts')` block |

### Where `$ga4` data originates (PHP → JS bridge)

| Controller | Sets `$ga4` for | Event emitted |
|---|---|---|
| `ProductPageController::show()` | `/product/{slug}` | `view_item` |
| `ComboPageController::show()` | `/combos/{slug}` | `view_item` |
| `LandingPageController::show()` (TYPE_PRODUCT) | product landing pages | `view_item` |
| `LandingPageController::show()` (TYPE_COMBO) | combo landing pages | `view_item` |
| `LandingPageController::show()` (TYPE_SALES/LISTING) | multi-item landing pages | `null` — no view_item |
| `CheckoutController::store()` | order success page | `purchase` (via session, not `$ga4`) |
| `LandingCheckoutController::checkout()` | landing order success | `purchase` (via session, not `$ga4`) |

### Where `data-ga-item` is set (Blade → JS bridge)

| File | Button | Notes |
|---|---|---|
| [`product-card.blade.php`](../resources/views/components/product-card.blade.php) | Add to Cart button | Initial value from PHP; updated by `product-card.js` on variant change |
| [`product.blade.php`](../resources/views/store/product.blade.php) | Main + mobile sticky button | Updated by inline JS on variant select |
| [`combo.blade.php`](../resources/views/store/combo.blade.php) | Combo Add to Cart button | Static (combos have no variants) |
| `listing-default.blade.php` | (not used) | Alpine fires Analytics directly — price is dynamic |
