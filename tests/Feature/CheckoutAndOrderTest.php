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
    $cartService->addItem($cart, $product->id, 6);

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
    expect($stockAfter)->toBe($stockBefore - 6);

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

// CR-023: nomor invoice web WEB-[yymm]-[0001], berurutan dan kembali ke 0001 tiap bulan.
it('numbers web invoices sequentially per month', function () {
    $this->travelTo(now()->setDate(2026, 10, 31)->setTime(10, 0));
    $customer = User::factory()->activeCustomer()->create();
    $address = $customer->addresses()->create([
        'recipient_name' => 'Toko', 'recipient_phone' => '08123456789', 'address_line' => 'Jl. Merdeka 1',
        'province_name' => 'Jawa Barat', 'city_name' => 'Bandung', 'district_name' => 'Coblong', 'postal_code' => '40135', 'is_default' => true,
    ]);
    $cartService = app(CartService::class);
    $productId = Product::where('sku', 'PB-10000')->value('id');
    $invoiceNumber = function () use ($cartService, $customer, $address, $productId) {
        $cart = $cartService->getOrCreateCart($customer);
        $cartService->addItem($cart, $productId, 5);

        return app(OrderService::class)->createOrderFromCart($customer, $cart, $address, ['code' => 'jne', 'service' => 'REG', 'cost' => 10000])->invoice->invoice_number;
    };

    expect($invoiceNumber())->toBe('WEB-2610-0001')
        ->and($invoiceNumber())->toBe('WEB-2610-0002');

    $this->travelTo(now()->setDate(2026, 11, 1)->setTime(9, 0));

    expect($invoiceNumber())->toBe('WEB-2611-0001');
});
