<?php

namespace App\Providers;

use App\Domains\Notifications\Contracts\WhatsAppNotifierInterface;
use App\Domains\Notifications\FakeWhatsAppNotifier;
use App\Domains\Notifications\FonnteWhatsAppNotifier;
use App\Domains\Notifications\LogWhatsAppNotifier;
use App\Domains\PosIntegration\PosMasterSyncInterface;
use App\Domains\PosIntegration\SamplePosSyncService;
use App\Domains\PosIntegration\SandboxPosMasterSyncService;
use App\Http\Middleware\EnsureActiveCustomer;
use App\Http\Middleware\EnsureAdmin;
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
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ShippingCalculatorInterface::class, function () {
            $driver = config('store.shipping.driver', 'mock');

            return match ($driver) {
                'biteship' => new BiteshipShippingService,
                default => new MockBiteshipShippingService,
            };
        });

        $this->app->singleton(WhatsAppNotifierInterface::class, function () {
            return match (config('store.whatsapp.driver')) {
                'fake' => new FakeWhatsAppNotifier,
                'fonnte' => new FonnteWhatsAppNotifier,
                default => new LogWhatsAppNotifier,
            };
        });

        $this->app->bind(PosMasterSyncInterface::class, function ($app) {
            $driver = config('pos.driver', 'sample');

            return match ($driver) {
                'sandbox' => $app->make(SandboxPosMasterSyncService::class),
                default => $app->make(SamplePosSyncService::class),
            };
        });
    }

    public function boot(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(PaymentProof::class, PaymentProofPolicy::class);
        Gate::policy(CustomerProfile::class, CustomerProfilePolicy::class);

        // Aksi Livewire (/livewire/update) ikut menjalankan ulang middleware route asal,
        // jadi admin yang diturunkan / pelanggan yang ditangguhkan saat tab terbuka tertolak.
        Livewire::addPersistentMiddleware([EnsureAdmin::class, EnsureActiveCustomer::class]);

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
