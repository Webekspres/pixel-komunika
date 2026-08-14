<?php

use App\Domains\Notifications\NotificationService;
use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Livewire\Admin\AdminOrders;
use App\Livewire\Admin\Notifications;
use App\Models\AppNotification;
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

function notificationFixtureOrder(User $customer): Order
{
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

it('creates database and whatsapp new-order notifications when an order is created', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->activeCustomer()->create();
    $order = notificationFixtureOrder($customer);

    $dbNotifications = AppNotification::query()
        ->where('order_id', $order->id)
        ->where('channel', AppNotification::CHANNEL_DATABASE)
        ->where('type', AppNotification::TYPE_NEW_ORDER)
        ->get();

    expect($dbNotifications->count())->toBeGreaterThanOrEqual(1)
        ->and($dbNotifications->first()->user_id)->toBe($admin->id)
        ->and($dbNotifications->first()->data['order_number'])->toBe($order->order_number)
        ->and($dbNotifications->first()->data['customer'])->toBe($order->recipient_name)
        ->and($dbNotifications->first()->read_at)->toBeNull();

    $whatsApp = AppNotification::query()
        ->where('order_id', $order->id)
        ->where('channel', AppNotification::CHANNEL_WHATSAPP)
        ->where('type', AppNotification::TYPE_NEW_ORDER)
        ->first();

    expect($whatsApp)->not->toBeNull()
        ->and($whatsApp->status)->toBe(AppNotification::PENDING)
        ->and($whatsApp->data['phone'])->toBe(config('store.whatsapp.admin_order_phone'))
        ->and($whatsApp->data['message'])->toBe('Cek Order masuk');
});

it('shows unread count in the admin notifications bell', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->activeCustomer()->create();
    $order = notificationFixtureOrder($customer);

    Livewire::actingAs($admin)
        ->test(Notifications::class)
        ->assertSee('Order baru masuk')
        ->assertSee($order->order_number);

    expect(app(NotificationService::class)->unreadCount($admin))->toBe(1);
});

it('marks new order notifications read when admin opens the orders list (FR-NTF-001)', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->activeCustomer()->create();
    notificationFixtureOrder($customer);

    expect(app(NotificationService::class)->unreadCount($admin))->toBe(1);

    Livewire::actingAs($admin)
        ->test(AdminOrders::class)
        ->assertHasNoErrors();

    expect(app(NotificationService::class)->unreadCount($admin))->toBe(0)
        ->and(AppNotification::query()->where('channel', AppNotification::CHANNEL_DATABASE)->whereNull('read_at')->count())->toBe(0);
});

it('marks a single notification read and navigates to the order', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->activeCustomer()->create();
    $order = notificationFixtureOrder($customer);

    $notification = AppNotification::query()
        ->where('order_id', $order->id)
        ->where('channel', AppNotification::CHANNEL_DATABASE)
        ->firstOrFail();

    Livewire::actingAs($admin)
        ->test(Notifications::class)
        ->call('openNotification', $notification->id)
        ->assertHasNoErrors();

    expect($notification->fresh()->read_at)->not->toBeNull()
        ->and(app(NotificationService::class)->unreadCount($admin))->toBe(0);
});

it('marks all notifications read via the bell action', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->activeCustomer()->create();
    notificationFixtureOrder($customer);

    Livewire::actingAs($admin)
        ->test(Notifications::class)
        ->call('markAllRead')
        ->assertHasNoErrors();

    expect(app(NotificationService::class)->unreadCount($admin))->toBe(0);
});

it('does not leak notifications to non-admins', function () {
    $customer = User::factory()->activeCustomer()->create();

    Livewire::actingAs($customer)
        ->test(Notifications::class)
        ->assertSee('Belum ada notifikasi');
});
