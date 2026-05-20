{{--
    Shared landing checkout form.

    Required variables:
    - $landing
    - $zones

    Parent templates may set:
    - window.initialItems = [{ variant_id: 1, quantity: 1 }]
--}}

<div id="landingCheckout"
    class="bg-white border border-red-100 rounded-3xl p-6 md:p-10 shadow-xl shadow-red-100/50 font-noto">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">অর্ডার কনফার্ম করুন</h2>
        <p class="text-red-600 text-sm italic mt-2">সঠিক তথ্য দিয়ে নিচের ফর্মটি পূরণ করুন।</p>
    </div>

    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input id="lcCustomerName" type="text" placeholder="আপনার নাম *"
                class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-1 focus:ring-red-200 outline-none text-sm transition-all">
            <input id="lcCustomerPhone" type="tel" placeholder="মোবাইল নম্বর *"
                class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-1 focus:ring-red-200 outline-none text-sm transition-all">
        </div>

        <input id="lcAddressLine" type="text" placeholder="পূর্ণ ঠিকানা (বাসা নম্বর, রোড, এলাকা, জেলা) *"
            class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-1 focus:ring-red-200 outline-none text-sm transition-all">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input id="lcCustomerEmail" type="email" placeholder="ইমেইল (ঐচ্ছিক)"
                class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-1 focus:ring-red-200 outline-none text-sm transition-all">
            <input id="lcCity" type="text" placeholder="শহর / জেলা"
                class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-1 focus:ring-red-200 outline-none text-sm transition-all">
        </div>

        <div
            class="p-4 bg-orange-50 border border-dashed border-orange-300 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4">
            <div class="flex items-center gap-3">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                </span>
                <p class="text-xs md:text-sm text-amber-900">আরও বেশি বা পাইকারি নিতে চান?</p>
            </div>
            <a href="tel:01334943783" class="text-sm md:text-base font-black text-pink-700 hover:underline">
                কল করুন: 01334 943783
            </a>
        </div>

        <div id="lcDeliveryZoneSection"
            class="bg-red-50/50 p-4 rounded-xl border border-red-100 mt-4 transition-all">
            <p class="text-sm font-bold text-gray-700 mb-1">
                ডেলিভারি এলাকা নির্বাচন করুন
                <span class="text-red-500">*</span>
            </p>
            <p id="lcZoneError" class="hidden text-xs text-red-600 mb-2 font-medium">
                অনুগ্রহ করে একটি ডেলিভারি এলাকা নির্বাচন করুন।
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach ($zones as $zone)
                    <label
                        class="lc-zone-label flex flex-col items-center p-2 border rounded-lg cursor-pointer text-center transition-all border-gray-200 bg-white hover:border-red-400">
                        <input type="radio" name="lc_zone_id" value="{{ $zone->id }}" class="hidden">
                        <span class="text-xs font-bold">{{ $zone->name }}</span>
                        @if ($zone->base_charge == 0)
                            <span class="text-xs text-green-600">ফ্রি</span>
                        @else
                            <span class="text-xs text-red-600">৳{{ number_format($zone->base_charge, 0) }}</span>
                            @if ($zone->free_shipping_threshold)
                                <span class="text-[10px] text-gray-400">
                                    ৳{{ number_format($zone->free_shipping_threshold, 0) }}+ ফ্রি
                                </span>
                            @endif
                        @endif
                    </label>
                @endforeach
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
            <p class="text-sm font-bold text-gray-700 mb-2">পেমেন্ট মেথড</p>
            <label
                class="flex items-center gap-3 p-3 rounded-xl border border-red-500 bg-white cursor-pointer transition-all">
                <input type="radio" name="lc_payment_method" value="cod" checked
                    class="accent-red-600 w-4 h-4 shrink-0">
                <div class="min-w-0">
                    <p class="font-bold text-gray-900 text-sm">ক্যাশ অন ডেলিভারি</p>
                    <p class="text-xs text-gray-500">পণ্য হাতে পেয়ে টাকা পরিশোধ করুন</p>
                </div>
            </label>
        </div>

        <div>
            <p class="text-sm font-bold text-gray-700 mb-2">প্রোমো কোড</p>
            <div
                class="flex items-center bg-white rounded-xl border border-gray-200 overflow-hidden focus-within:border-red-500 focus-within:ring-1 focus-within:ring-red-200 transition-all">
                <input id="lcCouponInput" type="text" placeholder="প্রোমো কোড থাকলে দিন"
                    class="bg-transparent flex-1 min-w-0 text-sm px-4 py-3 focus:outline-none text-gray-900 placeholder-gray-400 uppercase tracking-wider"
                    autocomplete="off">
                <button id="lcApplyCouponBtn" type="button"
                    class="bg-red-600 text-white px-4 py-3 text-sm font-bold hover:bg-red-700 transition-colors shrink-0">
                    Apply
                </button>
            </div>
            <p id="lcCouponMessage" class="hidden text-xs mt-2 font-medium"></p>
        </div>

        <div class="p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300 space-y-2 text-sm mt-4">
            <div class="flex justify-between">
                <span class="text-gray-500">পণ্যের মূল্য:</span>
                <span class="font-semibold">৳ <span id="lcSubtotal">0</span></span>
            </div>
            <div id="lcTierDiscountRow" class="hidden justify-between">
                <span class="text-gray-500">পরিমাণভিত্তিক ছাড়:</span>
                <span class="font-semibold text-green-600">-৳ <span id="lcTierDiscount">0</span></span>
            </div>
            <div id="lcLandingDiscountRow" class="hidden justify-between">
                <span class="text-gray-500">স্পেশাল ছাড়:</span>
                <span class="font-semibold text-green-600">-৳ <span id="lcLandingDiscount">0</span></span>
            </div>
            <div id="lcCouponDiscountRow" class="hidden justify-between">
                <span class="text-gray-500">কুপন ছাড় (<span id="lcCouponCode"></span>):</span>
                <span class="font-semibold text-green-600">-৳ <span id="lcCouponDiscount">0</span></span>
            </div>
            <div class="flex justify-between">
                <span>ডেলিভারি চার্জ:</span>
                <span id="lcShippingDisplay" class="text-red-600 font-bold">৳ 0</span>
            </div>
            <div class="flex justify-between border-t pt-2 mt-2 font-black text-lg text-red-600">
                <span>সর্বমোট:</span>
                <span>৳ <span id="lcGrandTotal">0</span></span>
            </div>
        </div>

        <div id="lcErrorMessage"
            class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-3"></div>

        <button id="lcPlaceOrderBtn" type="button" disabled
            class="w-full mt-4 py-5 bg-red-600 hover:bg-red-700 text-white font-bold text-xl rounded-2xl shadow-lg transition-all active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-3">
            <span id="lcSpinner" class="hidden">
                <svg class="animate-spin h-6 w-6 text-white" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </span>
            <span id="lcButtonText">অর্ডার কনফার্ম করুন</span>
            <svg id="lcCartIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
        </button>

        <div class="flex flex-wrap justify-center gap-4 mt-4 text-xs text-gray-500">
            <div class="flex items-center gap-1">
                <i class="fas fa-lock text-green-600"></i>
                <span>নিরাপদ অর্ডার</span>
            </div>
            <div class="flex items-center gap-1">
                <i class="fas fa-circle-check text-green-600"></i>
                <span>কোয়ালিটি গ্যারান্টি</span>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const slug = @json($landing->slug);
        const zoneIds = @json($zones->pluck('id')->values());
        const productName = @json($product->name ?? $landing->title);
        const itemCategory = @json($product->category->name ?? ($landing->type === \App\Domains\Landing\Models\LandingPage::TYPE_COMBO ? 'Combo' : 'Landing'));
        const state = {
            form: {
                customer_name: '',
                customer_phone: '',
                customer_email: '',
                address_line: '',
                area: '',
                city: '',
                zone_id: null,
                payment_method: 'cod',
                items: [],
                coupon_code: null,
                ga_client_id: null,
            },
            pricing: {
                subtotal: 0,
                tier_discount: 0,
                landing_discount: 0,
                coupon_discount: 0,
                coupon_code: null,
                shipping_cost: 0,
                grand_total: 0,
                free_delivery_applied: false,
            },
            couponInput: '',
            submitting: false,
            beginCheckoutFired: false,
            shippingInfoFired: false,
        };

        const $ = (id) => document.getElementById(id);

        function getFormValue(id) {
            return ($(id)?.value ?? '').trim();
        }

        function syncFormFromDom() {
            state.form.customer_name = getFormValue('lcCustomerName');
            state.form.customer_phone = getFormValue('lcCustomerPhone');
            state.form.customer_email = getFormValue('lcCustomerEmail');
            state.form.address_line = getFormValue('lcAddressLine');
            state.form.city = getFormValue('lcCity');
            state.couponInput = getFormValue('lcCouponInput');
        }

        function canSubmit() {
            syncFormFromDom();
            return state.form.customer_name !== '' &&
                state.form.customer_phone !== '' &&
                state.form.address_line !== '' &&
                state.form.zone_id &&
                state.form.items.length > 0;
        }

        function setText(id, value) {
            const el = $(id);
            if (el) el.textContent = value;
        }

        function toggleRow(id, show) {
            const el = $(id);
            if (!el) return;
            el.classList.toggle('hidden', !show);
            el.classList.toggle('flex', show);
        }

        function setError(message = '') {
            const el = $('lcErrorMessage');
            if (!el) return;
            el.textContent = message;
            el.classList.toggle('hidden', !message);
        }

        function setCouponMessage(message = '', isError = false) {
            const el = $('lcCouponMessage');
            if (!el) return;
            el.textContent = message;
            el.classList.toggle('hidden', !message);
            el.classList.toggle('text-red-500', isError);
            el.classList.toggle('text-green-600', !isError);
        }

        function renderZoneState() {
            document.querySelectorAll('.lc-zone-label').forEach((label) => {
                const input = label.querySelector('input[type="radio"]');
                const active = Number(input?.value) === Number(state.form.zone_id);
                label.classList.toggle('border-red-500', active);
                label.classList.toggle('bg-red-50/50', active);
                label.classList.toggle('border-gray-200', !active);
                label.classList.toggle('bg-white', !active);
            });
        }

        function renderPricing() {
            setText('lcSubtotal', Number(state.pricing.subtotal || 0).toFixed(0));
            setText('lcTierDiscount', Number(state.pricing.tier_discount || 0).toFixed(0));
            setText('lcLandingDiscount', Number(state.pricing.landing_discount || 0).toFixed(0));
            setText('lcCouponDiscount', Number(state.pricing.coupon_discount || 0).toFixed(0));
            setText('lcCouponCode', state.pricing.coupon_code || '');
            setText('lcGrandTotal', Number(state.pricing.grand_total || 0).toFixed(0));

            toggleRow('lcTierDiscountRow', Number(state.pricing.tier_discount || 0) > 0);
            toggleRow('lcLandingDiscountRow', Number(state.pricing.landing_discount || 0) > 0);
            toggleRow('lcCouponDiscountRow', Number(state.pricing.coupon_discount || 0) > 0);

            const shippingText = state.pricing.free_delivery_applied ?
                'ফ্রি' :
                `৳ ${Number(state.pricing.shipping_cost || 0).toFixed(0)}`;
            setText('lcShippingDisplay', shippingText);
        }

        function renderSubmitState() {
            const btn = $('lcPlaceOrderBtn');
            if (!btn) return;
            btn.disabled = state.submitting || !canSubmit();
            $('lcSpinner')?.classList.toggle('hidden', !state.submitting);
            $('lcCartIcon')?.classList.toggle('hidden', state.submitting);
            setText('lcButtonText', state.submitting ? 'প্রসেসিং...' : 'অর্ডার কনফার্ম করুন');
        }

        function ga4Items() {
            return state.form.items.map((item, index) => ({
                item_id: item.combo_id ? `combo_${item.combo_id}` : String(item.variant_id ?? ''),
                item_name: item.combo_id ? 'Landing Combo' : productName,
                item_variant: item.variant_id ? String(item.variant_id) : null,
                item_category: itemCategory,
                price: state.form.items.length === 1 ?
                    Number(state.pricing.subtotal || 0) / Number(item.quantity || 1) :
                    undefined,
                quantity: Number(item.quantity || 1),
                index,
            }));
        }

        function pushGa4(event, extra = {}) {
            const ecommerce = {
                currency: 'BDT',
                value: Number(state.pricing.grand_total || state.pricing.subtotal || 0),
                items: ga4Items(),
                ...extra,
            };

            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({ event, ecommerce });
        }

        function fireBeginCheckout() {
            if (state.beginCheckoutFired || !state.form.items.length || Number(state.pricing.subtotal || 0) <= 0) return;
            state.beginCheckoutFired = true;
            pushGa4('begin_checkout');
        }

        function fireShippingInfo() {
            if (state.shippingInfoFired || !state.form.zone_id || !state.form.items.length || Number(state.pricing.subtotal || 0) <= 0) return;
            state.shippingInfoFired = true;
            pushGa4('add_shipping_info', {
                shipping_tier: String(state.form.zone_id),
            });
        }

        async function refreshPreview() {
            if (!state.form.zone_id || state.form.items.length === 0) {
                renderSubmitState();
                return;
            }

            try {
                const res = await fetch(`/api/v1/landing/${slug}/preview`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        items: state.form.items,
                        zone_id: state.form.zone_id,
                        coupon_code: state.form.coupon_code,
                    }),
                });
                const json = await res.json();
                if (json.success && json.data) {
                    state.pricing = json.data;
                    setError('');
                    renderPricing();
                    fireBeginCheckout();
                    fireShippingInfo();
                } else {
                    setError(json.message || 'মূল্য হিসাব করতে সমস্যা হয়েছে।');
                }
            } catch (e) {
                console.error('Preview error:', e);
            } finally {
                renderSubmitState();
            }
        }

        function updateItems(items) {
            state.form.items = (items || []).filter((item) => Number(item.quantity || 0) > 0);
            refreshPreview();
        }

        async function applyCoupon() {
            syncFormFromDom();
            if (!state.couponInput) return;
            state.form.coupon_code = state.couponInput.toUpperCase();
            setCouponMessage('');
            await refreshPreview();

            if (Number(state.pricing.coupon_discount || 0) > 0) {
                setCouponMessage(`Coupon "${state.pricing.coupon_code}" applied! You save ৳${Number(state.pricing.coupon_discount).toFixed(0)}`);
            } else if (state.pricing.coupon_code) {
                setCouponMessage('Coupon applied but no discount on current items.', true);
            } else {
                state.form.coupon_code = null;
                setCouponMessage('Invalid or expired coupon code.', true);
            }
        }

        async function placeOrder() {
            if (!canSubmit() || state.submitting) return;

            if (!state.form.zone_id) {
                $('lcZoneError')?.classList.remove('hidden');
                $('lcDeliveryZoneSection')?.classList.add('ring-2', 'ring-red-400');
                return;
            }

            state.submitting = true;
            setError('');
            renderSubmitState();
            fireBeginCheckout();
            fireShippingInfo();

            try {
                const res = await fetch(`/api/v1/landing/${slug}/checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(state.form),
                });
                const json = await res.json();

                if (json.success && json.data?.redirect_url) {
                    window.location.href = json.data.redirect_url;
                    return;
                }

                setError(json.message || 'অর্ডার সম্পন্ন করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
            } catch (e) {
                setError('নেটওয়ার্কজনিত সমস্যা হয়েছে। আবার চেষ্টা করুন।');
            } finally {
                state.submitting = false;
                renderSubmitState();
            }
        }

        function init() {
            if (typeof window.initialItems !== 'undefined') {
                state.form.items = JSON.parse(JSON.stringify(window.initialItems));
            }

            const gaMatch = document.cookie.match(/_ga=GA\d+\.\d+\.(.+?)(?:;|$)/);
            state.form.ga_client_id = gaMatch ? gaMatch[1] : null;

            if (zoneIds.length === 1) {
                state.form.zone_id = zoneIds[0];
                const input = document.querySelector(`input[name="lc_zone_id"][value="${zoneIds[0]}"]`);
                if (input) input.checked = true;
                renderZoneState();
                refreshPreview();
            }

            ['lcCustomerName', 'lcCustomerPhone', 'lcAddressLine', 'lcCustomerEmail', 'lcCity'].forEach((id) => {
                $(id)?.addEventListener('input', renderSubmitState);
            });

            document.querySelectorAll('input[name="lc_zone_id"]').forEach((input) => {
                input.addEventListener('change', () => {
                    state.form.zone_id = Number(input.value);
                    $('lcZoneError')?.classList.add('hidden');
                    $('lcDeliveryZoneSection')?.classList.remove('ring-2', 'ring-red-400');
                    renderZoneState();
                    refreshPreview();
                });
            });

            $('lcApplyCouponBtn')?.addEventListener('click', applyCoupon);
            $('lcCouponInput')?.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    applyCoupon();
                }
            });
            $('lcPlaceOrderBtn')?.addEventListener('click', placeOrder);

            renderPricing();
            renderSubmitState();

            window.LandingCheckout = {
                updateItems,
                refreshPreview,
            };
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
