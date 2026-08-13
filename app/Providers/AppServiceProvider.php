<?php

namespace App\Providers;

use App\Domains\Notifications\Contracts\WhatsAppNotifierInterface;
use App\Domains\Notifications\FakeWhatsAppNotifier;
use App\Domains\Notifications\LogWhatsAppNotifier;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Policies\CustomerProfilePolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentProofPolicy;
use App\Services\Shipping\MockBiteshipShippingService;
use App\Services\Shipping\ShippingCalculatorInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ShippingCalculatorInterface::class,
            MockBiteshipShippingService::class
        );

        $this->app->singleton(WhatsAppNotifierInterface::class, function () {
            return config('store.whatsapp.driver') === 'fake'
                ? new FakeWhatsAppNotifier
                : new LogWhatsAppNotifier;
        });
    }

    public function boot(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(PaymentProof::class, PaymentProofPolicy::class);
        Gate::policy(CustomerProfile::class, CustomerProfilePolicy::class);
    }
}
