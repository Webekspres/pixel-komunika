<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Livewire\Admin\AdminPayments;
use App\Livewire\Admin\OrderFulfillmentActions;
use App\Livewire\Customer\OrderDetail;
use App\Models\BankAccount;
use App\Models\CustomerProfile;
use App\Models\InventorySnapshot;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('local');
    app(SampleCatalogImporter::class)->import();
    StoreProfile::query()->updateOrCreate(['store_name' => 'Pixel Komunika'], [
        'address' => 'Jl. Test', 'contact_number' => '081546407702',
        'company_npwp' => '00.000.000.0-000.000', 'partai_minimum_quantity' => 5, 'is_active' => true,
    ]);
    BankAccount::query()->updateOrCreate(['account_number' => '999988887777'], [
        'bank_name' => 'BCA', 'account_holder' => 'Pixel Komunika', 'is_active' => true,
    ]);
});

function lifecycleOrder(): Order
{
    $customer = User::factory()->activeCustomer()->create();
    $address = $customer->addresses()->create([
        'label' => 'Toko', 'recipient_name' => $customer->name, 'recipient_phone' => $customer->phone,
        'address_line' => 'Jl. Merdeka 1', 'province_name' => 'Jawa Barat', 'city_name' => 'Bandung',
        'district_name' => 'Coblong', 'postal_code' => '40135', 'is_default' => true,
    ]);
    $product = Product::query()->firstOrFail();
    $cart = app(CartService::class)->getOrCreateCart($customer);
    app(CartService::class)->addItem($cart, $product->id, 2);

    return app(OrderService::class)->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne', 'service' => 'REG', 'cost' => 15000,
    ]);
}

function lifecycleUpload(Order $order)
{
    return app(PaymentService::class)->uploadPaymentProof($order, $order->user, [
        'bank_name' => 'BCA', 'account_name' => 'X', 'amount' => 100000,
    ], UploadedFile::fake()->create('p.jpg', 100, 'image/jpeg'));
}

it('PAY-04/FR-ORD-008: approving a pending proof of an admin-cancelled order must not resurrect it', function () {
    $admin = User::factory()->admin()->create();
    $order = lifecycleOrder();
    $proof = lifecycleUpload($order);
    app(OrderService::class)->cancelOrder($order->fresh(), 'batal hari ini', 'ADMIN', $admin);

    Livewire::actingAs($admin)->test(AdminPayments::class)
        ->call('openReview', $proof->id)
        ->call('approvePayment');

    expect($order->fresh()->status)->toBe('cancelled');
});

it('FR-ORD-008: order detail page still offers Setujui for a cancelled order with pending proof', function () {
    $admin = User::factory()->admin()->create();
    $order = lifecycleOrder();
    lifecycleUpload($order);
    app(OrderService::class)->cancelOrder($order->fresh(), 'batal hari ini', 'ADMIN', $admin);

    Livewire::actingAs($admin)->test(OrderFulfillmentActions::class, ['order' => $order->fresh()])
        ->call('approvePayment');

    expect($order->fresh()->status)->toBe('cancelled');
});

it('PAY-02/UF-10: customer cannot upload proof on a completed order via Livewire call', function () {
    $order = lifecycleOrder();
    $order->update(['status' => 'completed']);

    Livewire::actingAs($order->user)->test(OrderDetail::class, ['order' => $order])
        ->set('bank_name', 'BCA')->set('account_name', 'X')->set('amount', '1000')
        ->set('proof_file', UploadedFile::fake()->create('p.jpg', 100, 'image/jpeg'))
        ->call('uploadPaymentProof');

    expect($order->fresh()->status)->toBe('completed');
});

it('PAY-02/FR-ORD-008: customer cannot upload proof on a cancelled order via Livewire call', function () {
    $order = lifecycleOrder();
    app(OrderService::class)->cancelOrder($order, 'x', 'SYSTEM');

    Livewire::actingAs($order->user)->test(OrderDetail::class, ['order' => $order->fresh()])
        ->set('bank_name', 'BCA')->set('account_name', 'X')->set('amount', '1000')
        ->set('proof_file', UploadedFile::fake()->create('p.jpg', 100, 'image/jpeg'))
        ->call('uploadPaymentProof');

    expect($order->fresh()->status)->toBe('cancelled');
});

it('CAN-02/K-7: transitionStatus(cancelled) must not cancel a processing order', function () {
    $admin = User::factory()->admin()->create();
    $order = lifecycleOrder();
    $order->update(['status' => 'processing']);
    $stockBefore = InventorySnapshot::where('product_id', $order->items->first()->product_id)->value('quantity_available');

    Livewire::actingAs($admin)->test(OrderFulfillmentActions::class, ['order' => $order])
        ->call('transitionStatus', 'cancelled');

    $fresh = $order->fresh();
    $stockAfter = InventorySnapshot::where('product_id', $order->items->first()->product_id)->value('quantity_available');
    // Either refused, or (if allowed) must go through cancelOrder with stock + reason.
    expect($fresh->status)->toBe('processing')
        ->and($stockAfter)->toBe($stockBefore);
});

it('PAY-04: re-approving an already approved proof must not regress a shipped order', function () {
    $admin = User::factory()->admin()->create();
    $order = lifecycleOrder();
    $proof = lifecycleUpload($order);
    app(PaymentService::class)->approvePayment($proof->fresh(), $admin);
    $order->refresh()->update(['status' => 'shipped']);

    Livewire::actingAs($admin)->test(AdminPayments::class)
        ->call('openReview', $proof->id)
        ->call('approvePayment');

    expect($order->fresh()->status)->toBe('shipped');
});

it('PAY-05: rejecting an already approved proof must not flip a paid order back to payment_rejected', function () {
    $admin = User::factory()->admin()->create();
    $order = lifecycleOrder();
    $proof = lifecycleUpload($order);
    app(PaymentService::class)->approvePayment($proof->fresh(), $admin);

    Livewire::actingAs($admin)->test(AdminPayments::class)
        ->call('openReview', $proof->id)
        ->set('adminNote', 'tolak belakangan')
        ->call('rejectPayment');

    expect($order->fresh()->status)->toBe('processing')
        ->and($order->fresh()->invoice->status)->toBe('paid');
});

it('FUL-04: transitionStatus(completed) on a TERKENDALA order must be refused', function () {
    $admin = User::factory()->admin()->create();
    $order = lifecycleOrder();
    $order->update(['status' => 'shipped']);
    $order->shipment->update(['issue_status' => Shipment::ISSUE_TERKENDALA, 'shipped_at' => now()]);

    Livewire::actingAs($admin)->test(OrderFulfillmentActions::class, ['order' => $order->fresh()])
        ->call('transitionStatus', 'completed');

    expect($order->fresh()->status)->toBe('shipped');
});

it('RISK: cancelOrder on a stale model restores stock twice (no row lock / re-check)', function () {
    $order = lifecycleOrder();
    $productId = $order->items->first()->product_id;
    $stockBefore = InventorySnapshot::where('product_id', $productId)->value('quantity_available');
    $staleA = Order::find($order->id);
    $staleB = Order::find($order->id);

    app(OrderService::class)->cancelOrder($staleA, 'admin', 'ADMIN');
    try {
        app(OrderService::class)->cancelOrder($staleB, 'auto', 'SYSTEM');
    } catch (InvalidArgumentException) {
    }

    expect(InventorySnapshot::where('product_id', $productId)->value('quantity_available'))
        ->toBe($stockBefore + $order->items->sum('quantity'));
});

it('FUL-01 requires a tracking number for expedition but not for the store courier', function () {
    $admin = User::factory()->admin()->create();
    $order = lifecycleOrder();
    $order->update(['status' => 'packed']);
    $order->shipment->update(['rate_provider' => Shipment::PROVIDER_BITESHIP]);

    Livewire::actingAs($admin)->test(OrderFulfillmentActions::class, ['order' => $order])
        ->set('trackingNumber', '')
        ->call('confirmShip');
    expect($order->fresh()->status)->toBe('packed');

    $order->shipment->update(['rate_provider' => Shipment::PROVIDER_STORE]);
    Livewire::actingAs($admin)->test(OrderFulfillmentActions::class, ['order' => $order->fresh()])
        ->set('trackingNumber', '')
        ->call('confirmShip');
    expect($order->fresh()->status)->toBe('shipped');
});

it('auto-cancels payment_rejected orders after the rejection day, not on the same day', function () {
    $order = lifecycleOrder();
    $order->update(['status' => 'payment_rejected']);

    // Ditolak hari ini: pelanggan masih boleh unggah ulang.
    $this->artisan('orders:auto-cancel-unpaid')->assertSuccessful();
    expect($order->fresh()->status)->toBe('payment_rejected');

    $this->travel(1)->days();
    $this->artisan('orders:auto-cancel-unpaid')->assertSuccessful();

    expect($order->fresh())
        ->status->toBe('cancelled')
        ->cancellation_source->toBe('SYSTEM');
});

it('lets a suspended customer view the order but not upload payment proof', function () {
    $order = lifecycleOrder();
    $order->user->customerProfile->update(['verification_status' => CustomerProfile::SUSPENDED]);

    $this->actingAs($order->user)->get(route('orders.show', $order))->assertOk();
    $this->actingAs($order->user)->get(route('orders.invoice', $order))->assertOk();

    expect(fn () => lifecycleUpload($order->fresh()))
        ->toThrow(ValidationException::class);
    expect($order->fresh()->status)->toBe('unpaid');
});
