<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Address;
use App\Models\BankAccount;
use App\Models\CustomerProfile;
use App\Models\Product;
use App\Models\Role;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();

    StoreProfile::query()->updateOrCreate(
        ['store_name' => 'Pixel Komunika'],
        [
            'address' => 'Jl. Test',
            'contact_number' => '081546407702',
            'company_npwp' => '00.000.000.0-000.000',
            'partai_minimum_quantity' => 5,
            'is_active' => true,
        ],
    );

    BankAccount::query()->updateOrCreate(
        ['account_number' => '999988887777'],
        [
            'bank_name' => 'BCA',
            'account_holder' => 'Pixel Komunika',
            'is_active' => true,
        ],
    );
});

function invoiceOrderActors(): array
{
    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $adminRole = Role::firstOrCreate(['code' => Role::ADMIN], ['name' => Role::ADMIN]);

    $customer = User::factory()->create(['role_id' => $customerRole->id]);
    CustomerProfile::create([
        'user_id' => $customer->id,
        'verification_status' => CustomerProfile::ACTIVE,
        'reseller_account_number' => 'PKR-000088',
    ]);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $other = User::factory()->create(['role_id' => $customerRole->id]);
    CustomerProfile::create([
        'user_id' => $other->id,
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $address = Address::create([
        'user_id' => $customer->id,
        'recipient_name' => 'Toko',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Merdeka 1',
        'province_name' => 'Jawa Barat',
        'city_name' => 'Bandung',
        'district_name' => 'Coblong',
        'postal_code' => '40135',
        'is_default' => true,
    ]);

    $product = Product::first();
    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($customer);
    $cartService->addItem($cart, $product->id, 1);

    $order = app(OrderService::class)->createOrderFromCart($customer, $cart, $address, [
        'provider' => 'STORE_COURIER',
        'code' => 'store',
        'service' => 'Kurir Toko',
        'cost' => 10000,
    ]);

    return compact('customer', 'admin', 'other', 'order');
}

it('lets owner download invoice pdf', function () {
    ['customer' => $customer, 'order' => $order] = invoiceOrderActors();

    $this->actingAs($customer)
        ->get(route('orders.invoice.download', $order))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('lets admin download invoice pdf', function () {
    ['admin' => $admin, 'order' => $order] = invoiceOrderActors();

    $this->actingAs($admin)
        ->get(route('orders.invoice.download', $order))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('forbids other customers from downloading invoice pdf', function () {
    ['other' => $other, 'order' => $order] = invoiceOrderActors();

    $this->actingAs($other)
        ->get(route('orders.invoice.download', $order))
        ->assertForbidden();
});
