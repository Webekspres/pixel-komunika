<?php

use App\Domains\Notifications\FakeWhatsAppNotifier;
use App\Domains\Notifications\Contracts\WhatsAppNotifierInterface;
use App\Domains\Order\FulfillmentService;
use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Address;
use App\Models\AppNotification;
use App\Models\BankAccount;
use App\Models\CustomerProfile;
use App\Models\Product;
use App\Models\Role;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;

beforeEach(function () {
    app()->instance(WhatsAppNotifierInterface::class, new FakeWhatsAppNotifier);
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

function receiptCustomer(): array
{
    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $adminRole = Role::firstOrCreate(['code' => Role::ADMIN], ['name' => Role::ADMIN]);

    $customer = User::factory()->create(['role_id' => $customerRole->id]);
    CustomerProfile::create([
        'user_id' => $customer->id,
        'verification_status' => CustomerProfile::ACTIVE,
        'reseller_account_number' => 'PKR-000099',
    ]);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

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

    return compact('customer', 'admin', 'address');
}

function shippedOrderWithToken(): array
{
    ['customer' => $customer, 'admin' => $admin, 'address' => $address] = receiptCustomer();

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

    $order->update(['status' => 'packed']);
    $fulfillment = app(FulfillmentService::class);
    $order = $fulfillment->transition($order->fresh(), 'shipped', $admin, 'RESI-TEST');

    $notification = AppNotification::query()
        ->where('order_id', $order->id)
        ->where('type', AppNotification::TYPE_RECEIPT_CONFIRMATION)
        ->first();

    expect($notification)->not->toBeNull();
    $url = $notification->data['url'] ?? '';
    parse_str(parse_url($url, PHP_URL_QUERY) ?: '', $query);
    $token = $query['token'] ?? '';

    expect($token)->not->toBeEmpty();

    return compact('order', 'token', 'admin');
}

it('confirms receipt via public link and completes order', function () {
    ['order' => $order, 'token' => $token] = shippedOrderWithToken();

    $this->get(route('orders.confirm-receipt', ['order' => $order, 'token' => $token]))
        ->assertOk()
        ->assertSee('pesanan selesai', false);

    expect($order->fresh()->status)->toBe('completed')
        ->and($order->fresh()->receipt_confirmed_at)->not->toBeNull();
});

it('rejects invalid receipt token', function () {
    ['order' => $order] = shippedOrderWithToken();

    $this->get(route('orders.confirm-receipt', ['order' => $order, 'token' => 'wrong-token']))
        ->assertStatus(410);

    expect($order->fresh()->status)->toBe('shipped');
});
