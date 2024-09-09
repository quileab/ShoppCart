<?php

namespace Quileab\ShopPCart;

class ShoppCartServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ShoppingCart::class, function ($app) {
            return new ShoppingCart();
        });
    }

    public function boot()
    {
        // If you need to publish configs, views, etc.
    }
}