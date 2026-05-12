export default class CartManager {
    constructor() {
        this.state = {
            items: [],
            subtotal: 0,
            totalQty: 0,
        };

        this.pending = false;
        this.lockedButtons = new Set();
        this.initialized = false;

        this.token = this.ensureToken();

        this.sidebar = document.getElementById("cartDrawer");
        this.badge = document.getElementById("cartCount");
        this.cartBadge = document.getElementById("cartCountBadge");

        this.init();
    }

    ensureToken() {
        // Helper to read cookie
        const getCookie = (name) => {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(";").shift();
            return null;
        };

        // Helper to set cookie
        const setCookie = (name, value, days = 30) => {
            const date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/;SameSite=Lax`;
        };

        const cookieName = "bionic_cart_token";
        let cartToken = getCookie(cookieName);

        // If no cookie, check localStorage or generate a new one
        if (!cartToken) {
            cartToken = localStorage.getItem(cookieName) || crypto.randomUUID();
            // Sync it to both places
            setCookie(cookieName, cartToken);
            localStorage.setItem(cookieName, cartToken);
        }

        return cartToken;
    }

    async api(url, data = {}, method = "POST") {
        const res = await fetch(`/api/v1/cart${url}`, {
            method,
            headers: this._getHeaders(),
            body: method === "GET" ? null : JSON.stringify(data),
        });

        const json = await res.json();

        if (!res.ok) throw json;

        return json;
    }

    async init() {
        await this.refresh();
    }

    async refresh() {
        try {
            const res = await fetch(`/api/v1/cart`, {
                headers: this._getHeaders(),
            });
            const json = await res.json();
            this.setState(json.data);
        } catch {
            this.setState({
                items: [],
                totals: { subtotal: 0, total_qty: 0 },
            });
        } finally {
            this.initialized = true;
        }
    }

    setState(payload) {
        if (payload.prices_updated && typeof flash === "function") {
            window.flash?.(
                "Price Alert",
                "warning",
                5000,
                "Prices in your cart have been updated to reflect current rates.",
            );
        }

        const totals = payload.totals ?? {};
        this.state = {
            items:        payload.items || [],
            subtotal:     totals.subtotal     || 0,  // full pre-discount price
            tierDiscount: totals.discount     || 0,  // tier savings total
            total:        totals.total        || (totals.subtotal || 0),  // final payable
            totalQty:     totals.total_qty    || 0,
        };

        if (this.badge) {
            this.badge.innerText = this.state.totalQty;
        }

        if (this.cartBadge) {
            this.cartBadge.innerText = this.state.totalQty;
        }

        window.dispatchEvent(new Event("cart:updated"));
    }

    async add(variantId, qty = 1, button = null) {
        return this._performCartAction(
            "/add",
            { variant_id: variantId, quantity: qty },
            button,
            qty,
        );
    }

    async addCombo(comboId, qty = 1, button = null) {
        return this._performCartAction(
            "/add-combo",
            { combo_id: comboId, quantity: qty },
            button,
            qty,
        );
    }

    _getHeaders() {
        const headers = {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Session-Token": this.token,
        };

        // Only add CSRF if available (Blade context)
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrf) headers["X-CSRF-TOKEN"] = csrf;
        return headers;
    }

    async _performCartAction(endpoint, data, button, qty = 1) {
        if (this.pending) return;
        this.pending = true;

        if (button) {
            this.lockedButtons.add(button);
            button.classList.add("opacity-50", "pointer-events-none");
        }

        try {
            const res = await this.api(endpoint, data);
            this.setState(res.data);
            if (typeof flash === "function" && !res.data.prices_updated)
                window.flash?.("Item Added to cart", "success", 2000);
            this.animateFlyToCart(button);

            // GA4: add_to_cart — item metadata lives on the button via data-ga-item
            if (button?.dataset?.gaItem) {
                try {
                    const gaItem = JSON.parse(button.dataset.gaItem);
                    window.Analytics?.addToCart(gaItem, qty);
                } catch { /* malformed data-ga-item — skip silently */ }
            }
            // this.open();
        } catch (e) {
            if (typeof flash === "function")
                window.flash?.(e.message || "Action failed", "error");
        } finally {
            this.pending = false;
            if (button) {
                button.classList.remove("opacity-50", "pointer-events-none");
                this.lockedButtons.delete(button);
            }
        }
    }

    async update(cartItemId, qty) {
        const sanitizedQty = Math.max(1, parseInt(qty) || 1);
        try {
            const res = await this.api("/update", {
                cart_item_id: cartItemId,
                quantity: sanitizedQty,
            });
            this.setState(res.data);
        } catch (e) {
            if (typeof flash === "function")
                window.flash?.(e.message || "Update failed", "error");
        }
    }

    async remove(cartItemId) {
        try {
            const res = await this.api("/remove", { cart_item_id: cartItemId });
            this.setState(res.data);
        } catch (e) {
            if (typeof flash === "function")
                window.flash?.(e.message || "Remove failed", "error");
        }
    }

    async clear() {
        try {
            await this.api("/clear", {}, "DELETE");
            await this.refresh();
            window.flash?.("Cart cleared", "success");
        } catch {
            window.flash?.("Clear failed", "error");
        }
    }

    /**
     * Navigate to the checkout page.
     * The actual order submission is handled by CheckoutManager on /checkout.
     */
    checkout() {
        window.location.href = "/checkout";
    }

    open() {
        const overlay = document.getElementById("overlay");
        this.sidebar?.classList.remove("translate-x-full");
        overlay?.classList.remove("opacity-0", "invisible");
        overlay?.classList.add("opacity-100");
        document.body.classList.add("overflow-hidden");
    }

    close() {
        const overlay = document.getElementById("overlay");
        this.sidebar?.classList.add("translate-x-full");
        overlay?.classList.remove("opacity-100");
        overlay?.classList.add("opacity-0", "invisible");
        document.body.classList.remove("overflow-hidden");
    }

    animateFlyToCart(button) {
        const cart = document.querySelector("#floatingCartButton");

        // Guard: skip on mobile where cart button is display:none (hidden md:flex)
        // NOTE: offsetParent is always null for position:fixed — use getComputedStyle instead
        if (!cart || !button || getComputedStyle(cart).display === "none") return;

        const btnRect = button.getBoundingClientRect();
        const cartRect = cart.getBoundingClientRect();

        // Guard: skip if positions couldn't be resolved (e.g. button unmounted mid-flight)
        if (!btnRect.width || !cartRect.width) return;

        const particle = document.createElement("div");
        particle.className = "cart-particle";
        particle.style.left = `${btnRect.left + btnRect.width / 2 - 16}px`;
        particle.style.top = `${btnRect.top + btnRect.height / 2 - 16}px`;
        particle.innerHTML = `<i class="fa-solid fa-crown text-4xl text-primary"></i>`;
        document.body.appendChild(particle);

        // Double rAF: first paints start position, second triggers the CSS transition
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                particle.style.left = `${cartRect.left + cartRect.width / 2}px`;
                particle.style.top = `${cartRect.top + cartRect.height / 2}px`;
                particle.style.transform = "scale(0.2)";
                particle.style.opacity = "0";
            });
        });

        particle.addEventListener("transitionend", () => {
            particle.remove();

            // Re-check cart still visible before applying impact classes
            if (getComputedStyle(cart).display === "none") return;

            cart.classList.add("animate-cart-hit");
            const badge = document.getElementById("cartCountBadge");
            badge?.classList.add("badge-shake");

            setTimeout(() => {
                cart.classList.remove("animate-cart-hit");
                badge?.classList.remove("badge-shake");
            }, 700);
        }, { once: true });
    }
}
