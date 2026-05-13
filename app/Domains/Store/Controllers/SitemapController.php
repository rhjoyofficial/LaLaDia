<?php

namespace App\Domains\Store\Controllers;

use App\Domains\Category\Models\Category;
use App\Domains\Product\Models\Combo;
use App\Domains\Product\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap_xml', now()->addHours(6), fn () => $this->build());

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    private function build(): string
    {
        $base = rtrim(config('app.url'), '/');

        $products = Product::active()
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $combos = Combo::active()
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $categories = Category::active()
            ->hasActiveProducts()
            ->select('slug', 'updated_at')
            ->orderBy('sort_order')
            ->get();

        $urls = [];

        // ── Static pages ──────────────────────────────────────────────────
        foreach ([
            ['/' ,              'weekly',  '1.0'],
            ['/products',       'daily',   '0.9'],
            ['/combos',         'daily',   '0.9'],
            ['/blog',           'weekly',  '0.6'],
            ['/gallery',        'monthly', '0.5'],
            ['/about',          'monthly', '0.5'],
            ['/contact',        'monthly', '0.5'],
            ['/faq',            'monthly', '0.5'],
            ['/privacy-policy', 'yearly',  '0.3'],
            ['/terms',          'yearly',  '0.3'],
            ['/disclaimer',     'yearly',  '0.3'],
        ] as [$path, $freq, $priority]) {
            $urls[] = $this->url($base . $path, now()->toAtomString(), $freq, $priority);
        }

        // ── Categories ────────────────────────────────────────────────────
        foreach ($categories as $cat) {
            $urls[] = $this->url(
                $base . '/category/' . $cat->slug,
                $cat->updated_at->toAtomString(),
                'weekly', '0.7'
            );
        }

        // ── Products ──────────────────────────────────────────────────────
        foreach ($products as $product) {
            $urls[] = $this->url(
                $base . '/product/' . $product->slug,
                $product->updated_at->toAtomString(),
                'weekly', '0.8'
            );
        }

        // ── Combos ────────────────────────────────────────────────────────
        foreach ($combos as $combo) {
            $urls[] = $this->url(
                $base . '/combos/' . $combo->slug,
                $combo->updated_at->toAtomString(),
                'weekly', '0.8'
            );
        }

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n"
            . implode("\n", $urls) . "\n"
            . '</urlset>';
    }

    private function url(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        $loc = htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return "  <url>\n"
            . "    <loc>{$loc}</loc>\n"
            . "    <lastmod>{$lastmod}</lastmod>\n"
            . "    <changefreq>{$changefreq}</changefreq>\n"
            . "    <priority>{$priority}</priority>\n"
            . "  </url>";
    }
}
