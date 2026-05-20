<?php

namespace Database\Seeders;

use App\Domains\Landing\Models\LandingPage;
use App\Domains\Landing\Models\LandingPageItem;
use App\Domains\Product\Models\Product;
use App\Domains\Product\Models\ProductVariant;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. LISTING TYPE — Premium Mango Collection ────────
        $mangoCategory = \App\Domains\Category\Models\Category::where('slug', 'fruits')->first();
        if ($mangoCategory) {
            $mangoProducts = Product::where('category_id', $mangoCategory->id)->get();
            if ($mangoProducts->count() > 0) {
                $mangoLanding = LandingPage::updateOrCreate(
                    ['slug' => 'premium-mango-collection'],
                    [
                        'type'             => LandingPage::TYPE_LISTING,
                        'title'            => 'প্রিমিয়াম আমের সমাহার — সরাসরি বাগান থেকে',
                        'blade_template'   => 'mango-items',
                        'content'          => 'রাজশাহীর সেরা বাগান থেকে বাছাইকৃত ১০০% ফরমালিন মুক্ত ও বিষমুক্ত আম।',
                        'meta_title'       => 'প্রিমিয়াম আম সংগ্রহ | La La Dia',
                        'meta_description' => 'হিমসাগর, আম্রপali, ল্যাংড়া সহ সেরা সব আমের কালেকশন। সরাসরি বাগান থেকে আপনার ঘরে।',
                        'pixel_event_name' => 'ViewContent',
                        'is_active'        => true,
                        'config'           => [
                            'hero_style' => 'tropical',
                        ],
                    ]
                );

                // Add items
                foreach ($mangoProducts as $index => $product) {
                    $variant = $product->variants()->first();
                    if ($variant) {
                        \App\Domains\Landing\Models\LandingPageItem::updateOrCreate(
                            [
                                'landing_page_id'    => $mangoLanding->id,
                                'product_variant_id' => $variant->id,
                            ],
                            [
                                'sort_order'     => $index,
                                'is_preselected' => false,
                            ]
                        );
                    }
                }
            }
        }

        // ─── 2. SALES TYPE — Dry Fish Collection ────────
        $dryfishLanding = LandingPage::updateOrCreate(
            ['slug' => 'dryfish-collection'],
            [
                'type'             => LandingPage::TYPE_SALES,
                'title'            => 'বিশুদ্ধ ও নিরাপদ শুঁটকি মাছ — সরাসরি সমুদ্র থেকে',
                'blade_template'   => 'dryfish',
                'content'          => 'শতভাগ রাসায়নিকমুক্ত শুঁটকি মাছ — লইট্টা, ছুরি, মধু ফাইশ্যা, মৌরালা কাচকি। গ্রিন মাচা ও ভ্যাকুয়াম প্যাকেজিংয়ে প্রস্তুত।',
                'meta_title'       => 'বিশুদ্ধ শুঁটকি মাছ কিনুন | La La Dia',
                'meta_description' => 'শতভাগ রাসায়নিকমুক্ত শুঁটকি মাছ — লইট্টা, ছুরি, মধু ফাইশ্যা, মৌরালা কাচকি। সারা বাংলাদেশে ডেলিভারি।',
                'pixel_event_name' => 'ViewContent',
                'is_active'        => true,
                'config'           => [
                    'free_delivery_amount' => 2000,
                    'hero_style'           => 'natural',
                ],
            ]
        );

        // Fixed SKUs to match ProductSeeder exactly
        $dryfishSkus = [
            'loitta-125G'        => 0,
            'loitta-500G'        => 1,
            'loitta-1KG'         => 2,
            'churi-125G'         => 3,
            'churi-500G'         => 4,
            'churi-1KG'          => 5,
            'faisa-125G'         => 6,
            'faisa-500G'         => 7,
            'faisa-1KG'          => 8,
            'kachki-125G'        => 9,
            'kachki-500G'        => 10,
            'kachki-1KG'         => 11,
        ];

        foreach ($dryfishSkus as $sku => $sortOrder) {
            $variant = ProductVariant::where('sku', $sku)->first();
            if ($variant) {
                LandingPageItem::updateOrCreate(
                    [
                        'landing_page_id'    => $dryfishLanding->id,
                        'product_variant_id' => $variant->id,
                    ],
                    [
                        'sort_order'     => $sortOrder,
                        'is_preselected' => false,
                    ]
                );
            }
        }

        // ─── 3. PRODUCT TYPE — Royal Essence Ghee ────────
        $gheeProduct = Product::where('slug', 'royal-essence-ghee')->first();
        if ($gheeProduct) {
            LandingPage::updateOrCreate(
                ['slug' => 'royal-essence-ghee'],
                [
                    'type'             => LandingPage::TYPE_PRODUCT,
                    'product_id'       => $gheeProduct->id,
                    'title'            => 'রয়্যাল এসেন্স ঘি — প্রকৃতির উপহার, আপনার রান্নায়',
                    'hero_image'       => 'assets/landing/royal-essence-hero.jpg',
                    'blade_template'   => 'royalessenceghee',
                    'content'          => '১০০% বিশুদ্ধ দেশি ঘি, দেশী সেরা গরুর দুধ থেকে তৈরি। স্বাস্থ্যকর রান্নার জন্য প্রাকৃতিক উপাদান ও পুষ্টিগুণে ভরপুর।',
                    'meta_title'       => 'রয়্যাল এসেন্স ঘি',
                    'meta_description' => '১০০% বিশুদ্ধ দেশি ঘি, দেশী সেরা গরুর দুধ থেকে তৈরি। স্বাস্থ্যকর রান্নার জন্য প্রাকৃতিক উপাদান ও পুষ্টিগুণে ভরপুর। সারা বাংলাদেশে ডেলিভারি।',
                ]
            );
        }
    }
}
