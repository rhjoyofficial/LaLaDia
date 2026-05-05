<?php

namespace Database\Seeders;

use App\Domains\Category\Models\Category;
use App\Domains\Product\Models\Product;
use App\Domains\Certification\Models\Certification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Product::truncate();
        \App\Domains\Product\Models\ProductVariant::truncate();
        \App\Domains\Product\Models\ProductTierPrice::truncate();
        \Illuminate\Support\Facades\DB::table('certification_product')->truncate();
        Schema::enableForeignKeyConstraints();

        $categories = Category::all()->keyBy('slug');
        $certs = Certification::all();

        // Helper to grab category ID
        $getCatId = fn($slug) => $categories[$slug]->id ?? Category::first()->id;

        // 1. Mangrove Gold Honey
        $honey = Product::create([
            'category_id' => $getCatId('honey'),
            'name' => 'Mangrove Gold Honey (ম্যানগ্রোভ গোল্ড হানি)',
            'slug' => Str::slug('Mangrove Gold Honey'),
            'base_price' => 990,
            'sku' => 'HON-MAN-500G',
            'thumbnail' => 'products/honey-mangrove.jpg',
            'short_description' => '100% natural raw honey collected from the Sundarbans mangrove forest.',
            'description' => 'Our Mangrove Gold Honey is harvested from the heart of the Sundarbans, the world’s largest mangrove forest. It is 100% natural, raw, and unfiltered, preserving all beneficial antioxidants and enzymes. No added preservatives, artificial flavors, or heat treatments are used, ensuring you get the pure essence of nature in every drop.',
            'is_active' => true,
            'is_featured' => true,
            'is_trending' => true,
            'nutritional_info' => [
                'energy' => '304 kcal',
                'carbohydrates' => '82.4g',
                'sugars' => '82.1g',
                'protein' => '0.3g',
                'minerals' => 'Rich in Potassium, Magnesium, and Iron',
            ],
            'is_landing_enabled' => true,
            'landing_slug' => 'sundarbans-mangrove-honey',
        ]);
        $honey->variants()->create([
            'title' => '500gm',
            'sku' => 'HON-MAN-V1',
            'price' => 990,
            'stock' => 500,
            'weight_grams' => 500,
            'is_active' => true,
        ]);
        $honey->certifications()->attach($certs->pluck('id'));
        $honey->variants->first()->tierPrices()->create([
            'min_quantity' => 2,
            'discount_type' => 'fixed',
            'discount_value' => 60,
        ]);

        // 2. Royal Essence Ghee
        $ghee = Product::create([
            'category_id' => $getCatId('ghee'),
            'name' => 'Royal Essence Ghee (রয়্যাল এসেন্স ঘি)',
            'slug' => Str::slug('Royal Essence Ghee'),
            'base_price' => 870,
            'sku' => 'GHE-ROY-350G',
            'thumbnail' => 'products/ghee-royal.jpg',
            'short_description' => 'Pure cow milk ghee prepared in small batches for premium quality.',
            'description' => 'Royal Essence Ghee is prepared using traditional methods from pure cow milk cream. Produced in small batches to maintain absolute quality and aroma. It is completely natural, free from additives, MSG, or preservatives. Perfect for traditional Bengali dishes and health-conscious diets.',
            'is_active' => true,
            'is_featured' => true,
            'is_trending' => true,
            'nutritional_info' => [
                'fat' => '99.9g',
                'saturated_fat' => '65g',
                'vitamin_a' => '3000 IU',
                'vitamin_e' => '2.5mg',
            ],
        ]);
        $ghee->variants()->create([
            'title' => '350gm',
            'sku' => 'GHE-ROY-V1',
            'price' => 1050,
            'discount_type' => 'fixed',
            'discount_value' => 180, // 15% off
            'stock' => 300,
            'weight_grams' => 350,
            'is_active' => true,
        ])->tierPrices()->create([
            'min_quantity' => 2,
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'has_free_delivery' => true,
        ]);
        $ghee->certifications()->attach($certs->where('name', '!=', 'Halal Certified')->pluck('id'));

        // 3. Royal Beef Pickle
        $beefPickle = Product::create([
            'category_id' => $getCatId('pickles'),
            'name' => 'Royal Beef Pickle (রয়্যাল বিফ আচার)',
            'slug' => Str::slug('Royal Beef Pickle'),
            'base_price' => 999,
            'sku' => 'PIC-BEEF-400G',
            'thumbnail' => 'products/pickle-beef.jpg',
            'short_description' => 'Premium beef pickle made with cold-pressed mustard oil and hand-selected spices.',
            'description' => 'Our Royal Beef Pickle is a masterpiece of flavor. We use premium quality beef chunks, authentic spices, and pure cold-pressed mustard oil. Natural vinegar is used for preservation, ensuring a long shelf life without chemical additives. A spicy, tangy delight for any meal.',
            'is_active' => true,
            'is_trending' => true,
        ]);
        $beefPickle->variants()->create([
            'title' => '400gm',
            'sku' => 'PIC-BEEF-V1',
            'price' => 1350,
            'discount_type' => 'fixed',
            'discount_value' => 351, // ~26% off to hit a psychological price
            'stock' => 200,
            'weight_grams' => 400,
            'is_active' => true,
        ])->tierPrices()->create([
            'min_quantity' => 2,
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'has_free_delivery' => true,
        ]);
        $beefPickle->certifications()->attach($certs->pluck('id'));

        // 4. Hilsa Fish Pickle
        $hilsaPickle = Product::create([
            'category_id' => $getCatId('pickles'),
            'name' => 'Hilsa Fish Pickle (ইলিশ মাছের আচার)',
            'slug' => Str::slug('Hilsa Fish Pickle'),
            'base_price' => 999,
            'sku' => 'PIC-HIL-400G',
            'thumbnail' => 'products/pickle-hilsa.jpg',
            'short_description' => 'Authentic river Hilsa fish pickle prepared with traditional hygiene standards.',
            'description' => 'Taste the heritage of Bengal with our Hilsa Fish Pickle. Sourced from the finest river Hilsa, this pickle preserves the traditional taste using modern hygiene standards. It’s rich, flavorful, and pairs perfectly with steamed rice or khichuri.',
            'is_active' => true,
            'is_trending' => true,
        ]);
        $hilsaPickle->variants()->create([
            'title' => '400gm',
            'sku' => 'PIC-HIL-V1',
            'price' => 1350,
            'discount_type' => 'fixed',
            'discount_value' => 351,
            'stock' => 150,
            'weight_grams' => 400,
            'is_active' => true,
        ])->tierPrices()->create([
            'min_quantity' => 2,
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'has_free_delivery' => true,
        ]);
        $hilsaPickle->certifications()->attach($certs->pluck('id'));

        // 5. Dry Fish Items
        $dryFishes = [
            ['name' => 'Loitta Shutki', 'price_125' => 260, 'price_500' => 990, 'price_1000' => 1850],
            ['name' => 'Churi Shutki', 'price_125' => 360, 'price_500' => 1350, 'price_1000' => 2500],
            ['name' => 'Modhu Faisa', 'price_125' => 240, 'price_500' => 890, 'price_1000' => 1650],
            ['name' => 'Mowrala Kachki', 'price_125' => 280, 'price_500' => 1050, 'price_1000' => 1950],
        ];

        foreach ($dryFishes as $df) {
            $product = Product::create([
                'category_id' => $getCatId('dry-fish'),
                'name' => $df['name'],
                'slug' => Str::slug($df['name']),
                'base_price' => $df['price_125'],
                'thumbnail' => 'products/dry-fish-' . Str::slug($df['name']) . '.jpg',
                'short_description' => 'Premium quality sun-dried ' . strtolower($df['name']) . ' from coastal regions.',
                'description' => 'Our ' . $df['name'] . ' is sourced directly from the coastal drying yards. It is sun-dried naturally, ensuring authentic taste and aroma. We maintain strict hygiene during processing to provide you with the best quality shutki.',
                'is_active' => true,
                'is_trending' => in_array($df['name'], ['Loitta Shutki', 'Churi Shutki']),
            ]);

            $product->variants()->createMany([
                ['title' => '125gm', 'sku' => Str::slug($df['name']) . '-125G', 'price' => $df['price_125'], 'stock' => 1000, 'weight_grams' => 125, 'is_active' => true],
                ['title' => '500gm', 'sku' => Str::slug($df['name']) . '-500G', 'price' => $df['price_500'], 'stock' => 500, 'weight_grams' => 500, 'is_active' => true],
                ['title' => '1KG', 'sku' => Str::slug($df['name']) . '-1KG', 'price' => $df['price_1000'], 'stock' => 200, 'weight_grams' => 1000, 'is_active' => true],
            ]);
            $product->certifications()->attach($certs->whereIn('name', ['Halal Certified', 'GMP Quality Standards'])->pluck('id'));
        }

        // 6. Mango Products
        $mangoes = [
            [
                'name' => 'Himsagar Mango (হিমসাগর আম)',
                'slug' => 'himsagar-mango',
                'image' => 'himsagar.jpg',
                'price' => 100,
                'desc' => 'The king of Bengal mangoes, famous for its sweet aroma and fiber-less pulp. Sourced directly from Satkhira/Rajshahi.'
            ],
            [
                'name' => 'Harivanga Mango (হাঁড়ি ভাঙ্গা আম)',
                'slug' => 'harivanga-mango',
                'image' => 'harivanga.jpg',
                'price' => 90,
                'desc' => 'A specialty of Rangpur, known for its distinct fiber-less texture and incredibly sweet taste.'
            ],
            [
                'name' => 'Langra Mango (ল্যাংড়া আম)',
                'slug' => 'langra-mango',
                'image' => 'langra.jpg',
                'price' => 95,
                'desc' => 'Famous for its unique aroma, thin skin, and melt-in-the-mouth texture. A favorite aromatic variety.'
            ],
            [
                'name' => 'Amrapali Mango (আমরুপালি আম)',
                'slug' => 'amrapali-mango',
                'image' => 'amrapali.jpg',
                'price' => 85,
                'desc' => 'Exceptionally sweet and deep orange in color. These are high in pulp and rich in flavor.'
            ],
            [
                'name' => 'Banana Mango (ব্যানানা আম)',
                'slug' => 'banana-mango',
                'image' => 'banana-mango.jpg',
                'price' => 150,
                'desc' => 'A premium elongated variety that looks like a banana. It has a tiny seed and thick, sweet pulp.'
            ],
            [
                'name' => 'Gourmati Mango (গোড়মতি আম)',
                'slug' => 'gourmati-mango',
                'image' => 'gourmati.jpg',
                'price' => 200,
                'desc' => 'A late-season premium variety. It is extremely sweet, has a long shelf life, and is highly sought after.'
            ],
        ];

        foreach ($mangoes as $m) {
            $product = Product::create([
                'category_id' => $getCatId('fruits'),
                'name' => $m['name'],
                'slug' => $m['slug'],
                'base_price' => $m['price'],
                'thumbnail' => 'products/' . $m['image'],
                'short_description' => 'Fresh, carbide-free premium ' . $m['name'] . ' direct from the orchards.',
                'description' => $m['desc'] . ' Our mangoes are picked at the perfect ripeness and delivered fresh. We guarantee carbide-free and chemical-free products for your safety and enjoyment.',
                'is_active' => true,
                'is_featured' => true,
                'is_trending' => true,
            ]);

            // 1KG Variant
            $v1kg = $product->variants()->create([
                'title' => '1KG',
                'sku' => strtoupper($m['slug']) . '-1KG',
                'price' => $m['price'],
                'stock' => 5000,
                'weight_grams' => 1000,
                'is_active' => true
            ]);

            // 5KG Variant (approx 5% discount)
            $v5kg = $product->variants()->create([
                'title' => '5KG',
                'sku' => strtoupper($m['slug']) . '-5KG',
                'price' => round($m['price'] * 5 * 0.95),
                'stock' => 1000,
                'weight_grams' => 5000,
                'is_active' => true
            ]);

            // 10KG Variant (approx 10% discount)
            $v10kg = $product->variants()->create([
                'title' => '10KG',
                'sku' => strtoupper($m['slug']) . '-10KG',
                'price' => round($m['price'] * 10 * 0.90),
                'stock' => 500,
                'weight_grams' => 10000,
                'is_active' => true
            ]);

            // Tier Price & Free Delivery for 5KG (1+ qty)
            $v5kg->tierPrices()->create([
                'min_quantity' => 1,
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'has_free_delivery' => true,
            ]);

            // Tier Price & Free Delivery for 10KG (1+ qty)
            $v10kg->tierPrices()->create([
                'min_quantity' => 1,
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'has_free_delivery' => true,
            ]);

            $product->certifications()->attach($certs->pluck('id'));
        }

        // ── QA Scenario: Ghee with zone-restricted free delivery ────────────
        // Tests the "free_delivery_zones" constraint path in CheckoutPricingService.
        $gheeVariant = $ghee->variants->first();
        if ($gheeVariant) {
            $firstZoneId = \App\Domains\Shipping\Models\ShippingZone::orderBy('id')->value('id');
            if ($firstZoneId) {
                $gheeVariant->tierPrices()->create([
                    'min_quantity'        => 3,
                    'discount_type'       => 'percentage',
                    'discount_value'      => 8,
                    'has_free_delivery'   => true,
                    // Free delivery only for the first zone — all others still pay shipping
                    'free_delivery_zones' => [$firstZoneId],
                ]);
            }
        }
    }
}
