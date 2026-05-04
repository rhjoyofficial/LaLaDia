<?php

namespace Database\Seeders;

use App\Domains\Landing\Models\LandingPage;
use App\Domains\Landing\Models\LandingPageItem;
use App\Domains\Product\Models\Combo;
use App\Domains\Product\Models\Product;
use App\Domains\Product\Models\ProductVariant;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. PRODUCT TYPE — Mangrove Gold Honey ─────
        $honey = Product::where('slug', 'mangrove-gold-honey')->first();
        if ($honey) {
            LandingPage::updateOrCreate(
                ['slug' => 'sundarbans-mangrove-honey'],
                [
                    'type'             => LandingPage::TYPE_PRODUCT,
                    'product_id'       => $honey->id,
                    'title'            => 'সুন্দরবনের খাঁটি ম্যানগ্রোভ গোল্ড হানি',
                    'blade_template'   => 'product-default',
                    'hero_image'       => $honey->thumbnail,
                    'content'          => 'সুন্দরবনের গভীর থেকে সংগৃহীত ১০০% প্রাকৃতিক ও বিশুদ্ধ মধু। কোনো প্রিজারভেটিভ নেই।',
                    'meta_title'       => 'খাঁটি সুন্দরবনের মধু কিনুন | La La Dia',
                    'meta_description' => '১০০% প্রাকৃতিক ম্যানগ্রোভ গোল্ড হানি। সুন্দরবনের খাঁটি মধু সরাসরি আপনার ঘরে।',
                    'pixel_event_name' => 'ViewContent',
                    'is_active'        => true,
                    'config'           => [
                        'free_delivery_amount' => 2000,
                        'hero_style'           => 'golden',
                    ],
                ]
            );
        }

        // ─── 2. PRODUCT TYPE — Royal Essence Ghee ────────
        $ghee = Product::where('slug', 'royal-essence-ghee')->first();
        if ($ghee) {
            LandingPage::updateOrCreate(
                ['slug' => 'royal-essence-ghee-offer'],
                [
                    'type'             => LandingPage::TYPE_PRODUCT,
                    'product_id'       => $ghee->id,
                    'title'            => 'গাওয়া ঘি — রয়্যাল এসেন্স প্রিমিয়াম কোয়ালিটি',
                    'blade_template'   => 'product-default',
                    'hero_image'       => $ghee->thumbnail,
                    'content'          => 'খাঁটি গরুর দুধের সর থেকে তৈরি সুগন্ধি ও দানাদার ঘি।',
                    'meta_title'       => 'খাঁটি গাওয়া ঘি কিনুন | La La Dia',
                    'meta_description' => 'রয়্যাল এসেন্স ঘি — ঐতিহ্যের স্বাদ ও বিশুদ্ধতার নিশ্চয়তা।',
                    'pixel_event_name' => 'ViewContent',
                    'is_active'        => true,
                    'config'           => [
                        'hero_style'      => 'light',
                    ],
                ]
            );
        }

        // ─── 3. COMBO TYPE — Breakfast Delight ────────────────────
        $breakfastCombo = Combo::where('slug', 'breakfast-delight-combo')->first();
        if ($breakfastCombo) {
            LandingPage::updateOrCreate(
                ['slug' => 'breakfast-delight-offer'],
                [
                    'type'             => LandingPage::TYPE_COMBO,
                    'combo_id'         => $breakfastCombo->id,
                    'title'            => 'ব্রেকফাস্ট ডিলাইট কম্বো — মধু ও ঘি এর মেলবন্ধন',
                    'blade_template'   => 'combo-default',
                    'hero_image'       => $breakfastCombo->image,
                    'content'          => 'ম্যানগ্রোভ গোল্ড হানি এবং রয়্যাল এসেন্স ঘি এর সেরা কম্বো।',
                    'meta_title'       => 'মধু ও ঘি কম্বো অফার | La La Dia',
                    'meta_description' => 'সুস্থ থাকতে প্রতিদিনের নাস্তায় রাখুন মধু ও ঘি। কিনুন সাশ্রয়ী মূল্যে।',
                    'pixel_event_name' => 'ViewContent',
                    'is_active'        => true,
                    'config'           => [
                        'hero_style'           => 'luxury',
                    ],
                ]
            );
        }

        // ─── 4. LISTING TYPE — Premium Mango Collection ────────
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
                        'meta_description' => 'হিমসাগর, আম্রপালি, ল্যাংড়া সহ সেরা সব আমের কালেকশন। সরাসরি বাগান থেকে আপনার ঘরে।',
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

        // ─── 5. SALES TYPE — Dry Fish Collection ────────
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

        // Add all dry fish variants as sales items
        $dryfishSkus = [
            'loitta-shutki-125G' => 0,
            'loitta-shutki-500G' => 1,
            'loitta-shutki-1KG'  => 2,
            'churi-shutki-125G'  => 3,
            'churi-shutki-500G'  => 4,
            'churi-shutki-1KG'   => 5,
            'modhu-faisa-125G'   => 6,
            'modhu-faisa-500G'   => 7,
            'modhu-faisa-1KG'    => 8,
            'mowrala-kachki-125G'=> 9,
            'mowrala-kachki-500G'=> 10,
            'mowrala-kachki-1KG' => 11,
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
    }
}
