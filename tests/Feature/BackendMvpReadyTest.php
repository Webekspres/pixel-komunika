<?php

use App\Domains\Notifications\Contracts\WhatsAppNotifierInterface;
use App\Domains\Notifications\FakeWhatsAppNotifier;
use App\Domains\Notifications\NotificationService;
use App\Domains\Order\FulfillmentService;
use App\Domains\PosIntegration\SamplePosSyncService;
use App\Domains\Reporting\ReportingService;
use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Address;
use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\CustomerProfile;
use App\Models\PosIntegrationOperation;
use App\Models\Product;
use App\Models\Role;
use App\Models\SalesReturn;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

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

function mvpCustomer(): array
{
    $customerRole = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $adminRole = Role::firstOrCreate(['code' => Role::ADMIN], ['name' => Role::ADMIN]);

    $customer = User::factory()->create(['role_id' => $customerRole->id]);
    CustomerProfile::create([
        'user_id' => $customer->id,
        'verification_status' => CustomerProfile::ACTIVE,
        'reseller_account_number' => 'PKR-000001',
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

it('creates charge components shipment payment and notifications on checkout', function () {
    ['customer' => $customer, 'address' => $address] = mvpCustomer();

    $product = Product::first();
    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($customer);
    $cartService->addItem($cart, $product->id, 2);

    $order = app(OrderService::class)->createOrderFromCart($customer, $cart, $address, [
        'provider' => 'STORE_COURIER',
        'code' => 'store',
        'service' => 'Kurir Toko',
        'cost' => 10000,
    ]);

    expect($order->shipment)->not->toBeNull()
        ->and($order->payment)->not->toBeNull()
        ->and($order->payment->status)->toBe('NOT_SUBMITTED')
        ->and(PosIntegrationOperation::where('order_id', $order->id)->where('operation', 'WEB_SALE_REPORT')->exists())->toBeTrue()
        ->and(AppNotification::where('order_id', $order->id)->where('type', 'NEW_ORDER')->count())->toBeGreaterThan(0);
});

it('rejects payment with audit and retain_until on upload', function () {
    Storage::fake('local');
    ['customer' => $customer, 'admin' => $admin, 'address' => $address] = mvpCustomer();

    $product = Product::first();
    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($customer);
    $cartService->addItem($cart, $product->id, 1);

    $order = app(OrderService::class)->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne', 'service' => 'REG', 'cost' => 15000,
    ]);

    $paymentService = app(PaymentService::class);
    $proof = $paymentService->uploadPaymentProof($order, $customer, [
        'bank_name' => 'BCA',
        'account_name' => 'Toko',
        'amount' => $order->grand_total,
    ], UploadedFile::fake()->image('bukti.jpg'));

    expect($proof->retain_until)->not->toBeNull();

    $paymentService->rejectPayment($proof, 'Nominal tidak cocok', $admin);

    expect($proof->fresh()->status)->toBe('rejected')
        ->and($order->fresh()->status)->toBe('payment_rejected')
        ->and(AuditLog::where('action', 'PAYMENT_REJECTED')->exists())->toBeTrue();
});

it('processes return approve into sales_return outbox', function () {
    ['customer' => $customer, 'admin' => $admin, 'address' => $address] = mvpCustomer();

    $product = Product::first();
    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($customer);
    $cartService->addItem($cart, $product->id, 1);

    $order = app(OrderService::class)->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne', 'service' => 'REG', 'cost' => 10000,
    ]);

    $order->update(['status' => 'shipped']);
    $order->shipment->update(['status' => 'SHIPPED', 'shipped_at' => now()]);

    $return = app(OrderService::class)->requestReturn($order, $customer, 'Barang rusak');
    app(OrderService::class)->processReturn($return, 'approve', null, $admin, 'OK');

    expect($return->fresh()->status)->toBe('approved')
        ->and(SalesReturn::where('order_id', $order->id)->exists())->toBeTrue()
        ->and(PosIntegrationOperation::where('operation', 'WEB_RETURN_REPORT')->where('order_id', $order->id)->exists())->toBeTrue();
});

it('holds auto-complete when shipment is TERKENDALA and completes otherwise', function () {
    ['customer' => $customer, 'admin' => $admin, 'address' => $address] = mvpCustomer();

    $product = Product::first();
    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($customer);
    $cartService->addItem($cart, $product->id, 1);
    $order = app(OrderService::class)->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne', 'service' => 'REG', 'cost' => 10000,
    ]);

    $order->update(['status' => 'paid']);
    $fulfillment = app(FulfillmentService::class);
    $fulfillment->transition($order->fresh(), 'processing', $admin);
    $fulfillment->transition($order->fresh(), 'packed', $admin);
    $fulfillment->transition($order->fresh(), 'shipped', $admin, 'RESI-1');

    $fulfillment->markTerkendala($order->fresh(), 'Kurir hilang', $admin);
    $order->shipment->update(['shipped_at' => now()->subDays(20)]);

    $this->artisan('orders:auto-complete-shipped')->assertSuccessful();
    expect($order->fresh()->status)->toBe('shipped');

    $fulfillment->resolveTerkendala($order->fresh(), $admin);
    $this->artisan('orders:auto-complete-shipped')->assertSuccessful();
    expect($order->fresh()->status)->toBe('completed');
});

it('dispatches sample pos sync and sale acks', function () {
    $run = app(SamplePosSyncService::class)->syncMasters();
    expect($run->status)->toBe('SUCCEEDED');

    ['customer' => $customer, 'address' => $address] = mvpCustomer();
    $product = Product::first();
    $cart = app(CartService::class)->getOrCreateCart($customer);
    app(CartService::class)->addItem($cart, $product->id, 1);
    $order = app(OrderService::class)->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne', 'service' => 'REG', 'cost' => 10000,
    ]);

    $count = app(SamplePosSyncService::class)->dispatchPendingSaleReports();
    expect($count)->toBeGreaterThan(0)
        ->and(PosIntegrationOperation::where('order_id', $order->id)->value('status'))->toBe('SUCCEEDED');
});

it('sends pending whatsapp via stub and reports shipped omzet', function () {
    $fake = new FakeWhatsAppNotifier;
    $this->app->instance(WhatsAppNotifierInterface::class, $fake);

    ['customer' => $customer, 'address' => $address] = mvpCustomer();
    $product = Product::first();
    $cart = app(CartService::class)->getOrCreateCart($customer);
    app(CartService::class)->addItem($cart, $product->id, 1);
    $order = app(OrderService::class)->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne', 'service' => 'REG', 'cost' => 10000,
    ]);

    $sent = (new NotificationService($fake))->dispatchPendingWhatsApp();
    expect($sent)->toBeGreaterThan(0)->and($fake->sent)->not->toBeEmpty();

    $order->update(['status' => 'shipped']);
    $summary = app(ReportingService::class)->salesSummary();
    expect($summary['order_count'])->toBeGreaterThan(0);
});

it('denies cross-user order policy', function () {
    ['customer' => $owner, 'address' => $address] = mvpCustomer();
    $other = User::factory()->activeCustomer()->create();

    $product = Product::first();
    $cart = app(CartService::class)->getOrCreateCart($owner);
    app(CartService::class)->addItem($cart, $product->id, 1);
    $order = app(OrderService::class)->createOrderFromCart($owner, $cart, $address, [
        'code' => 'jne', 'service' => 'REG', 'cost' => 10000,
    ]);

    expect(Gate::forUser($other)->allows('view', $order))->toBeFalse()
        ->and(Gate::forUser($owner)->allows('view', $order))->toBeTrue();
});
