<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $cart = json_decode(request()->cookie('cart', '[]'), true);
            $countCart = request()->cookie('countCart', 0);
            $view->with([
                'cart' => $cart,
                'countCart' => $countCart
            ]);
        });
    }
}
