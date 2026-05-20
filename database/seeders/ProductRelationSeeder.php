<?php

namespace Database\Seeders;

use App\Domains\Product\Models\Product;
use App\Domains\Product\Models\ProductRelation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductRelationSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('product_relations')->truncate();
        Schema::enableForeignKeyConstraints();

        $products = Product::query()
            ->whereIn('slug', collect($this->relations())->flatMap(fn ($relation) => [
                $relation['product'],
                $relation['related'],
            ])->unique())
            ->get()
            ->keyBy('slug');

        foreach ($this->relations() as $relation) {
            $product = $products->get($relation['product']);
            $relatedProduct = $products->get($relation['related']);

            if (! $product || ! $relatedProduct || $product->is($relatedProduct)) {
                continue;
            }

            ProductRelation::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'related_product_id' => $relatedProduct->id,
                    'relation_type' => $relation['type'],
                ],
                [
                    'discount_percentage' => $relation['discount_percentage'] ?? null,
                ]
            );
        }
    }

    private function relations(): array
    {
        return [
            ['product' => 'mangrove-gold-honey', 'related' => 'royal-essence-ghee', 'type' => 'upsell'],
            ['product' => 'mangrove-gold-honey', 'related' => 'royal-beef-pickle', 'type' => 'cross_sell'],
            ['product' => 'royal-essence-ghee', 'related' => 'mangrove-gold-honey', 'type' => 'cross_sell'],
            ['product' => 'royal-essence-ghee', 'related' => 'royal-beef-pickle', 'type' => 'cross_sell'],
            ['product' => 'royal-beef-pickle', 'related' => 'hilsa-fish-pickle', 'type' => 'cross_sell'],
            ['product' => 'hilsa-fish-pickle', 'related' => 'royal-beef-pickle', 'type' => 'cross_sell'],

            ['product' => 'loitta-shutki', 'related' => 'churi-shutki', 'type' => 'cross_sell'],
            ['product' => 'loitta-shutki', 'related' => 'modhu-faisa', 'type' => 'cross_sell'],
            ['product' => 'churi-shutki', 'related' => 'loitta-shutki', 'type' => 'cross_sell'],
            ['product' => 'churi-shutki', 'related' => 'mowrala-kachki', 'type' => 'cross_sell'],
            ['product' => 'modhu-faisa', 'related' => 'loitta-shutki', 'type' => 'cross_sell'],
            ['product' => 'mowrala-kachki', 'related' => 'churi-shutki', 'type' => 'cross_sell'],

            ['product' => 'himsagar-mango', 'related' => 'harivanga-mango', 'type' => 'cross_sell'],
            ['product' => 'harivanga-mango', 'related' => 'langra-mango', 'type' => 'cross_sell'],
            ['product' => 'langra-mango', 'related' => 'amrapali-mango', 'type' => 'cross_sell'],
            ['product' => 'amrapali-mango', 'related' => 'banana-mango', 'type' => 'cross_sell'],
            ['product' => 'banana-mango', 'related' => 'gourmati-mango', 'type' => 'cross_sell'],
            ['product' => 'gourmati-mango', 'related' => 'himsagar-mango', 'type' => 'cross_sell'],
            ['product' => 'himsagar-mango', 'related' => 'banana-mango', 'type' => 'upsell'],
            ['product' => 'harivanga-mango', 'related' => 'gourmati-mango', 'type' => 'upsell'],
        ];
    }
}
