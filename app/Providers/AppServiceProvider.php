<?php

namespace App\Providers;

use App\Services\Shipping\MockBiteshipShippingService;
use App\Services\Shipping\ShippingCalculatorInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ShippingCalculatorInterface::class,
            MockBiteshipShippingService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
