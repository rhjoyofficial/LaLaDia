<?php

namespace Database\Seeders;

use App\Domains\Coupon\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10,
                'min_purchase' => 500,
                'usage_limit' => 5000,
                'limit_per_user' => 1,
                'start_date' => now()->subDays(7),
                'end_date' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'SAVE120',
                'type' => 'fixed',
                'value' => 120,
                'min_purchase' => 1200,
                'usage_limit' => 1000,
                'limit_per_user' => 2,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addMonths(1),
                'is_active' => true,
            ],
            [
                'code' => 'RAMADAN25',
                'type' => 'percentage',
                'value' => 25,
                'min_purchase' => 2000,
                'usage_limit' => 300,
                'limit_per_user' => 1,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(20),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(
                ['code' => $coupon['code']],
                $coupon
            );
        }

        // HM25 — 6% off Himsagar Mango 22-26KG (SKU: HIM-25KG) only
        $himsagarVariant = \App\Domains\Product\Models\ProductVariant::where('sku', 'HIM-25KG')->first();

        if ($himsagarVariant) {
            $hm25 = Coupon::updateOrCreate(
                ['code' => 'HM25'],
                [
                    'type'           => 'percentage',
                    'value'          => 6,
                    'min_purchase'   => null,
                    'usage_limit'    => null,
                    'limit_per_user' => 1,
                    'start_date'     => '2026-05-25 00:00:00',
                    'end_date'       => '2026-06-03 23:59:00',
                    'is_active'      => true,
                    'applies_to'     => 'products',
                ]
            );

            $hm25->productVariantScopes()->syncWithoutDetaching([$himsagarVariant->id]);
        } else {
            $this->command->warn('HM25 coupon skipped: Himsagar Mango 25kg variant not found.');
        }
    }
}
