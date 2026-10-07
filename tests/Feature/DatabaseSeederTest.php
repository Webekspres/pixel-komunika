<?php

use App\Models\Order;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Storage;

it('seeds sample orders without repeating the processing transition', function () {
    Storage::fake('local');

    app(DatabaseSeeder::class)->run();

    expect(Order::query()->where('status', 'processing')->count())->toBe(1)
        ->and(Order::query()->where('status', 'shipped')->count())->toBe(1)
        ->and(Order::query()->where('status', 'cancelled')->count())->toBe(1);
});

// UAT REG-05: seeder demo dulu memakai user_id sehingga menyetujui pendaftar pending bentrok nomor reseller.
it('lets admin approve the seeded pending customer', function () {
    Storage::fake('local');
    app(DatabaseSeeder::class)->run();

    $pending = App\Models\CustomerProfile::query()->where('verification_status', App\Models\CustomerProfile::PENDING)->firstOrFail();
    $admin = App\Models\User::query()->where('email', 'admin@pixelkomunika.test')->firstOrFail();

    $approved = app(App\Domains\CustomerManagement\CustomerVerificationService::class)->transition($pending, 'approve', $admin);

    expect($approved->verification_status)->toBe(App\Models\CustomerProfile::ACTIVE);
});
