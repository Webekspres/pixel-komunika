<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Livewire\Admin\AdminOrders;
use App\Livewire\Admin\OrderFulfillmentActions;
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

it('transitions orders through fulfillment workflow from the detail component', function () {
    $admin = User::factory()->admin()->create();
    $order = ordersFixtureOrder();
    $order->update(['status' => 'paid']);

    Livewire::actingAs($admin)
        ->test(OrderFulfillmentActions::class, ['order' => $order])
        ->call('transitionStatus', 'processing')
        ->assertHasNoErrors();

    expect($order->fresh()->status)->toBe('processing');

    Livewire::actingAs($admin)
        ->test(OrderFulfillmentActions::class, ['order' => $order])
        ->call('transitionStatus', 'packed')
        ->assertHasNoErrors();

    expect($order->fresh()->status)->toBe('packed');
});

it('rejects invalid status transitions with an error flash', function () {
    $admin = User::factory()->admin()->create();
    $order = ordersFixtureOrder();
    $order->update(['status' => 'paid']);

    Livewire::actingAs($admin)
        ->test(OrderFulfillmentActions::class, ['order' => $order])
        ->call('transitionStatus', 'shipped')
        ->assertHasNoErrors();

    // Transisi tidak diizinkan: status tidak berubah dan tidak ada exception bocor.
    expect($order->fresh()->status)->toBe('paid');
});

it('cancels a same-day unpaid order via the detail component', function () {
    $admin = User::factory()->admin()->create();
    $order = ordersFixtureOrder();

    expect($order->status)->toBe('unpaid');

    Livewire::actingAs($admin)
        ->test(OrderFulfillmentActions::class, ['order' => $order])
        ->set('cancelReason', 'Pelanggan meminta pembatalan')
        ->call('confirmCancelOrder')
        ->assertHasNoErrors();

    $order->refresh();
    expect($order->status)->toBe('cancelled')
        ->and($order->cancellation_source)->toBe('ADMIN')
        ->and($order->cancelled_by_user_id)->toBe($admin->id)
        ->and($order->cancellation_reason)->toBe('Pelanggan meminta pembatalan');
});

it('rejects admin cancellation outside the same transaction day (FR-ORD-006)', function () {
    $admin = User::factory()->admin()->create();
    $order = ordersFixtureOrder();
    $order->update(['order_date_local' => now('Asia/Jakarta')->subDay()->toDateString()]);

    Livewire::actingAs($admin)
        ->test(OrderFulfillmentActions::class, ['order' => $order])
        ->set('cancelReason', 'Alasan pembatalan lama')
        ->call('confirmCancelOrder')
        ->assertHasNoErrors();

    expect($order->fresh()->status)->toBe('unpaid');
});

it('requires a cancellation reason of at least 5 characters', function () {
    $admin = User::factory()->admin()->create();
    $order = ordersFixtureOrder();

    Livewire::actingAs($admin)
        ->test(OrderFulfillmentActions::class, ['order' => $order])
        ->set('cancelReason', 'abc')
        ->call('confirmCancelOrder')
        ->assertHasErrors(['cancelReason']);

    expect($order->fresh()->status)->toBe('unpaid');
});

it('exposes cancel option on the list only for same-day unpaid/payment_pending orders', function () {
    $admin = User::factory()->admin()->create();
    $order = ordersFixtureOrder();

    Livewire::actingAs($admin)
        ->test(AdminOrders::class)
        ->assertSee('Batalkan Pesanan')
        ->assertSee($order->order_number);

    $order->update(['order_date_local' => now('Asia/Jakarta')->subDay()->toDateString()]);

    Livewire::actingAs($admin)
        ->test(AdminOrders::class)
        ->assertDontSee('Batalkan Pesanan');
});

it('renders shipped orders with terkendala action in order list', function () {
    $admin = User::factory()->admin()->create();
    $order = ordersFixtureOrder();
    $order->update(['status' => 'shipped']);

    Livewire::actingAs($admin)
        ->test(AdminOrders::class)
        ->assertSee('Tandai Terkendala')
        ->assertSee($order->order_number)
        ->assertOk();
});
