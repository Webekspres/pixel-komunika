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
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('allows customer to upload payment proof and admin to approve it', function () {
    Storage::fake('local');

    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $adminRole = Role::firstOrCreate(['code' => Role::ADMIN], ['name' => Role::ADMIN]);

    $customer = User::factory()->create(['role_id' => $customerRole->id]);
    CustomerProfile::create([
        'user_id' => $customer->id,
        'verification_status' => 'approved',
    ]);

    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $address = Address::create([
        'user_id' => $customer->id,
        'recipient_name' => 'Toko Komunika',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Merdeka No 123',
        'province_name' => 'DKI Jakarta',
        'city_name' => 'Jakarta Selatan',
        'district_name' => 'Kebayoran Baru',
        'postal_code' => '12110',
        'is_default' => true,
    ]);

    $product = Product::first();
    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($customer);
    $cartService->addItem($cart, $product->id, 2);

    $orderService = app(OrderService::class);
    $order = $orderService->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne',
        'service' => 'REG',
        'cost' => 15000,
    ]);

    expect($order->status)->toBe('unpaid');

    $paymentService = app(PaymentService::class);
    $file = UploadedFile::fake()->create('proof.jpg', 500, 'image/jpeg');

    $proof = $paymentService->uploadPaymentProof($order, $customer, [
        'bank_name' => 'BCA',
        'account_name' => 'Toko Komunika',
        'amount' => $order->grand_total,
    ], $file);

    expect($proof->status)->toBe('pending');
    expect($order->fresh()->status)->toBe('payment_pending');
    expect($order->invoice->fresh()->status)->toBe('payment_pending');

    // Admin approves payment
    $paymentService->approvePayment($proof, $admin);

    expect($proof->fresh()->status)->toBe('approved');
    expect($order->fresh()->status)->toBe('paid');
    expect($order->invoice->fresh()->status)->toBe('paid');
});

it('restores inventory stock when an order is cancelled', function () {
    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $customer = User::factory()->create(['role_id' => $customerRole->id]);
    CustomerProfile::create([
        'user_id' => $customer->id,
        'verification_status' => 'approved',
    ]);

    $address = Address::create([
        'user_id' => $customer->id,
        'recipient_name' => 'Toko Komunika',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Merdeka No 123',
        'province_name' => 'DKI Jakarta',
        'city_name' => 'Jakarta Selatan',
        'district_name' => 'Kebayoran Baru',
        'postal_code' => '12110',
        'is_default' => true,
    ]);

    $product = Product::first();
    $snapshotBefore = InventorySnapshot::where('product_id', $product->id)->first()->quantity_available;

    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($customer);
    $cartService->addItem($cart, $product->id, 3);

    $orderService = app(OrderService::class);
    $order = $orderService->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne',
        'service' => 'REG',
        'cost' => 15000,
    ]);

    $snapshotAfterOrder = InventorySnapshot::where('product_id', $product->id)->first()->quantity_available;
    expect($snapshotAfterOrder)->toBe($snapshotBefore - 3);

    // Cancel order
    $orderService->cancelOrder($order, 'Testing cancellation', 'SYSTEM');

    $snapshotAfterCancel = InventorySnapshot::where('product_id', $product->id)->first()->quantity_available;
    expect($snapshotAfterCancel)->toBe($snapshotBefore);
    expect($order->fresh()->status)->toBe('cancelled');
    expect($order->fresh()->cancellation_source)->toBe('SYSTEM');
});
