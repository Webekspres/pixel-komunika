<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\BankAccount;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;
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

function paymentFixtureOrder(): array
{
    $customer = User::factory()->activeCustomer()->create();
    $admin = User::factory()->admin()->create();

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

    $order = app(OrderService::class)->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne',
        'service' => 'REG',
        'cost' => 15000,
    ]);

    $proof = app(PaymentService::class)->uploadPaymentProof($order, $customer, [
        'bank_name' => 'BCA',
        'account_name' => $customer->name,
        'amount' => $order->grand_total,
    ], UploadedFile::fake()->image('proof.jpg'));

    return compact('customer', 'admin', 'order', 'proof');
}

it('shows pending queue and streams proof preview for admins', function () {
    Storage::fake('local');
    ['admin' => $admin, 'proof' => $proof] = paymentFixtureOrder();

    $this->actingAs($admin)
        ->get(route('admin.payments.index'))
        ->assertOk()
        ->assertSee('Antrian Verifikasi')
        ->assertSee($proof->order->order_number)
        ->assertSee('1 menunggu verifikasi');

    $this->actingAs($admin)
        ->get(route('admin.payments.show', $proof))
        ->assertOk();
});

it('lets admins approve a pending payment proof', function () {
    Storage::fake('local');
    ['admin' => $admin, 'order' => $order, 'proof' => $proof] = paymentFixtureOrder();

    $this->actingAs($admin)
        ->patch(route('admin.payments.update', $proof), ['action' => 'approve'])
        ->assertRedirect();

    expect($proof->fresh()->status)->toBe('approved')
        ->and($proof->fresh()->reviewed_by)->toBe($admin->id)
        ->and($order->fresh()->status)->toBe('paid');
});

it('lets admins reject a pending payment proof with reason', function () {
    Storage::fake('local');
    ['admin' => $admin, 'order' => $order, 'proof' => $proof] = paymentFixtureOrder();

    $this->actingAs($admin)
        ->patch(route('admin.payments.update', $proof), [
            'action' => 'reject',
            'reason' => 'Nominal tidak sesuai tagihan.',
        ])
        ->assertRedirect();

    expect($proof->fresh()->status)->toBe('rejected')
        ->and($proof->fresh()->is_active)->toBeFalse()
        ->and($order->fresh()->status)->toBe('unpaid');
});

it('requires a reason when rejecting', function () {
    Storage::fake('local');
    ['admin' => $admin, 'proof' => $proof] = paymentFixtureOrder();

    $this->actingAs($admin)
        ->patch(route('admin.payments.update', $proof), ['action' => 'reject'])
        ->assertSessionHasErrors('reason');

    expect($proof->fresh()->status)->toBe('pending');
});

it('blocks non-admins from payment review', function () {
    $customer = User::factory()->activeCustomer()->create();

    $this->actingAs($customer)
        ->get(route('admin.payments.index'))
        ->assertForbidden();
});
