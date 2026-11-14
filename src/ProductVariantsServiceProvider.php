<?php

namespace ItmoSoft\ProductVariants;

use Illuminate\Support\ServiceProvider;

class ProductVariantsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/product-variants.php', 'product-variants');
        $this->app->singleton(ProductVariantsManager::class);
        $this->app->alias(ProductVariantsManager::class, 'product-variants');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/product-variants.php' => config_path('product-variants.php'),
        ], 'product-variants-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
