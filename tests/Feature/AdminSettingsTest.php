<?php

use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\StoreCourierRate;
use App\Models\StoreProfile;
use App\Models\User;

beforeEach(function () {
    StoreProfile::query()->updateOrCreate(
        ['store_name' => 'Pixel Komunika'],
        [
            'address' => 'Jl. Sawahkurung IV No. 18B',
            'contact_number' => '081546407702',
            'company_name' => 'Pixel Komunika',
            'company_npwp' => '0821.4146.0442.4000',
            'partai_minimum_quantity' => 5,
            'is_active' => true,
        ],
    );
});

it('shows settings tabs and updates store profile with audit trail', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.settings.index'))
        ->assertOk()
        ->assertSee('Identitas Toko')
        ->assertSee('Rekening Bank')
        ->assertSee('Minimum Partai')
        ->assertSee('Kurir Toko');

    $this->actingAs($admin)
        ->patch(route('admin.settings.store-profile.update'), [
            'store_name' => 'Pixel Komunika Baru',
            'address' => 'Jl. Baru No 1',
            'contact_number' => '081111111111',
            'company_name' => 'PT Pixel Komunika',
            'company_npwp' => '00.000.000.0-000.000',
            'partai_minimum_quantity' => 10,
            'origin_postal_code' => '40111',
            'tab' => 'partai',
        ])
        ->assertRedirect();

    $store = StoreProfile::active();

    expect($store->store_name)->toBe('Pixel Komunika Baru')
        ->and($store->partai_minimum_quantity)->toBe(10)
        ->and(AuditLog::where('action', 'STORE_PROFILE_UPDATED')->where('auditable_type', StoreProfile::class)->count())->toBe(1);
});

it('saves identitas and partai tabs independently', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patch(route('admin.settings.store-profile.update'), [
            'store_name' => 'Toko Baru',
            'address' => 'Jl. Contoh No. 7',
            'contact_number' => '082122223333',
            'company_npwp' => '12.345.678.9-012.345',
            'tab' => 'identitas',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect(StoreProfile::active()->store_name)->toBe('Toko Baru')
        ->and(StoreProfile::active()->partai_minimum_quantity)->toBe(5);

    $this->actingAs($admin)
        ->patch(route('admin.settings.store-profile.update'), [
            'partai_minimum_quantity' => 25,
            'tab' => 'partai',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect(StoreProfile::active()->partai_minimum_quantity)->toBe(25)
        ->and(StoreProfile::active()->store_name)->toBe('Toko Baru')
        ->and(AuditLog::where('action', 'STORE_PROFILE_UPDATED')->where('auditable_type', StoreProfile::class)->count())->toBe(1);
});

it('manages bank account CRUD', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.settings.bank-accounts.store'), [
            'bank_name' => 'Mandiri',
            'account_number' => '1234500001',
            'account_holder' => 'Pixel Komunika',
            'is_active' => '1',
            'tab' => 'rekening',
        ])
        ->assertRedirect();

    $account = BankAccount::where('account_number', '1234500001')->first();

    expect($account)->not->toBeNull()
        ->and($account->is_active)->toBeTrue();

    $this->actingAs($admin)
        ->patch(route('admin.settings.bank-accounts.update', $account), [
            'bank_name' => 'Mandiri Updated',
            'account_number' => '1234500001',
            'account_holder' => 'Pixel Komunika',
            'tab' => 'rekening',
        ])
        ->assertRedirect();

    expect($account->fresh()->bank_name)->toBe('Mandiri Updated');

    $this->actingAs($admin)
        ->delete(route('admin.settings.bank-accounts.destroy', $account), ['tab' => 'rekening'])
        ->assertRedirect();

    expect(BankAccount::find($account->id))->toBeNull();
});

it('manages courier rate CRUD', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.settings.courier-rates.store'), [
            'area_name' => 'Buah Batu',
            'area_code' => 'BDG-BUAHBATU',
            'rate_amount' => 13000,
            'eta_text' => 'H+1 hari kerja',
            'is_active' => '1',
            'tab' => 'kurir',
        ])
        ->assertRedirect();

    $rate = StoreCourierRate::where('area_name', 'Buah Batu')->first();

    expect($rate)->not->toBeNull()
        ->and((float) $rate->rate_amount)->toBe(13000.0);

    $this->actingAs($admin)
        ->patch(route('admin.settings.courier-rates.update', $rate), [
            'area_name' => 'Buah Batu',
            'rate_amount' => 14000,
            'eta_text' => 'H+1/H+2 hari kerja',
            'tab' => 'kurir',
        ])
        ->assertRedirect();

    expect((float) $rate->fresh()->rate_amount)->toBe(14000.0);

    $this->actingAs($admin)
        ->delete(route('admin.settings.courier-rates.destroy', $rate), ['tab' => 'kurir'])
        ->assertRedirect();

    expect(StoreCourierRate::find($rate->id))->toBeNull();
});

it('blocks non-admins from settings', function () {
    $customer = User::factory()->activeCustomer()->create();

    $this->actingAs($customer)
        ->get(route('admin.settings.index'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->patch(route('admin.settings.store-profile.update'), ['store_name' => 'X', 'address' => 'Y', 'contact_number' => 'Z', 'company_npwp' => 'N'])
        ->assertForbidden();
});
