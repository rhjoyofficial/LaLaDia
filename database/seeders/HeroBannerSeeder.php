<?php

namespace Database\Seeders;

use App\Domains\Store\Models\HeroBanner;
use App\Domains\Category\Models\Category;
use Illuminate\Database\Seeder;

class HeroBannerSeeder extends Seeder
{
    public function run(): void
    {
        $honeyCategory   = Category::where('slug', 'honey')->first();
        $pickleCategory  = Category::where('slug', 'pickles')->first();
        $gheeCategory    = Category::where('slug', 'ghee')->first();
        $fruitCategory   = Category::where('slug', 'fruits')->first();
        $shutkiCategory  = Category::where('slug', 'dry-fish')->first();

        HeroBanner::truncate();

        $banners = [
            [
                'badge'       => '100% Pure & Natural',
                'title'       => 'Pure Sundarbans <br> Mangrove Gold <br> Honey',
                'subtitle'    => 'Farm to Jar Freshness',
                'description' => 'Raw, unfiltered honey harvested from the heart of the Sundarbans — no additives, no heat.',
                'button_text' => 'Shop Honey',
                'button_url'  => '/shop?category=honey',
                'image'       => 'banners/honey-banner.png',
                'sort_order'  => 1,
                'is_active'   => true,
                'starts_at'   => null,
                'ends_at'     => null,
                'category_id' => $honeyCategory?->id,
            ],
            [
                'badge'       => 'Authentic Bengali Recipe',
                'title'       => 'Royal Beef & <br> Hilsa Fish <br> Pickles',
                'subtitle'    => 'Generations of Flavour',
                'description' => 'Traditional hand-crafted pickles made from premium Hilsa and tender beef — bold, rich, unforgettable.',
                'button_text' => 'Shop Pickles',
                'button_url'  => '/shop?category=pickles',
                'image'       => 'banners/pickle-banner.png',
                'sort_order'  => 2,
                'is_active'   => true,
                'starts_at'   => null,
                'ends_at'     => null,
                'category_id' => $pickleCategory?->id,
            ],
            [
                'badge'       => 'Premium A2 Ghee',
                'title'       => 'Royal Essence <br> Ghee — <br> রয়্যাল এসেন্স ঘি',
                'subtitle'    => 'Slow-Churned, Cultured Butter',
                'description' => 'Golden, aromatic, bilona-method ghee crafted from grass-fed cows. Taste the purity.',
                'button_text' => 'Shop Ghee',
                'button_url'  => '/shop?category=ghee',
                'image'       => 'banners/ghee-banner.png',
                'sort_order'  => 3,
                'is_active'   => true,
                'starts_at'   => null,
                'ends_at'     => null,
                'category_id' => $gheeCategory?->id,
            ],
            [
                'badge'       => 'Mango Season',
                'title'       => 'Fresh Fazli & <br> Langra Mangoes <br> Delivered',
                'subtitle'    => 'Straight from the Orchard',
                'description' => 'Premium-grade Fazli, Gopalbhog, Langra, and Amrapali mangoes — ordered by weight, shipped fresh.',
                'button_text' => 'Order Mangoes',
                'button_url'  => '/shop?category=fruits',
                'image'       => 'banners/mango-banner.png',
                'sort_order'  => 4,
                'is_active'   => true,
                'starts_at'   => null,
                'ends_at'     => null,
                'category_id' => $fruitCategory?->id,
            ],
            [
                'badge'       => 'Bay of Bengal Dry Fish',
                'title'       => 'Premium Shutki <br> — Loitta, Churi <br> & More',
                'subtitle'    => 'Sun-Dried, Naturally Preserved',
                'description' => 'Authentic Bangladeshi shutki: Loitta, Churi, Modhu Faisa, and Mowrala Kachki — packed fresh for you.',
                'button_text' => 'Shop Shutki',
                'button_url'  => '/shop?category=dry-fish',
                'image'       => 'banners/shutki-banner.png',
                'sort_order'  => 5,
                'is_active'   => true,
                'starts_at'   => null,
                'ends_at'     => null,
                'category_id' => $shutkiCategory?->id,
            ],
        ];

        foreach ($banners as $banner) {
            HeroBanner::create($banner);
        }
    }
}
