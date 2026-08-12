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

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('creates an order and invoice from cart and updates inventory ledger', function () {
    $cartService = app(CartService::class);
    $orderService = app(OrderService::class);

    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => 'Customer']);
    $user = User::factory()->create(['role_id' => $customerRole->id]);
    $user->customerProfile()->create([
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $address = Address::create([
        'user_id' => $user->id,
        'label' => 'Toko Utama',
        'recipient_name' => 'Budi Santoso',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Merdeka No. 10',
        'province_name' => 'DKI Jakarta',
        'city_name' => 'Jakarta Selatan',
        'district_name' => 'Kebayoran Baru',
        'postal_code' => '12110',
        'is_default' => true,
    ]);

    $cart = $cartService->getOrCreateCart($user);
    $product = Product::where('sku', 'PB-10000')->firstOrFail();
    $cartService->addItem($cart, $product->id, 2);

    $stockBefore = InventorySnapshot::where('product_id', $product->id)->first()->quantity_available;

    $order = $orderService->createOrderFromCart($user, $cart, $address, [
        'code' => 'jne',
        'service' => 'REG',
        'cost' => 15000,
    ]);

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->status)->toBe('unpaid')
        ->and($order->items)->toHaveCount(1)
        ->and($order->invoice)->not->toBeNull()
        ->and($order->invoice->status)->toBe('unpaid')
        ->and($order->invoice->store_name)->toBe(config('store.name'))
        ->and($order->invoice->store_npwp)->toBe(config('store.npwp'))
        ->and($order->tax_pph22_snapshot)->toBeArray();

    $stockAfter = InventorySnapshot::where('product_id', $product->id)->first()->quantity_available;
    expect($stockAfter)->toBe($stockBefore - 2);

    // Cart should be empty now
    $summary = $cartService->getCartSummary($cart);
    expect($summary['total_items'])->toBe(0);
});

it('allows active customers to access checkout and orders pages', function () {
    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => 'Customer']);
    $user = User::factory()->create(['role_id' => $customerRole->id]);
    $user->customerProfile()->create([
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $this->actingAs($user)
        ->get('/cart')
        ->assertOk();

    $this->actingAs($user)
        ->get('/orders')
        ->assertOk();
});

it('allows admin users to access admin orders management', function () {
    $adminRole = Role::firstOrCreate(['code' => Role::ADMIN], ['name' => 'Admin']);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin)
        ->get('/admin/orders')
        ->assertOk();
});
