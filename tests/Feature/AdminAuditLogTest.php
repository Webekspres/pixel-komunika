<?php

use App\Domains\Audit\AuditLogger;
use App\Models\AuditLog;
use App\Models\StoreProfile;
use App\Models\User;

it('lets admin browse and filter audit logs', function () {
    $admin = User::factory()->admin()->create();
    $other = User::factory()->admin()->create();

    $store = StoreProfile::query()->updateOrCreate(
        ['store_name' => 'Pixel Komunika'],
        [
            'address' => 'Jl. Test',
            'contact_number' => '081546407702',
            'company_npwp' => '00.000.000.0-000.000',
            'partai_minimum_quantity' => 5,
            'is_active' => true,
        ],
    );

    app(AuditLogger::class)->log('STORE_PROFILE_UPDATED', $store, $admin, null, ['partai_minimum_quantity' => 5]);
    app(AuditLogger::class)->log('PAYMENT_VERIFIED', $store, $other);

    $this->actingAs($admin)
        ->get(route('admin.audit-logs.index', ['action' => 'STORE_PROFILE_UPDATED']))
        ->assertOk()
        ->assertSee('Audit Log')
        ->assertSee('STORE_PROFILE_UPDATED')
        ->assertSee($admin->name);
});

it('forbids non-admin from audit logs', function () {
    $customer = User::factory()->activeCustomer()->create();

    $this->actingAs($customer)
        ->get(route('admin.audit-logs.index'))
        ->assertForbidden();
});
