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
use App\Services\Shipping\BiteshipShippingService;
use App\Services\Shipping\MockBiteshipShippingService;
use App\Services\Shipping\ShippingCalculatorInterface;
use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ShippingCalculatorInterface::class, function () {
            $driver = config('store.shipping.driver', 'mock');

            return match ($driver) {
                'biteship' => new BiteshipShippingService(),
                default => new MockBiteshipShippingService(),
            };
        });

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

        // Saat DB di-reset (migrate:fresh), media unggahan lokal ikut dibersihkan
        // agar konsisten dengan tabel media_library yang ter-wipe. Khusus dev/staging,
        // tidak pernah di production.
        Event::listen(DatabaseRefreshed::class, function (): void {
            if (! app()->environment(['local', 'staging']) || app()->runningUnitTests()) {
                return;
            }

            Storage::disk('public')->deleteDirectory('product-media');
        });
    }
}
