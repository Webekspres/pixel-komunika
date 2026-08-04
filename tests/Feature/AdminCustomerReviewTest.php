<?php

use App\Models\CustomerProfile;
use App\Models\User;

it('allows admins to review and change customer verification status', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    $profile = $customer->customerProfile()->create([
        'business_name' => 'Toko Pending',
        'verification_status' => CustomerProfile::PENDING,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.customers.update', $profile), [
            'action' => 'approve',
        ])
        ->assertRedirect();

    expect($profile->fresh()->verification_status)->toBe(CustomerProfile::ACTIVE)
        ->and($profile->fresh()->reviewed_by)->toBe($admin->id);

    $this->actingAs($admin)
        ->patch(route('admin.customers.update', $profile), [
            'action' => 'suspend',
            'reason' => 'Dokumen perlu diperbarui.',
        ])
        ->assertRedirect();

    expect($profile->fresh()->verification_status)->toBe(CustomerProfile::SUSPENDED)
        ->and($profile->fresh()->rejection_reason)->toBe('Dokumen perlu diperbarui.');

    $this->actingAs($admin)
        ->patch(route('admin.customers.update', $profile), [
            'action' => 'reactivate',
        ])
        ->assertRedirect();

    expect($profile->fresh()->verification_status)->toBe(CustomerProfile::ACTIVE)
        ->and($profile->fresh()->rejection_reason)->toBeNull();
});

it('lets admins reject customers with a reason', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    $profile = $customer->customerProfile()->create([
        'verification_status' => CustomerProfile::PENDING,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.customers.update', $profile), [
            'action' => 'reject',
            'reason' => 'Data usaha belum lengkap.',
        ])
        ->assertRedirect();

    expect($profile->fresh()->verification_status)->toBe(CustomerProfile::REJECTED)
        ->and($profile->fresh()->rejection_reason)->toBe('Data usaha belum lengkap.');
});

it('blocks non-admins from customer management', function () {
    $customer = User::factory()->create();
    $customer->customerProfile()->create([
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $this->actingAs($customer)
        ->get(route('admin.customers.index'))
        ->assertForbidden();
});

it('lets admins search customers and view customer detail', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create([
        'name' => 'Sylvi Toko',
        'email' => 'sylvi@example.com',
        'phone' => '081234567890',
    ]);

    $profile = $customer->customerProfile()->create([
        'business_name' => 'Pixel Partner',
        'verification_status' => CustomerProfile::PENDING,
    ]);

    $customer->addresses()->create([
        'label' => 'Toko Utama',
        'recipient_name' => 'Sylvi Toko',
        'recipient_phone' => '081234567890',
        'address_line' => 'Jalan Pixel 1',
        'province_name' => 'Jawa Barat',
        'city_name' => 'Bandung',
        'district_name' => 'Coblong',
        'postal_code' => '40135',
        'is_default' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.customers.index', ['q' => 'Pixel Partner']))
        ->assertOk()
        ->assertSee('Sylvi Toko')
        ->assertSee('Pixel Partner');

    $this->actingAs($admin)
        ->get(route('admin.customers.show', $profile))
        ->assertOk()
        ->assertSee('sylvi@example.com')
        ->assertSee('Toko Utama');
});
