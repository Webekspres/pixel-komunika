<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Address;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('renders customer account dashboard overview with user greeting and transaction stats', function () {
    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $customer = User::factory()->create([
        'name' => 'Toko Jaya Abadi',
        'email' => 'jaya@example.com',
        'phone' => '08123456789',
        'role_id' => $customerRole->id,
    ]);

    CustomerProfile::create([
        'user_id' => $customer->id,
        'business_name' => 'PT Jaya Abadi',
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $address = Address::create([
        'user_id' => $customer->id,
        'recipient_name' => 'Budi Jaya',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Merdeka No. 12',
        'province_name' => 'Jawa Barat',
        'city_name' => 'Bandung',
        'district_name' => 'Coblong',
        'postal_code' => '40132',
        'is_default' => true,
    ]);

    $product = Product::first();
    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($customer);
    $cartService->addItem($cart, $product->id, 1);

    $orderService = app(OrderService::class);
    $order = $orderService->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne',
        'service' => 'REG',
        'cost' => 15000,
    ]);

    $this->actingAs($customer)
        ->get(route('account.dashboard'))
        ->assertOk()
        ->assertSee('Toko Jaya Abadi')
        ->assertSee('PT Jaya Abadi')
        ->assertSee('Aktif')
        ->assertSee('Menunggu Pembayaran')
        ->assertSee('Pesanan Diproses')
        ->assertSee('Sedang Dikirim')
        ->assertSee('Alamat Tersimpan')
        ->assertSee('Pesanan Terbaru')
        ->assertSee('#' . $order->order_number)
        ->assertSee('Unggah Bukti Bayar')
        ->assertSee('Profil Toko')
        ->assertSee('Alamat Utama')
        ->assertSee('Budi Jaya');
});

it('renders customer orders list page with filters and order cards', function () {
    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $customer = User::factory()->create([
        'role_id' => $customerRole->id,
    ]);

    CustomerProfile::create([
        'user_id' => $customer->id,
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $this->actingAs($customer)
        ->get(route('orders.index'))
        ->assertOk()
        ->assertSee('Semua')
        ->assertSee('Menunggu Pembayaran')
        ->assertSee('Diproses')
        ->assertSee('Dikirim');
});

it('renders printable order invoice page for the order owner', function () {
    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $customer = User::factory()->create([
        'role_id' => $customerRole->id,
    ]);

    CustomerProfile::create([
        'user_id' => $customer->id,
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $address = Address::create([
        'user_id' => $customer->id,
        'recipient_name' => 'Budi Jaya',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Merdeka No. 12',
        'province_name' => 'Jawa Barat',
        'city_name' => 'Bandung',
        'district_name' => 'Coblong',
        'postal_code' => '40132',
        'is_default' => true,
    ]);

    $product = Product::first();
    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($customer);
    $cartService->addItem($cart, $product->id, 1);

    $orderService = app(OrderService::class);
    $order = $orderService->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne',
        'service' => 'REG',
        'cost' => 15000,
    ]);

    $this->actingAs($customer)
        ->get(route('orders.invoice', $order))
        ->assertOk()
        ->assertSee('FAKTUR PENJUALAN')
        ->assertSee($order->order_number);
});

