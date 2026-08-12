<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Address;
use App\Models\CustomerProfile;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;

beforeEach(function () {
    config(['services.pos.api_token' => 'test-pos-token']);
    app(SampleCatalogImporter::class)->import();
});

it('returns order acknowledgement payload for pos with valid token', function () {
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
    $cartService->addItem($cart, $product->id, 5);

    $order = $orderService->createOrderFromCart($user, $cart, $address, [
        'code' => 'jne',
        'service' => 'REG',
        'cost' => 15000,
    ]);

    $this->getJson('/api/pos/orders/'.$order->order_number, [
        'Authorization' => 'Bearer test-pos-token',
    ])
        ->assertOk()
        ->assertJsonPath('order_number', $order->order_number)
        ->assertJsonPath('status', 'unpaid')
        ->assertJsonPath('invoice.store_name', config('store.name'))
        ->assertJsonStructure(['acknowledged_at', 'items']);
});

it('rejects pos requests without a valid token', function () {
    $this->getJson('/api/pos/orders/PK-MISSING')
        ->assertUnauthorized();
});
