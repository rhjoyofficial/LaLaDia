<?php

namespace Database\Seeders;

use App\Domains\Category\Models\Category;
use App\Domains\Certification\Models\Certification;
use App\Domains\Product\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
        if (Certification::doesntExist()) {
            $this->call(CertificationSeeder::class);
        }

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
            'is_landing_enabled' => false,
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
            'is_landing_enabled' => true,
            'landing_slug' => 'royal-essence-ghee',
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
            'min_quantity' => 3,
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'has_free_delivery' => true,
        ]);
        $ghee->certifications()->attach($certs->whereIn('name', ['Halal Certified', 'GMP Quality Standards', 'ISO 22000', 'HACCP Certified'])->pluck('id'));

        // 3. Dry Fish (Shutki) Items
        // ---------------------------------------------------------------------

        // 3a. Loitta Shutki
        $loitta = Product::create([
            'category_id' => $getCatId('dry-fish'),
            'name' => 'Loitta Shutki (লইট্টা শুটকি)',
            'slug' => 'loitta-shutki',
            'base_price' => 260,
            'thumbnail' => 'products/dry-fish-loitta-shutki.jpg',
            'short_description' => 'Premium quality sun-dried loitta shutki from coastal regions.',
            'description' => 'Our Loitta Shutki is sourced directly from the coastal drying yards. It is sun-dried naturally, ensuring authentic taste and aroma.',
            'is_active' => true,
            'is_trending' => true,
        ]);
        $vLoitta125 = $loitta->variants()->create(['title' => '125gm', 'sku' => 'loitta-125G', 'price' => 260, 'stock' => 1000, 'weight_grams' => 125, 'is_active' => true]);
        $loitta->variants()->create(['title' => '500gm', 'sku' => 'loitta-500G', 'price' => 990, 'stock' => 500, 'weight_grams' => 500, 'is_active' => true]);
        $vLoitta1k = $loitta->variants()->create(['title' => '1KG', 'sku' => 'loitta-1KG', 'price' => 1850, 'stock' => 200, 'weight_grams' => 1000, 'is_active' => true]);
        $loitta->certifications()->attach($certs->whereIn('name', ['Halal Certified', 'GMP Quality Standards'])->pluck('id'));

        // 3b. Churi Shutki
        $churi = Product::create([
            'category_id' => $getCatId('dry-fish'),
            'name' => 'Churi Shutki (ছুরি শুটকি)',
            'slug' => 'churi-shutki',
            'base_price' => 360,
            'thumbnail' => 'products/dry-fish-churi-shutki.jpg',
            'short_description' => 'Premium quality sun-dried churi shutki from coastal regions.',
            'description' => 'Our Churi Shutki is sourced directly from the coastal drying yards. It is sun-dried naturally, ensuring authentic taste and aroma.',
            'is_active' => true,
            'is_trending' => true,
        ]);
        $vChuri125 = $churi->variants()->create(['title' => '125gm', 'sku' => 'churi-125G', 'price' => 360, 'stock' => 1000, 'weight_grams' => 125, 'is_active' => true]);
        $churi->variants()->create(['title' => '500gm', 'sku' => 'churi-500G', 'price' => 1350, 'stock' => 500, 'weight_grams' => 500, 'is_active' => true]);
        $vChuri1k = $churi->variants()->create(['title' => '1KG', 'sku' => 'churi-1KG', 'price' => 2500, 'stock' => 200, 'weight_grams' => 1000, 'is_active' => true]);
        $churi->certifications()->attach($certs->whereIn('name', ['Halal Certified', 'GMP Quality Standards'])->pluck('id'));

        // 3c. Modhu Faisa
        $faisa = Product::create([
            'category_id' => $getCatId('dry-fish'),
            'name' => 'Modhu Faisa (মধু ফাইস্যা)',
            'slug' => 'modhu-faisa',
            'base_price' => 240,
            'thumbnail' => 'products/dry-fish-modhu-faisa.jpg',
            'short_description' => 'Premium quality sun-dried modhu faisa from coastal regions.',
            'description' => 'Our Modhu Faisa is sourced directly from the coastal drying yards. It is sun-dried naturally, ensuring authentic taste and aroma.',
            'is_active' => true,
        ]);
        $vFaisa125 = $faisa->variants()->create(['title' => '125gm', 'sku' => 'faisa-125G', 'price' => 240, 'stock' => 1000, 'weight_grams' => 125, 'is_active' => true]);
        $faisa->variants()->create(['title' => '500gm', 'sku' => 'faisa-500G', 'price' => 890, 'stock' => 500, 'weight_grams' => 500, 'is_active' => true]);
        $vFaisa1k = $faisa->variants()->create(['title' => '1KG', 'sku' => 'faisa-1KG', 'price' => 1650, 'stock' => 200, 'weight_grams' => 1000, 'is_active' => true]);
        $faisa->certifications()->attach($certs->whereIn('name', ['Halal Certified', 'GMP Quality Standards'])->pluck('id'));

        // 3d. Mowrala Kachki
        $kachki = Product::create([
            'category_id' => $getCatId('dry-fish'),
            'name' => 'Mowrala Kachki (মওরালা কাচকি)',
            'slug' => 'mowrala-kachki',
            'base_price' => 280,
            'thumbnail' => 'products/dry-fish-mowrala-kachki.jpg',
            'short_description' => 'Premium quality sun-dried mowrala kachki from coastal regions.',
            'description' => 'Our Mowrala Kachki is sourced directly from the coastal drying yards. It is sun-dried naturally, ensuring authentic taste and aroma.',
            'is_active' => true,
        ]);
        $vKachki125 = $kachki->variants()->create(['title' => '125gm', 'sku' => 'kachki-125G', 'price' => 280, 'stock' => 1000, 'weight_grams' => 125, 'is_active' => true]);
        $kachki->variants()->create(['title' => '500gm', 'sku' => 'kachki-500G', 'price' => 1050, 'stock' => 500, 'weight_grams' => 500, 'is_active' => true]);
        $vKachki1k = $kachki->variants()->create(['title' => '1KG', 'sku' => 'kachki-1KG', 'price' => 1950, 'stock' => 200, 'weight_grams' => 1000, 'is_active' => true]);
        $kachki->certifications()->attach($certs->whereIn('name', ['Halal Certified', 'GMP Quality Standards'])->pluck('id'));

        // ---------------------------------------------------------------------
        // Shutki Tier Assignments
        // ---------------------------------------------------------------------
        // $vLoitta1k->tierPrices()->create(['min_quantity' => 1, 'discount_type' => 'fixed', 'discount_value' => 0, 'gift_product_variant_id' => $vChuri125->id, 'gift_quantity' => 1]);
        // $vChuri1k->tierPrices()->create(['min_quantity' => 1, 'discount_type' => 'fixed', 'discount_value' => 0, 'gift_product_variant_id' => $vFaisa125->id, 'gift_quantity' => 1]);
        // $vFaisa1k->tierPrices()->create(['min_quantity' => 1, 'discount_type' => 'fixed', 'discount_value' => 0, 'gift_product_variant_id' => $vKachki125->id, 'gift_quantity' => 1]);
        // $vKachki1k->tierPrices()->create(['min_quantity' => 1, 'discount_type' => 'fixed', 'discount_value' => 0, 'gift_product_variant_id' => $vLoitta125->id, 'gift_quantity' => 1]);

        // 4. Royal Beef Pickle
        $beefPickle = Product::create([
            'category_id' => $getCatId('pickles'),
            'name' => 'Royal Beef Pickle (রয়্যাল বিফ আচার)',
            'slug' => Str::slug('Royal Beef Pickle'),
            'base_price' => 999,
            'sku' => 'PIC-BEEF-400G',
            'thumbnail' => 'products/pickle-beef.jpg',
            'short_description' => 'Premium beef pickle made with cold-pressed mustard oil and hand-selected spices.',
            'description' => 'Our Royal Beef Pickle is a masterpiece of flavor. We use premium quality beef chunks, authentic spices, and pure cold-pressed mustard oil. Natural vinegar is used for preservation, ensuring a long shelf life without chemical additives. A spicy, tangy delight for any meal.',
            'is_landing_enabled' => true,
            'landing_slug' => 'royal-beef-pickle',
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
        $beefPickle->certifications()->attach($certs->whereIn('name', ['Halal Certified', 'GMP Quality Standards', 'ISO 22000', 'HACCP Certified'])->pluck('id'));

        // 5. Hilsa Fish Pickle
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
        $hilsaPickle->certifications()->attach($certs->whereIn('name', ['Halal Certified', 'GMP Quality Standards', 'ISO 22000', 'HACCP Certified'])->pluck('id'));

        // 6. Mango Products
        // ---------------------------------------------------------------------

        // 6a. Himsagar
        $himsagar = Product::create([
            'category_id' => $getCatId('fruits'),
            'name' => 'Himsagar Mango (হিমসাগর আম)',
            'slug' => 'himsagar-mango',
            'base_price' => 160,
            'thumbnail' => 'products/himsagar.jpg',
            'short_description' => 'The king of Bengal mangoes, famous for its sweet aroma.',
            'description' => 'Sourced directly from Satkhira/Rajshahi. Carbide-free.',
            'is_active' => true,
            'is_featured' => true,
            'is_trending' => true,
        ]);
        $vHim1k = $himsagar->variants()->create(['title' => '1KG', 'sku' => 'HIM-1KG', 'price' => 160, 'stock' => 5000, 'weight_grams' => 1000, 'is_active' => true]);
        $vHim10k = $himsagar->variants()->create(['title' => '10KG', 'sku' => 'HIM-10KG', 'price' => 1600, 'stock' => 1000, 'weight_grams' => 5000, 'is_active' => true]);
        $vHim25k = $himsagar->variants()->create(['title' => '22-26KG', 'sku' => 'HIM-25KG', 'price' => 4000, 'stock' => 5000, 'weight_grams' => 10000, 'is_active' => true]);

        // 6b. Harivanga
        $harivanga = Product::create([
            'category_id' => $getCatId('fruits'),
            'name' => 'Harivanga Mango (হাঁড়ি ভাঙ্গা আম)',
            'slug' => 'harivanga-mango',
            'base_price' => 90,
            'thumbnail' => 'products/harivanga.jpg',
            'short_description' => 'A specialty of Rangpur, known for its distinct fiber-less texture.',
            'description' => 'Exceptionally sweet and deep orange in color.',
            'is_active' => true,
            'is_featured' => true,
            'is_trending' => true,
        ]);
        $vHar1k = $harivanga->variants()->create(['title' => '1KG', 'sku' => 'HAR-1KG', 'price' => 90, 'stock' => 5000, 'weight_grams' => 1000, 'is_active' => true]);
        $vHar5k = $harivanga->variants()->create(['title' => '5KG', 'sku' => 'HAR-5KG', 'price' => 428, 'stock' => 1000, 'weight_grams' => 5000, 'is_active' => true]);
        $vHar10k = $harivanga->variants()->create(['title' => '10KG', 'sku' => 'HAR-10KG', 'price' => 810, 'stock' => 500, 'weight_grams' => 10000, 'is_active' => true]);

        // 6c. Langra
        $langra = Product::create([
            'category_id' => $getCatId('fruits'),
            'name' => 'Langra Mango (ল্যাংড়া আম)',
            'slug' => 'langra-mango',
            'base_price' => 95,
            'thumbnail' => 'products/langra.jpg',
            'short_description' => 'Famous for its unique aroma, thin skin, and melt-in-the-mouth texture.',
            'description' => 'A favorite aromatic variety.',
            'is_active' => true,
            'is_featured' => true,
            'is_trending' => true,
        ]);
        $vLan1k = $langra->variants()->create(['title' => '1KG', 'sku' => 'LAN-1KG', 'price' => 95, 'stock' => 5000, 'weight_grams' => 1000, 'is_active' => true]);
        $vLan5k = $langra->variants()->create(['title' => '5KG', 'sku' => 'LAN-5KG', 'price' => 451, 'stock' => 1000, 'weight_grams' => 5000, 'is_active' => true]);
        $vLan10k = $langra->variants()->create(['title' => '10KG', 'sku' => 'LAN-10KG', 'price' => 855, 'stock' => 500, 'weight_grams' => 10000, 'is_active' => true]);

        // 6d. Amrapali
        $amrapali = Product::create([
            'category_id' => $getCatId('fruits'),
            'name' => 'Amrapali Mango (আমরুপালি আম)',
            'slug' => 'amrapali-mango',
            'base_price' => 85,
            'thumbnail' => 'products/amrapali.jpg',
            'short_description' => 'Exceptionally sweet and deep orange in color.',
            'description' => 'High in pulp and rich in flavor.',
            'is_active' => true,
            'is_featured' => true,
            'is_trending' => true,
        ]);
        $vAmr1k = $amrapali->variants()->create(['title' => '1KG', 'sku' => 'AMR-1KG', 'price' => 85, 'stock' => 5000, 'weight_grams' => 1000, 'is_active' => true]);
        $vAmr5k = $amrapali->variants()->create(['title' => '5KG', 'sku' => 'AMR-5KG', 'price' => 404, 'stock' => 1000, 'weight_grams' => 5000, 'is_active' => true]);
        $vAmr10k = $amrapali->variants()->create(['title' => '10KG', 'sku' => 'AMR-10KG', 'price' => 765, 'stock' => 500, 'weight_grams' => 10000, 'is_active' => true]);

        // 6e. Banana Mango
        $banana = Product::create([
            'category_id' => $getCatId('fruits'),
            'name' => 'Banana Mango (ব্যানানা আম)',
            'slug' => 'banana-mango',
            'base_price' => 150,
            'thumbnail' => 'products/banana-mango.jpg',
            'short_description' => 'A premium elongated variety that looks like a banana.',
            'description' => 'Tiny seed and thick, sweet pulp.',
            'is_active' => true,
            'is_featured' => true,
            'is_trending' => true,
        ]);
        $vBan1k = $banana->variants()->create(['title' => '1KG', 'sku' => 'BAN-1KG', 'price' => 150, 'stock' => 5000, 'weight_grams' => 1000, 'is_active' => true]);
        $vBan5k = $banana->variants()->create(['title' => '5KG', 'sku' => 'BAN-5KG', 'price' => 713, 'stock' => 1000, 'weight_grams' => 5000, 'is_active' => true]);
        $vBan10k = $banana->variants()->create(['title' => '10KG', 'sku' => 'BAN-10KG', 'price' => 1350, 'stock' => 500, 'weight_grams' => 10000, 'is_active' => true]);

        // 6f. Gourmati
        $gourmati = Product::create([
            'category_id' => $getCatId('fruits'),
            'name' => 'Gourmati Mango (গোড়মতি আম)',
            'slug' => 'gourmati-mango',
            'base_price' => 200,
            'thumbnail' => 'products/gourmati.jpg',
            'short_description' => 'A late-season premium variety.',
            'description' => 'Extremely sweet, long shelf life.',
            'is_active' => true,
            'is_featured' => true,
            'is_trending' => true,
        ]);
        $vGou1k = $gourmati->variants()->create(['title' => '1KG', 'sku' => 'GOU-1KG', 'price' => 200, 'stock' => 5000, 'weight_grams' => 1000, 'is_active' => true]);
        $vGou5k = $gourmati->variants()->create(['title' => '5KG', 'sku' => 'GOU-5KG', 'price' => 950, 'stock' => 1000, 'weight_grams' => 5000, 'is_active' => true]);
        $vGou10k = $gourmati->variants()->create(['title' => '10KG', 'sku' => 'GOU-10KG', 'price' => 1800, 'stock' => 500, 'weight_grams' => 10000, 'is_active' => true]);

        // ---------------------------------------------------------------------
        // Mango Tier Assignments (Buy 15+ 1KG units -> Get different variety gift)
        // ---------------------------------------------------------------------
        // No Free Delivery as per request
        // $vHim1k->tierPrices()->create(['min_quantity' => 15, 'discount_type' => 'fixed', 'discount_value' => 0, 'gift_product_variant_id' => $vHar1k->id, 'gift_quantity' => 1]);
        // $vHar1k->tierPrices()->create(['min_quantity' => 15, 'discount_type' => 'fixed', 'discount_value' => 0, 'gift_product_variant_id' => $vLan1k->id, 'gift_quantity' => 1]);
        // $vLan1k->tierPrices()->create(['min_quantity' => 15, 'discount_type' => 'fixed', 'discount_value' => 0, 'gift_product_variant_id' => $vAmr1k->id, 'gift_quantity' => 1]);
        // $vAmr1k->tierPrices()->create(['min_quantity' => 15, 'discount_type' => 'fixed', 'discount_value' => 0, 'gift_product_variant_id' => $vBan1k->id, 'gift_quantity' => 1]);
        // $vBan1k->tierPrices()->create(['min_quantity' => 15, 'discount_type' => 'fixed', 'discount_value' => 0, 'gift_product_variant_id' => $vGou1k->id, 'gift_quantity' => 1]);
        // $vGou1k->tierPrices()->create(['min_quantity' => 15, 'discount_type' => 'fixed', 'discount_value' => 0, 'gift_product_variant_id' => $vHim1k->id, 'gift_quantity' => 1]);

        // Attach certifications
        foreach ([$himsagar, $harivanga, $langra, $amrapali, $banana, $gourmati] as $mProd) {
            $mProd->certifications()->attach($certs->pluck('id'));
        }
    }
}
