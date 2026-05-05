<?php

namespace Database\Seeders;

use App\Domains\Product\Models\Combo;
use App\Domains\Product\Models\ComboItem;
use App\Domains\Product\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class ComboSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Combo::truncate();
        ComboItem::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Pickle Duo Combo (Hilsa + Beef Pickle)
        $beefPickle = ProductVariant::where('sku', 'PIC-BEEF-V1')->first();
        $hilsaPickle = ProductVariant::where('sku', 'PIC-HIL-V1')->first();

        if ($beefPickle && $hilsaPickle) {
            Combo::create([
                'title' => 'Pickle Duo Combo',
                'slug' => 'pickle-duo-combo',
                'description' => 'The ultimate pickle lover’s duo: Royal Beef Pickle and Hilsa Fish Pickle. A flavorful blast for your taste buds.',
                'image' => 'combos/pickle-duo.jpg',
                'pricing_mode' => 'manual',
                'manual_price' => 1999,
                'is_active' => true,
                'is_featured' => true,
                'has_free_delivery' => true,
            ])->items()->createMany([
                ['product_variant_id' => $beefPickle->id, 'quantity' => 1],
                ['product_variant_id' => $hilsaPickle->id, 'quantity' => 1],
            ]);
        }

        // 2. Breakfast Delight (Honey + Ghee)
        $honeyVariant = ProductVariant::where('sku', 'HON-MAN-V1')->first();
        $gheeVariant = ProductVariant::where('sku', 'GHE-ROY-V1')->first();

        if ($honeyVariant && $gheeVariant) {
            $breakfast = Combo::create([
                'title' => 'Breakfast Delight Combo',
                'slug' => 'breakfast-delight-combo',
                'description' => 'Start your day with the pure energy of Mangrove Honey and Royal Essence Ghee. A perfect duo for health and taste.',
                'image' => 'combos/breakfast-delight.jpg',
                'pricing_mode' => 'manual',
                'manual_price' => 1750,
                'is_active' => true,
                'is_featured' => true,
            ]);

            $breakfast->items()->createMany([
                ['product_variant_id' => $honeyVariant->id, 'quantity' => 1],
                ['product_variant_id' => $gheeVariant->id, 'quantity' => 1],
            ]);
        }

        // 3. Traditional Bengali Feast (Pickle Duo + Shutki)
        $beefPickle = ProductVariant::where('sku', 'PIC-BEEF-V1')->first();
        $hilsaPickle = ProductVariant::where('sku', 'PIC-HIL-V1')->first();
        $loittaShutki = ProductVariant::where('sku', 'loitta-shutki-125G')->first();

        if ($beefPickle && $hilsaPickle && $loittaShutki) {
            $feast = Combo::create([
                'title' => 'Traditional Bengali Feast',
                'slug' => 'traditional-bengali-feast',
                'description' => 'A curated collection of our best pickles and premium dry fish. Authentic taste guaranteed.',
                'image' => 'combos/bengali-feast.jpg',
                'pricing_mode' => 'manual',
                'manual_price' => 2100,
                'is_active' => true,
                'is_featured' => true,
            ]);

            $feast->items()->createMany([
                ['product_variant_id' => $beefPickle->id, 'quantity' => 1],
                ['product_variant_id' => $hilsaPickle->id, 'quantity' => 1],
                ['product_variant_id' => $loittaShutki->id, 'quantity' => 1],
            ]);
        }

        // 4. Mango Summer Special (Amrapali + Himsagar)
        $amrapali = ProductVariant::where('sku', 'LIKE', 'amrapali%5KG')->first();
        $himsagar = ProductVariant::where('sku', 'LIKE', 'himsagar%5KG')->first();

        if ($amrapali && $himsagar) {
            $summer = Combo::create([
                'title' => 'King of Summer Mango Combo',
                'slug' => 'king-of-summer-mango-combo',
                'description' => '5KG of Himsagar and 5KG of Amrapali. The ultimate treat for mango lovers.',
                'image' => 'combos/mango-combo.jpg',
                'pricing_mode' => 'manual',
                'manual_price' => 1100,
                'is_active' => true,
            ]);

            $summer->items()->createMany([
                ['product_variant_id' => $amrapali->id, 'quantity' => 1],
                ['product_variant_id' => $himsagar->id, 'quantity' => 1],
            ]);
        }
    }
}
