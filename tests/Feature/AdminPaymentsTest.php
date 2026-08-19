<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Livewire\Admin\AdminPayments;
use App\Models\BankAccount;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

function paymentsFixtureProof(): PaymentProof
{
    Storage::fake('local');

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

    $order = app(OrderService::class)->createOrderFromCart($customer, $cart, $address, [
        'code' => 'jne',
        'service' => 'REG',
        'cost' => 15000,
    ]);

    $file = UploadedFile::fake()->create('proof.jpg', 500, 'image/jpeg');

    return app(PaymentService::class)->uploadPaymentProof($order, $customer, [
        'bank_name' => 'BCA',
        'account_name' => $customer->name,
        'amount' => $order->grand_total,
    ], $file);
}

it('shows a unified table with status tabs and pending counter', function () {
    $admin = User::factory()->admin()->create();
    $proof = paymentsFixtureProof();

    Livewire::actingAs($admin)
        ->test(AdminPayments::class)
        ->assertSee('Semua')
        ->assertSee('Menunggu Verifikasi')
        ->assertSee('Disetujui')
        ->assertSee('Ditolak')
        ->assertSee($proof->order->order_number)
        ->assertSee('Periksa & Verifikasi');
});

it('filters the proof list by status tab', function () {
    $admin = User::factory()->admin()->create();
    $pendingProof = paymentsFixtureProof();

    $approvedProof = paymentsFixtureProof();
    app(PaymentService::class)->approvePayment($approvedProof, $admin);

    Livewire::actingAs($admin)
        ->test(AdminPayments::class)
        ->set('statusFilter', 'approved')
        ->assertSee($approvedProof->order->order_number)
        ->assertDontSee($pendingProof->order->order_number)
        ->assertDontSee('Periksa & Verifikasi')
        ->assertSee('Detail');
});

it('searches by order number and by bank name', function () {
    $admin = User::factory()->admin()->create();
    $first = paymentsFixtureProof();
    $second = paymentsFixtureProof();

    Livewire::actingAs($admin)
        ->test(AdminPayments::class)
        ->set('search', $first->order->order_number)
        ->assertSee($first->order->order_number)
        ->assertDontSee($second->order->order_number);

    Livewire::actingAs($admin)
        ->test(AdminPayments::class)
        ->set('search', 'BCA')
        ->assertSee($first->order->order_number);
});

it('approves a pending payment and moves the order to processing', function () {
    $admin = User::factory()->admin()->create();
    $proof = paymentsFixtureProof();

    Livewire::actingAs($admin)
        ->test(AdminPayments::class)
        ->call('openReview', $proof->id)
        ->set('adminNote', 'Sudah dicek, lanjut proses.')
        ->call('approvePayment')
        ->assertHasNoErrors();

    $proof->refresh();
    expect($proof->status)->toBe('approved')
        ->and($proof->reviewed_by)->toBe($admin->id)
        ->and($proof->payment->fresh()->status)->toBe(Payment::VERIFIED)
        ->and($proof->payment->fresh()->verified_by)->toBe($admin->id)
        ->and($proof->payment->fresh()->admin_note)->toBe('Sudah dicek, lanjut proses.')
        ->and($proof->order->fresh()->status)->toBe('processing');
});

it('rejects a payment with a reason and moves the order to payment_rejected', function () {
    $admin = User::factory()->admin()->create();
    $proof = paymentsFixtureProof();

    Livewire::actingAs($admin)
        ->test(AdminPayments::class)
        ->call('openReview', $proof->id)
        ->set('adminNote', 'abc')
        ->call('rejectPayment')
        ->assertHasErrors(['adminNote']);

    expect($proof->fresh()->status)->toBe('pending');

    Livewire::actingAs($admin)
        ->test(AdminPayments::class)
        ->call('openReview', $proof->id)
        ->set('adminNote', 'Nominal transfer tidak sesuai tagihan')
        ->call('rejectPayment')
        ->assertHasNoErrors();

    $proof->refresh();
    expect($proof->status)->toBe('rejected')
        ->and($proof->rejection_reason)->toBe('Nominal transfer tidak sesuai tagihan')
        ->and($proof->reviewed_by)->toBe($admin->id)
        ->and($proof->payment->fresh()->status)->toBe(Payment::REJECTED)
        ->and($proof->order->fresh()->status)->toBe('payment_rejected');
});

it('hides approve/reject actions for already reviewed proofs', function () {
    $admin = User::factory()->admin()->create();
    $proof = paymentsFixtureProof();

    Livewire::actingAs($admin)
        ->test(AdminPayments::class)
        ->call('openReview', $proof->id)
        ->assertSee('Terima Pembayaran')
        ->assertSee('Tolak Pembayaran');

    app(PaymentService::class)->approvePayment($proof, $admin);

    Livewire::actingAs($admin)
        ->test(AdminPayments::class)
        ->set('statusFilter', 'approved')
        ->call('openReview', $proof->id)
        ->assertDontSee('Terima Pembayaran')
        ->assertDontSee('Tolak Pembayaran')
        ->assertSee('Review Sebelumnya');
});
