<?php

use App\Livewire\Customer\OrderDetail;
use App\Livewire\Storefront\Checkout;
use App\Models\Address;
use App\Models\BankAccount;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\Role;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
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

it('rate limits repeated checkout placeOrder attempts', function () {
    $role = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $user = User::factory()->create(['role_id' => $role->id]);
    CustomerProfile::create([
        'user_id' => $user->id,
        'verification_status' => CustomerProfile::ACTIVE,
    ]);
    Address::create([
        'user_id' => $user->id,
        'recipient_name' => 'Toko',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Merdeka 1',
        'province_name' => 'Jawa Barat',
        'city_name' => 'Bandung',
        'district_name' => 'Coblong',
        'postal_code' => '40135',
        'is_default' => true,
    ]);

    RateLimiter::clear('checkout-order:'.$user->id);

    $this->actingAs($user);

    foreach (range(1, 10) as $i) {
        Livewire::test(Checkout::class)->call('placeOrder');
    }

    Livewire::test(Checkout::class)
        ->call('placeOrder')
        ->assertHasErrors(['selectedCourierKey']);
});

it('rate limits repeated payment proof uploads', function () {
    $role = Role::firstOrCreate(['code' => Role::CUSTOMER], ['name' => Role::CUSTOMER]);
    $user = User::factory()->create(['role_id' => $role->id]);
    CustomerProfile::create([
        'user_id' => $user->id,
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $order = Order::query()->create([
        'order_number' => 'PK-RATE-001',
        'idempotency_key' => (string) str()->uuid(),
        'user_id' => $user->id,
        'status' => 'unpaid',
        'order_date_local' => now('Asia/Jakarta')->toDateString(),
        'recipient_name' => 'Toko',
        'recipient_phone' => '08123456789',
        'shipping_address_line' => 'Jl. Merdeka 1',
        'shipping_province' => 'Jawa Barat',
        'shipping_city' => 'Bandung',
        'shipping_district' => 'Coblong',
        'shipping_postal_code' => '40135',
        'courier_code' => 'store',
        'courier_service' => 'Kurir Toko',
        'shipping_cost' => 10000,
        'subtotal' => 100000,
        'tax_pph22' => 0,
        'grand_total' => 110000,
        'expires_at' => now()->addDay(),
    ]);

    RateLimiter::clear('payment-proof-upload:'.$user->id);

    $this->actingAs($user);

    foreach (range(1, 5) as $i) {
        Livewire::test(OrderDetail::class, ['order' => $order])->call('uploadPaymentProof');
    }

    Livewire::test(OrderDetail::class, ['order' => $order])
        ->call('uploadPaymentProof')
        ->assertHasErrors(['proof_file']);
});
