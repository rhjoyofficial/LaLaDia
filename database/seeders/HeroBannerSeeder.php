<?php

namespace Database\Seeders;

use App\Domains\Store\Models\HeroBanner;
use App\Domains\Category\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class HeroBannerSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');

        Schema::disableForeignKeyConstraints();
        HeroBanner::truncate();
        Schema::enableForeignKeyConstraints();

        $banners = [
            [
                'badge'       => 'Seasonal Excellence',
                'title'       => 'Premium Seasonal <br> Rajshahi & <br> Satkhira Mangos',
                'subtitle'    => 'Taste the King of Fruits',
                'description' => 'Direct from the finest orchards of Bangladesh. Hand-picked, chemical-free, and naturally ripened.',
                'button_text' => 'Order Mangoes',
                'button_url'  => '/page/premium-mango-collection',
                'image'       => 'banners/banner-mango.png',
                'sort_order'  => 0,
                'is_active'   => true,
                'category_id' => $categories['fruits']->id ?? null,
            ],
            [
                'badge'       => '100% Pure & Raw',
                'title'       => 'Pure Sundarbans <br> Mangrove Gold <br> Honey',
                'subtitle'    => 'Nature’s Golden Elixir',
                'description' => 'Unfiltered, chemical-free honey harvested from the heart of the world’s largest mangrove forest.',
                'button_text' => 'Shop Honey',
                'button_url'  => '/shop?category=honey',
                'image'       => 'banners/banner-honey.png',
                'sort_order'  => 1,
                'is_active'   => true,
                'category_id' => $categories['honey']->id ?? null,
            ],
            [
                'badge'       => 'Traditional Small Batch',
                'title'       => 'Pure Royal <br> Essence Ghee <br> — রয়্যাল এসেন্স ঘি',
                'subtitle'    => 'Slow-Churned Perfection',
                'description' => 'Aromatic, golden ghee crafted from grass-fed cow milk cream. No additives, just purity.',
                'button_text' => 'Order Ghee',
                'button_url'  => '/shop?category=ghee',
                'image'       => 'banners/banner-ghee.png',
                'sort_order'  => 2,
                'is_active'   => true,
                'category_id' => $categories['ghee']->id ?? null,
            ],
            [
                'badge'       => 'Authentic Bengali Taste',
                'title'       => 'Royal Beef & <br> Hilsa Fish <br> Pickles',
                'subtitle'    => 'The Perfect Meal Companion',
                'description' => 'Hand-crafted pickles using age-old recipes, premium meat/fish, and cold-pressed mustard oil.',
                'button_text' => 'View Pickles',
                'button_url'  => '/shop?category=pickles',
                'image'       => 'banners/banner-pickle.png',
                'sort_order'  => 3,
                'is_active'   => true,
                'category_id' => $categories['pickles']->id ?? null,
            ],
            [
                'badge'       => 'Premium Coastal Harvest',
                'title'       => 'Authentic <br> Coastal Dry <br> Fish (Shutki)',
                'subtitle'    => 'Naturally Sun-Dried',
                'description' => 'Loitta, Churi, and Faisa shutki — cleaned and processed with modern hygiene standards.',
                'button_text' => 'Shop Shutki',
                'button_url'  => '/page/dryfish-collection',
                'image'       => 'banners/banner-shutki.png',
                'sort_order'  => 4,
                'is_active'   => true,
                'category_id' => $categories['dry-fish']->id ?? null,
            ],
        ];

        foreach ($banners as $banner) {
            HeroBanner::create($banner);
        }
    }
}
