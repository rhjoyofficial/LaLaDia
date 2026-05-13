<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed</title>
    <style>
        body { margin: 0; padding: 0; background: #FDFAF4; font-family: 'Helvetica Neue', Arial, sans-serif; color: #1a1a1a; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background: #1a5c2e; padding: 36px 32px; text-align: center; }
        .header h1 { margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; }
        .header p { margin: 6px 0 0; color: rgba(255,255,255,0.75); font-size: 13px; }
        .order-num { display: inline-block; margin-top: 12px; background: rgba(255,255,255,0.15); color: #ffffff; font-size: 12px; font-weight: 600; letter-spacing: 0.08em; padding: 4px 12px; border-radius: 999px; font-family: monospace; }
        .body { padding: 28px 32px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; margin: 0 0 12px; }
        .address-box { background: #f9fafb; border-radius: 10px; padding: 14px 16px; margin-bottom: 24px; }
        .address-box p { margin: 3px 0; font-size: 14px; color: #374151; }
        .address-box .name { font-weight: 600; color: #111827; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        table.items th { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; padding: 0 0 8px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        table.items th:last-child, table.items td:last-child { text-align: right; }
        table.items td { padding: 10px 0; font-size: 13px; color: #374151; border-bottom: 1px solid #F5EDD8; vertical-align: top; }
        table.items td .item-name { font-weight: 600; color: #111827; }
        table.items td .item-variant { font-size: 12px; color: #9ca3af; margin-top: 2px; }
        .totals { background: #f9fafb; border-radius: 10px; padding: 14px 16px; margin-bottom: 24px; }
        .totals-row { display: flex; justify-content: space-between; font-size: 13px; color: #6b7280; padding: 4px 0; }
        .totals-row.grand { font-size: 15px; font-weight: 700; color: #111827; border-top: 1px solid #e5e7eb; margin-top: 8px; padding-top: 12px; }
        .totals-row .value { font-weight: 600; color: #111827; }
        .totals-row.discount .value { color: #16a34a; }
        .cod-notice { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 16px; margin-bottom: 24px; font-size: 13px; color: #92400e; }
        .cod-notice strong { display: block; margin-bottom: 4px; color: #78350f; }
        .cta { text-align: center; margin: 28px 0 8px; }
        .cta a { display: inline-block; background: #1a5c2e; color: #ffffff !important; text-decoration: none; padding: 13px 32px; border-radius: 999px; font-size: 14px; font-weight: 700; }
        .footer { text-align: center; padding: 20px 32px 28px; font-size: 12px; color: #9ca3af; }
        .footer a { color: #16a34a; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- Header --}}
    <div class="header">
        <h1>Order Confirmed!</h1>
        <p>Thank you for shopping with {{ config('app.name') }}</p>
        <span class="order-num">#{{ $order->order_number }}</span>
    </div>

    <div class="body">

        {{-- Delivery address --}}
        @if ($order->shippingAddress)
        <p class="section-title">Delivering to</p>
        <div class="address-box">
            <p class="name">{{ $order->shippingAddress->customer_name }}</p>
            <p>{{ $order->shippingAddress->customer_phone }}</p>
            <p>{{ collect([$order->shippingAddress->address_line, $order->shippingAddress->city])->filter()->join(', ') }}</p>
        </div>
        @endif

        {{-- Items --}}
        @if ($order->items->count())
        <p class="section-title">Items Ordered</p>
        <table class="items">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                @php
                    $isGift  = $item->discount_type_snapshot === 'Free Gift';
                    $isCombo = !is_null($item->combo_id);
                    $displayName = $item->combo_name_snapshot ?: $item->product_name_snapshot;
                    $mrp     = (float) ($item->original_unit_price ?? $item->unit_price);
                    $unitDiscount = max(0, $mrp - (float) $item->unit_price);
                    $hasDiscount  = $unitDiscount > 0.001;
                @endphp
                <tr style="{{ $isGift ? 'background:#f0fdf4;' : '' }}">
                    <td>
                        <div class="item-name">
                            @if ($isGift)
                                <span style="display:inline-block;background:#16a34a;color:#fff;font-size:9px;font-weight:700;padding:1px 6px;border-radius:999px;text-transform:uppercase;margin-right:4px;">Gift</span>
                            @elseif ($isCombo)
                                <span style="display:inline-block;background:#1a5c2e20;color:#1a5c2e;font-size:9px;font-weight:700;padding:1px 6px;border-radius:999px;text-transform:uppercase;margin-right:4px;">Bundle</span>
                            @endif
                            {{ $displayName }}
                        </div>
                        @if (!$isCombo && $item->variant_title_snapshot && $item->variant_title_snapshot !== 'Bundle')
                            <div class="item-variant">{{ $item->variant_title_snapshot }}</div>
                        @endif
                        @if ($hasDiscount && !$isGift)
                            <div style="font-size:11px;color:#16a34a;margin-top:3px;">✓ Saving ৳{{ number_format($unitDiscount, 2) }}/unit</div>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        @if ($isGift)
                            <span style="color:#16a34a;font-weight:600;">Free Gift</span>
                        @elseif ($hasDiscount)
                            <span style="text-decoration:line-through;color:#9ca3af;font-size:11px;">৳{{ number_format($mrp * $item->quantity, 2) }}</span><br>
                            <span style="font-weight:700;">৳{{ number_format($item->total_price, 2) }}</span>
                        @else
                            ৳{{ number_format($item->total_price, 2) }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Totals --}}
        <div class="totals">
            <div class="totals-row">
                <span>Subtotal</span>
                <span class="value">৳{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if ($order->discount_total > 0)
            <div class="totals-row discount">
                <span>Discount (Tier + Coupon)</span>
                <span class="value">−৳{{ number_format($order->discount_total, 2) }}</span>
            </div>
            @endif
            <div class="totals-row">
                <span>Shipping</span>
                @if ($order->shipping_cost == 0)
                    <span class="value" style="color:#16a34a;">🚚 Free Shipping</span>
                @else
                    <span class="value">৳{{ number_format($order->shipping_cost, 2) }}</span>
                @endif
            </div>
            <div class="totals-row grand">
                <span>Total</span>
                <span>৳{{ number_format($order->grand_total, 2) }}</span>
            </div>
        </div>

        {{-- COD notice --}}
        @if ($order->payment_method === 'cod')
        <div class="cod-notice">
            <strong>Cash on Delivery</strong>
            Please have ৳{{ number_format($order->grand_total, 2) }} ready when your order arrives.
        </div>
        @endif

        {{-- CTA --}}
        <div class="cta">
            <a href="{{ route('order.success', ['order' => $order->order_number]) }}">View Order Details</a>
        </div>

    </div>

    <div class="footer">
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p>You received this email because you placed an order with us.</p>
    </div>

</div>
</body>
</html>










