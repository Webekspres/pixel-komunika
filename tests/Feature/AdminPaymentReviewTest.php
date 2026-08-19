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
use Illuminate\Support\Facades\URL;

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
    Storage::fake('local');

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

it('streams proof preview only through a temporary signed URL', function () {
    ['admin' => $admin, 'proof' => $proof] = paymentFixtureOrder();

    $this->actingAs($admin)
        ->get(route('admin.payments.show', $proof))
        ->assertForbidden();

    $signed = URL::temporarySignedRoute(
        'admin.payments.show',
        now()->addMinutes(30),
        ['paymentProof' => $proof->id]
    );

    $this->actingAs($admin)
        ->get($signed)
        ->assertOk();
});

it('blocks non-admins from payment review', function () {
    $customer = User::factory()->activeCustomer()->create();

    $this->actingAs($customer)
        ->get(route('admin.payments.index'))
        ->assertForbidden();
});
