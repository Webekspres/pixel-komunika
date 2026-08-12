<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Address;
use App\Models\CustomerProfile;
use App\Models\InventorySnapshot;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('auto cancels unpaid orders from previous calendar days', function () {
    $orderService = app(OrderService::class);
    $cartService = app(CartService::class);

    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => 'Customer']);
    $user = User::factory()->create(['role_id' => $customerRole->id]);
    $user->customerProfile()->create(['verification_status' => CustomerProfile::ACTIVE]);

    $address = Address::create([
        'user_id' => $user->id,
        'label' => 'Toko',
        'recipient_name' => 'Budi',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Test',
        'province_name' => 'Jawa Barat',
        'city_name' => 'Bandung',
        'district_name' => 'Coblong',
        'postal_code' => '40135',
        'is_default' => true,
    ]);

    $cart = $cartService->getOrCreateCart($user);
    $product = Product::where('sku', 'PB-10000')->firstOrFail();
    $stockBefore = InventorySnapshot::where('product_id', $product->id)->value('quantity_available');

    $cartService->addItem($cart, $product->id, 2);
    $order = $orderService->createOrderFromCart($user, $cart, $address, ['code' => 'jne', 'service' => 'REG', 'cost' => 0]);

    $order->forceFill(['created_at' => now()->subDays(2)])->save();

    Artisan::call('orders:auto-cancel-unpaid');

    expect($order->fresh()->status)->toBe('cancelled');

    $stockAfter = InventorySnapshot::where('product_id', $product->id)->value('quantity_available');
    expect($stockAfter)->toBe($stockBefore);
});

it('does not auto cancel todays unpaid orders', function () {
    $orderService = app(OrderService::class);
    $cartService = app(CartService::class);

    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => 'Customer']);
    $user = User::factory()->create(['role_id' => $customerRole->id]);
    $user->customerProfile()->create(['verification_status' => CustomerProfile::ACTIVE]);

    $address = Address::create([
        'user_id' => $user->id,
        'label' => 'Toko',
        'recipient_name' => 'Budi',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Test',
        'province_name' => 'Jawa Barat',
        'city_name' => 'Bandung',
        'district_name' => 'Coblong',
        'postal_code' => '40135',
        'is_default' => true,
    ]);

    $cart = $cartService->getOrCreateCart($user);
    $product = Product::where('sku', 'PB-10000')->firstOrFail();
    $cartService->addItem($cart, $product->id, 1);
    $order = $orderService->createOrderFromCart($user, $cart, $address, ['code' => 'jne', 'service' => 'REG', 'cost' => 0]);

    Artisan::call('orders:auto-cancel-unpaid');

    expect($order->fresh()->status)->toBe('unpaid');
});
