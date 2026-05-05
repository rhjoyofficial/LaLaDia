<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Shipping\Models\ShippingZone;

class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Inside Dhaka (ঢাকা সিটি)',
                'base_charge' => 60.00,
                'free_shipping_threshold' => 2000.00,
                'estimated_days' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Dhaka Suburbs (ঢাকার আশেপাশে)',
                'base_charge' => 90.00,
                'free_shipping_threshold' => 2000.00,
                'estimated_days' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Outside Dhaka (সারাদেশ)',
                'base_charge' => 120.00,
                'free_shipping_threshold' => 2000.00,
                'estimated_days' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($zones as $zone) {
            ShippingZone::updateOrCreate(
                ['name' => $zone['name']],
                $zone
            );
        }
    }
}
