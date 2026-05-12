<?php

namespace App\Domains\Product\Services;

use App\Domains\Product\Models\Product;

class ProductSearchService
{

    public function search(array $filters)
    {
        $query = Product::query()
            ->with(['variants', 'category'])
            ->where('is_active', true);

        // Use collect() for cleaner filter handling
        $filters = collect($filters);

        // Keyword search — FULLTEXT index (avoids full-table LIKE '%q%' scan).
        // Falls back to LIKE for very short terms (< 3 chars) which FULLTEXT skips.
        if ($filters->get('q')) {
            $term = trim($filters->get('q'));
            if (mb_strlen($term) >= 3) {
                $query->whereFullText(['name', 'short_description'], $term);
            } else {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'LIKE', "{$term}%")
                      ->orWhere('short_description', 'LIKE', "{$term}%");
                });
            }
        }

        // Category filter
        if ($filters->get('category_id')) {
            $query->where('category_id', $filters->get('category_id'));
        }

        // Price filtering — runs against the cheapest active variant's price
        // so the filter matches what customers actually see on product cards.
        if ($filters->has('min_price') || $filters->has('max_price')) {
            $query->whereHas('variants', function ($q) use ($filters) {
                $q->where('is_active', true);
                if ($filters->has('min_price')) {
                    $q->where('price', '>=', $filters->get('min_price'));
                }
                if ($filters->has('max_price')) {
                    $q->where('price', '<=', $filters->get('max_price'));
                }
            });
        }

        // Featured
        if ($filters->get('featured')) {
            $query->where('is_featured', true);
        }

        // Sorting - match is great here
        $sort = $filters->get('sort', 'latest');
        match ($sort) {
            'price_asc'  => $query->orderBy('base_price', 'asc'),
            'price_desc' => $query->orderBy('base_price', 'desc'),
            'latest'     => $query->latest(),
            default      => $query->latest(),
        };

        return $query->paginate(12)->withQueryString();
        // .withQueryString() ensures pagination links include your search terms
    }
}
