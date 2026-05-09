/**
 * AnalyticsManager — single source of truth for all GA4 eCommerce dataLayer pushes.
 *
 * Page-level events (view_item) are injected by PHP controllers via window.__ga4__
 * before DOMContentLoaded, then consumed by autoFire().
 *
 * Cart-level events (add_to_cart, view_cart, begin_checkout) are fired by JS
 * managers that have access to live cart state.
 *
 * External usage:
 *   window.Analytics.addToCart(item, qty)
 *   window.Analytics.viewCart(items, value)
 *   window.Analytics.beginCheckout(items, value, coupon)
 */
const Analytics = {

    /**
     * Push to dataLayer. Clears previous ecommerce context per GA4 spec.
     */
    push(payload) {
        window.dataLayer = window.dataLayer || [];
        if ('ecommerce' in payload) {
            window.dataLayer.push({ ecommerce: null });
        }
        window.dataLayer.push(payload);
    },

    // ── Page-level ──────────────────────────────────────────────────────────

    viewItem(item) {
        this.push({
            event: 'view_item',
            ecommerce: {
                currency: 'BDT',
                value: parseFloat(item.price ?? 0),
                items: [{ ...item, index: 0 }],
            },
        });
    },

    // ── Cart / funnel ───────────────────────────────────────────────────────

    /**
     * Fired by CartManager after a successful /add or /add-combo API call.
     * @param {Object} item  GA4 item schema (from data-ga-item on button)
     * @param {number} qty   Quantity added
     */
    addToCart(item, qty = 1) {
        this.push({
            event: 'add_to_cart',
            ecommerce: {
                currency: 'BDT',
                value: parseFloat((parseFloat(item.price ?? 0) * qty).toFixed(2)),
                items: [{ ...item, quantity: qty }],
            },
        });
    },

    /**
     * Fired on /cart page load after cart state is hydrated.
     * @param {Array}  items  GA4 item schema array
     * @param {number} value  Cart subtotal
     */
    viewCart(items, value) {
        this.push({
            event: 'view_cart',
            ecommerce: {
                currency: 'BDT',
                value: parseFloat(value.toFixed(2)),
                items,
            },
        });
    },

    /**
     * Fired on /checkout page after cart items are confirmed by fetchPreview().
     * @param {Array}       items   GA4 item schema array
     * @param {number}      value   Order value before shipping
     * @param {string|null} coupon  Applied coupon code (if any)
     */
    beginCheckout(items, value, coupon = null) {
        const ecommerce = {
            currency: 'BDT',
            value: parseFloat(value.toFixed(2)),
            items,
        };
        if (coupon) ecommerce.coupon = coupon;
        this.push({ event: 'begin_checkout', ecommerce });
    },

    // ── Utilities ───────────────────────────────────────────────────────────

    /**
     * Converts a CartItemResource-shaped object to a GA4 item schema.
     * Used for view_cart and begin_checkout (where cart state drives the data).
     * item_category is intentionally omitted here — cart state doesn't carry it.
     * It IS present on purchase (set server-side) and on view_item (from controller).
     *
     * @param {Object} item  CartItemResource JSON object
     * @param {number} index Position in list (0-based)
     */
    _cartItemToGa4(item, index = 0) {
        const isCombo = !!item.combo_id;
        return {
            item_id: isCombo
                ? `combo_${item.combo_id}`
                : String(item.variant_id ?? ''),
            item_name: isCombo
                ? (item.combo_name_snapshot ?? '')
                : (item.product_name_snapshot ?? ''),
            item_variant: isCombo ? null : (item.variant_title_snapshot ?? null),
            item_category: isCombo ? 'Combo' : null,
            price: parseFloat(item.unit_price ?? 0),
            quantity: item.quantity ?? 1,
            index,
        };
    },

    /**
     * Auto-fires the correct event based on window.__ga4__ injected by the controller.
     * Call once from app.js inside DOMContentLoaded, after cart is booted.
     */
    autoFire() {
        const cfg = window.__ga4__;
        if (!cfg?.event) return;

        switch (cfg.event) {
            case 'view_item':
                if (cfg.item) this.viewItem(cfg.item);
                break;
            // view_cart and begin_checkout are fired client-side (JS managers),
            // not via __ga4__, because they need live cart state.
        }
    },
};

export default Analytics;
