<?php

namespace App\Providers;

use App\Domains\Category\Models\Category;
use App\Domains\Category\Observers\CategoryObserver;
use App\Domains\Landing\Models\LandingPage;
use App\Domains\Landing\Observers\LandingPageObserver;
use App\Domains\Order\Models\Order;
use App\Domains\Order\Observers\OrderObserver;
use App\Domains\Product\Models\Combo;
use App\Domains\Product\Models\Product;
use App\Domains\Product\Models\ProductVariant;
use App\Domains\Product\Observers\ComboObserver;
use App\Domains\Product\Observers\ProductObserver;
use App\Domains\Product\Observers\ProductVariantObserver;
use App\Domains\Shipping\Models\ShippingZone;
use App\Domains\Shipping\Observers\ShippingZoneObserver;
use App\Domains\Store\Models\HeroBanner;
use App\Domains\Store\Observers\HeroBannerObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        View::composer('partials.datalayer', function ($view) {
            $route = request()->route()?->getName() ?? '';
            $pageType = match (true) {
                $route === 'product.show'                                                       => 'product',
                $route === 'combos.show'                                                        => 'combo',
                str_starts_with($route, 'landing.')                                             => 'landing',
                $route === 'home'                                                               => 'home',
                in_array($route, ['product.index', 'shop', 'category.view', 'combos.index'])   => 'shop',
                str_starts_with($route, 'cart.')                                                => 'cart',
                str_starts_with($route, 'checkout.')                                            => 'checkout',
                $route === 'order.success'                                                      => 'thank-you',
                $route === 'order.failed'                                                       => 'order-failed',
                str_starts_with($route, 'customer.')                                            => 'account',
                default                                                                         => 'other',
            };
            $view->with('pageType', $pageType);
        });

        Product::observe(ProductObserver::class);
        ProductVariant::observe(ProductVariantObserver::class);
        Category::observe(CategoryObserver::class);
        Combo::observe(ComboObserver::class);
        LandingPage::observe(LandingPageObserver::class);
        HeroBanner::observe(HeroBannerObserver::class);
        ShippingZone::observe(ShippingZoneObserver::class);
        Order::observe(OrderObserver::class);
    }
}
