<?php

namespace Database\Seeders;

use App\Domains\Category\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Honey', 'description' => 'Pure and natural honey from the Sundarbans and beyond', 'image' => 'categories/honey.gif'],
            ['name' => 'Ghee', 'description' => 'Pure cow milk ghee prepared using traditional methods', 'image' => 'categories/ghee.gif'],
            ['name' => 'Pickles', 'description' => 'Authentic hand-crafted meat and fish pickles', 'image' => 'categories/pickles.gif'],
            ['name' => 'Dry Fish', 'description' => 'Premium sun-dried fish sourced from the coastal regions', 'image' => 'categories/dry_fish.gif'],
            ['name' => 'Fruits', 'description' => 'Seasonal premium fruits direct from the orchards', 'image' => 'categories/fruits.gif'],
        ];

        foreach ($categories as $index => $catData) {
            Category::updateOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'image' => $catData['image'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
