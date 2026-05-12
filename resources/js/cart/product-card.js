export default function initProductCards() {
    document.querySelectorAll(".product-card").forEach((card) => {
        const variants = JSON.parse(card.dataset.variants || "[]");
        if (!variants.length) return;

        const select = card.querySelector(".variantSelect");
        const price = card.querySelector(".finalPrice");
        const old = card.querySelector(".oldPrice");
        const badge = card.querySelector(".discountBadge");
        const tier = card.querySelector(".tierPreview");
        const addBtn = card.querySelector(".addToCartBtn");
        const contactBtn = card.querySelector(".contactBtn");

        function render(v) {
            // 1. Update Prices — if priceBox is present (single-variant products)
            if (price) {
                price.innerText = "৳" + v.final_price;
            }

            if (old) {
                if (v.discount_percent) {
                    old.innerText = "৳" + v.price;
                    old.classList.remove("hidden");
                } else {
                    old.classList.add("hidden");
                }
            }

            // 2. Update Discount Badge
            if (badge) {
                if (v.discount_percent) {
                    badge.innerText = "-" + v.discount_percent + "%";
                    badge.classList.remove("hidden");
                } else {
                    badge.classList.add("hidden");
                }
            }

            // 3. Update Stock Status / Buttons
            if (v.available_stock <= 0) {
                addBtn?.classList.add("hidden");
                contactBtn?.classList.remove("hidden");
            } else {
                addBtn?.classList.remove("hidden");
                contactBtn?.classList.add("hidden");
            }

            // 4. Update Tier Pricing Preview — ascending order, with perk icons
            if (tier) {
                const sortedTiers = (v.tiers || [])
                    .slice()
                    .sort((a, b) => a.qty - b.qty);
                if (sortedTiers.length) {
                    console.log("Sorted Tiers:", sortedTiers);
                    tier.innerHTML = sortedTiers
                        .map((t) => {
                            const val =
                                t.type === "percentage"
                                    ? `${t.value}%`
                                    : `৳${t.value}`;

                            // Build perk indicators
                            const perks = [];
                            if (t.free_delivery) {
                                perks.push(
                                    `<span class="inline-flex items-center gap-0.5 text-[8px] font-bold text-sky-600 bg-sky-50 border border-sky-200 px-1 py-0.5 rounded leading-none">🚚 Free Delivery</span>`,
                                );
                            }
                            if (t.gift_variant_id) {
                                perks.push(
                                    `<span class="inline-flex items-center gap-0.5 text-[8px] font-bold text-violet-600 bg-violet-50 border border-violet-200 px-1 py-0.5 rounded leading-none">🎁 Gift</span>`,
                                );
                            }

                            return `
                                <div class="bg-white/90 backdrop-blur-sm border border-primary/20 text-primary px-2 py-1 rounded-md shadow-sm">
                                    <p class="text-[9px] font-bold uppercase tracking-tight leading-none">Buy ${t.qty}+</p>
                                    ${t.value > 0 ? `<p class="text-[11px] font-black leading-tight mt-0.5">Save ${val}</p>` : ""}
                                    ${perks.length ? `<div class="flex flex-wrap gap-0.5 mt-0.5">${perks.join("")}</div>` : ""}
                                </div>`;
                        })
                        .join("");
                } else {
                    tier.innerHTML = "";
                }
            }

            // 5. Update the Data Attribute for the Add to Cart logic
            if (addBtn) {
                addBtn.dataset.variant = v.id;

                // Keep GA4 item metadata in sync with the selected variant
                addBtn.dataset.gaItem = JSON.stringify({
                    item_id: card.dataset.productSku || String(v.id),
                    item_name: card.dataset.productName ?? "",
                    item_category: card.dataset.productCategory ?? null,
                    price: parseFloat(v.final_price ?? 0),
                });
            }
        }

        // --- INITIALIZATION ---

        let initial = variants[0];

        // If a select dropdown exists, sync with its current value
        if (select) {
            const found = variants.find((x) => x.id == select.value);
            if (found) initial = found;

            // Listen for changes
            select.addEventListener("change", (e) => {
                const selectedVariant = variants.find(
                    (x) => x.id == e.target.value,
                );
                if (selectedVariant) render(selectedVariant);
            });
        }

        // Run the first render
        render(initial);
    });
}
