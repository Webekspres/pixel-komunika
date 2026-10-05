<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Livewire\Customer\OrderDetail;
use App\Models\Address;
use App\Models\BankAccount;
use App\Models\CustomerProfile;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('renders customer order detail page with unified cards, stepper, and bank accounts', function () {
    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $customer = User::factory()->create(['role_id' => $customerRole->id]);
    CustomerProfile::create([
        'user_id' => $customer->id,
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $address = Address::create([
        'user_id' => $customer->id,
        'recipient_name' => 'Budi Santoso',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Sudirman No. 45',
        'province_name' => 'DKI Jakarta',
        'city_name' => 'Jakarta Selatan',
        'district_name' => 'Kebayoran Baru',
        'postal_code' => '12110',
        'is_default' => true,
    ]);

    BankAccount::create([
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Pixel Komunika',
        'instructions' => 'Transfer ke BCA',
        'is_active' => true,
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

    Livewire::actingAs($customer)
        ->test(OrderDetail::class, ['order' => $order])
        ->assertOk()
        ->assertSee('Status Alur Pesanan')
        ->assertSee('Rincian Produk & Biaya')
        ->assertSee('Total Tagihan')
        ->assertSee('Upload Bukti Pembayaran')
        ->assertSee('Kirim Bukti Pembayaran')
        ->assertSee('BCA')
        ->assertSee('1234567890')
        ->assertSee('Pixel Komunika')
        ->assertSee('Klik atau seret file bukti transfer ke sini')
        ->assertSee('JPG, PNG, PDF maks 5MB')
        ->call('copyToClipboard', '1234567890')
        ->assertDispatched('copy-to-clipboard', text: '1234567890');
});

it('handles payment proof upload and preview safely for images and pdfs', function () {
    Storage::fake('local');

    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $customer = User::factory()->create(['role_id' => $customerRole->id]);
    CustomerProfile::create([
        'user_id' => $customer->id,
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $address = Address::create([
        'user_id' => $customer->id,
        'recipient_name' => 'Budi Santoso',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Sudirman No. 45',
        'province_name' => 'DKI Jakarta',
        'city_name' => 'Jakarta Selatan',
        'district_name' => 'Kebayoran Baru',
        'postal_code' => '12110',
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

    $imageFile = UploadedFile::fake()->image('transfer-proof.png');

    Livewire::actingAs($customer)
        ->test(OrderDetail::class, ['order' => $order])
        ->set('bank_name', 'BCA')
        ->set('account_name', 'Budi Santoso')
        ->set('amount', (string) $order->grand_total)
        ->set('proof_file', $imageFile)
        ->call('uploadPaymentProof')
        ->assertHasNoErrors();

    expect($order->fresh()->status)->toBe('payment_pending');
});

it('hides customer cancel and offers whatsapp return only after shipping', function () {
    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $customer = User::factory()->create(['role_id' => $customerRole->id]);
    CustomerProfile::create([
        'user_id' => $customer->id,
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $address = Address::create([
        'user_id' => $customer->id,
        'recipient_name' => 'Budi Santoso',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Sudirman No. 45',
        'province_name' => 'DKI Jakarta',
        'city_name' => 'Jakarta Selatan',
        'district_name' => 'Kebayoran Baru',
        'postal_code' => '12110',
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

    // Pembatalan hanya oleh admin/sistem (FR-ORD-006/007).
    Livewire::actingAs($customer)
        ->test(OrderDetail::class, ['order' => $order])
        ->assertDontSee('Batalkan Pesanan')
        ->assertDontSee('Ajukan Retur via WhatsApp');

    $order->update(['status' => 'shipped']);

    Livewire::actingAs($customer)
        ->test(OrderDetail::class, ['order' => $order->fresh()])
        ->assertSee('Ajukan Retur via WhatsApp')
        ->assertSeeHtml('https://wa.me/6281546407702?text=')
        ->assertSee($order->order_number);
});
