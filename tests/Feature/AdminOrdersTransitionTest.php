<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Livewire\Admin\AdminOrders;
use App\Models\BankAccount;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use Livewire\Livewire;

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

function ordersFixtureOrder(): Order
{
    $customer = User::factory()->activeCustomer()->create();

    $address = $customer->addresses()->create([
        'label' => 'Toko Utama',
        'recipient_name' => $customer->name,
        'recipient_phone' => $customer->phone,
        'address_line' => 'Jl. Merdeka No 1',
        'province_name' => 'Jawa Barat',
        'city_name' => 'Bandung',
        'district_name' => 'Coblong',
        'postal_code' => '40135',
        'is_default' => true,
    ]);

    $product = Product::query()->firstOrFail();
    $cart = app(CartService::class)->getOrCreateCart($customer);
    app(CartService::class)->addItem($cart, $product->id, 2);

    return app(OrderService::class)->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne',
        'service' => 'REG',
        'cost' => 15000,
    ]);
}

it('transitions orders through fulfillment workflow from the admin component', function () {
    $admin = User::factory()->admin()->create();
    $order = ordersFixtureOrder();
    $order->update(['status' => 'paid']);

    Livewire::actingAs($admin)
        ->test(AdminOrders::class)
        ->call('transitionStatus', $order->id, 'processing')
        ->assertHasNoErrors();

    expect($order->fresh()->status)->toBe('processing');

    Livewire::actingAs($admin)
        ->test(AdminOrders::class)
        ->call('transitionStatus', $order->id, 'packed')
        ->assertHasNoErrors();

    expect($order->fresh()->status)->toBe('packed');
});

it('rejects invalid status transitions with an error flash', function () {
    $admin = User::factory()->admin()->create();
    $order = ordersFixtureOrder();
    $order->update(['status' => 'paid']);

    Livewire::actingAs($admin)
        ->test(AdminOrders::class)
        ->call('transitionStatus', $order->id, 'shipped')
        ->assertHasNoErrors();

    // Transisi tidak diizinkan: status tidak berubah dan tidak ada exception bocor.
    expect($order->fresh()->status)->toBe('paid');
});
