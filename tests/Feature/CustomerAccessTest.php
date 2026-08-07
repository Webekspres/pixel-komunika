<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Address;
use App\Models\CustomerProfile;
use App\Models\User;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('hides prices from guests and pending customers', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Verifikasi Akun');

    $pending = User::factory()->create();
    $pending->customerProfile()->create([
        'verification_status' => CustomerProfile::PENDING,
    ]);

    $this->actingAs($pending)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Verifikasi Akun');
});

it('shows prices to active customers', function () {
    $customer = User::factory()->create();
    $customer->customerProfile()->create([
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $this->actingAs($customer)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Rp');
});

it('blocks pending customers from checkout and order history', function () {
    $pending = User::factory()->create();
    $pending->customerProfile()->create([
        'verification_status' => CustomerProfile::PENDING,
    ]);

    $this->actingAs($pending)
        ->get(route('checkout.index'))
        ->assertForbidden();

    $this->actingAs($pending)
        ->get(route('orders.index'))
        ->assertForbidden();
});

it('allows active customers to access checkout and order history', function () {
    $customer = User::factory()->create();
    $customer->customerProfile()->create([
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $this->actingAs($customer)
        ->get(route('checkout.index'))
        ->assertOk()
        ->assertSee('Checkout');

    $this->actingAs($customer)
        ->get(route('orders.index'))
        ->assertOk()
        ->assertSee('Riwayat Pesanan');
});

it('lets a customer update profile and manage default addresses', function () {
    $customer = User::factory()->create([
        'phone' => '081111111111',
    ]);
    $customer->customerProfile()->create([
        'business_name' => 'Toko Lama',
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $this->actingAs($customer)
        ->patch(route('account.update'), [
            'name' => 'Nama Baru',
            'phone' => '082222222222',
            'business_name' => 'Toko Baru',
        ])
        ->assertRedirect();

    expect($customer->fresh()->name)->toBe('Nama Baru')
        ->and($customer->fresh()->phone)->toBe('082222222222')
        ->and($customer->fresh()->customerProfile->business_name)->toBe('Toko Baru');

    $this->actingAs($customer)
        ->post(route('account.addresses.store'), [
            'label' => 'Toko Utama',
            'recipient_name' => 'Nama Baru',
            'recipient_phone' => '082222222222',
            'address_line' => 'Jalan Mawar No. 1',
            'province_name' => 'Jawa Barat',
            'city_name' => 'Bandung',
            'district_name' => 'Coblong',
            'postal_code' => '40135',
        ])
        ->assertRedirect();

    $firstAddress = Address::query()->where('user_id', $customer->id)->firstOrFail();
    expect($firstAddress->is_default)->toBeTrue();

    $this->actingAs($customer)
        ->post(route('account.addresses.store'), [
            'label' => 'Gudang',
            'recipient_name' => 'Nama Baru',
            'recipient_phone' => '082222222222',
            'address_line' => 'Jalan Melati No. 2',
            'province_name' => 'Jawa Barat',
            'city_name' => 'Bandung',
            'district_name' => 'Sukajadi',
            'postal_code' => '40162',
            'is_default' => '1',
        ])
        ->assertRedirect();

    $firstAddress->refresh();
    $secondAddress = Address::query()->where('label', 'Gudang')->firstOrFail();

    expect($firstAddress->is_default)->toBeFalse()
        ->and($secondAddress->is_default)->toBeTrue();
});

it('blocks a customer from editing another customers address', function () {
    $customer = User::factory()->activeCustomer()->create();
    $other = User::factory()->activeCustomer()->create();

    $address = $other->addresses()->create([
        'label' => 'Alamat Lain',
        'recipient_name' => 'Other User',
        'recipient_phone' => '081234567890',
        'address_line' => 'Jalan Lain',
        'province_name' => 'Jawa Tengah',
        'city_name' => 'Semarang',
        'district_name' => 'Tembalang',
        'postal_code' => '50275',
        'is_default' => true,
    ]);

    $this->actingAs($customer)
        ->patch(route('account.addresses.update', $address), [
            'label' => 'Hacked',
            'recipient_name' => 'Bad Actor',
            'recipient_phone' => '080000000000',
            'address_line' => 'Jalan Baru',
            'province_name' => 'DKI Jakarta',
            'city_name' => 'Jakarta',
            'district_name' => 'Setiabudi',
        ])
        ->assertForbidden();
});
