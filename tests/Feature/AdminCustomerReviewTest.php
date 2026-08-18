<?php

use App\Livewire\Admin\CustomerReviewDetail;
use App\Models\AuditLog;
use App\Models\CustomerProfile;
use App\Models\User;
use Livewire\Livewire;

it('allows admins to review and change customer verification status', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    $profile = $customer->customerProfile()->create([
        'business_name' => 'Toko Pending',
        'verification_status' => CustomerProfile::PENDING,
    ]);

    Livewire::actingAs($admin)
        ->test(CustomerReviewDetail::class, ['customerProfile' => $profile])
        ->call('approve')
        ->assertHasNoErrors();

    expect($profile->fresh()->verification_status)->toBe(CustomerProfile::ACTIVE)
        ->and($profile->fresh()->reviewed_by)->toBe($admin->id)
        ->and($profile->fresh()->rejection_reason)->toBeNull();

    Livewire::actingAs($admin)
        ->test(CustomerReviewDetail::class, ['customerProfile' => $profile])
        ->set('rejectionReason', 'Dokumen perlu diperbarui.')
        ->call('suspend')
        ->assertHasNoErrors();

    expect($profile->fresh()->verification_status)->toBe(CustomerProfile::SUSPENDED)
        ->and($profile->fresh()->rejection_reason)->toBe('Dokumen perlu diperbarui.');

    Livewire::actingAs($admin)
        ->test(CustomerReviewDetail::class, ['customerProfile' => $profile])
        ->call('reactivate')
        ->assertHasNoErrors();

    expect($profile->fresh()->verification_status)->toBe(CustomerProfile::ACTIVE)
        ->and($profile->fresh()->rejection_reason)->toBeNull();
});

it('records verification changes to audit logs', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    $profile = $customer->customerProfile()->create([
        'verification_status' => CustomerProfile::PENDING,
    ]);

    Livewire::actingAs($admin)
        ->test(CustomerReviewDetail::class, ['customerProfile' => $profile])
        ->call('approve');

    expect(AuditLog::query()->where('auditable_type', CustomerProfile::class)
        ->where('auditable_id', $profile->id)
        ->where('action', 'CUSTOMER_APPROVE')
        ->exists())->toBeTrue();
});

it('rejects customers only with a valid reason', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    $profile = $customer->customerProfile()->create([
        'verification_status' => CustomerProfile::PENDING,
    ]);

    // Alasan terlalu pendek → validasi gagal, status tidak berubah.
    Livewire::actingAs($admin)
        ->test(CustomerReviewDetail::class, ['customerProfile' => $profile])
        ->set('rejectionReason', 'abc')
        ->call('reject')
        ->assertHasErrors('rejectionReason');

    expect($profile->fresh()->verification_status)->toBe(CustomerProfile::PENDING);

    // Alasan valid → ditolak.
    Livewire::actingAs($admin)
        ->test(CustomerReviewDetail::class, ['customerProfile' => $profile])
        ->set('rejectionReason', 'Data usaha belum lengkap.')
        ->call('reject')
        ->assertHasNoErrors();

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
        ->assertSee('Toko Utama')
        ->assertSee('Setujui Pendaftaran')
        ->assertSee('Laporan') // shell admin (sidebar) tetap tampil
        ->assertDontSee('Cari pelanggan, produk, pesanan'); // search global navbar dihapus
});

it('shows state-driven actions on customer detail page', function () {
    $admin = User::factory()->admin()->create();

    $makeProfile = function (string $status) use ($admin): CustomerProfile {
        $customer = User::factory()->create();
        $profile = $customer->customerProfile()->create([
            'verification_status' => $status,
            'rejection_reason' => $status === CustomerProfile::PENDING ? null : 'Alasan contoh yang valid.',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        return $profile;
    };

    $pending = $makeProfile(CustomerProfile::PENDING);
    $this->actingAs($admin)->get(route('admin.customers.show', $pending))
        ->assertOk()
        ->assertSee('Setujui Pendaftaran')
        ->assertSee('Tolak Pendaftaran');

    $active = $makeProfile(CustomerProfile::ACTIVE);
    $this->actingAs($admin)->get(route('admin.customers.show', $active))
        ->assertOk()
        ->assertSee('Tangguhkan Akun')
        ->assertDontSee('Setujui Pendaftaran');

    $suspended = $makeProfile(CustomerProfile::SUSPENDED);
    $this->actingAs($admin)->get(route('admin.customers.show', $suspended))
        ->assertOk()
        ->assertSee('Aktifkan Kembali')
        ->assertSee('Akun ditangguhkan');

    $rejected = $makeProfile(CustomerProfile::REJECTED);
    $this->actingAs($admin)->get(route('admin.customers.show', $rejected))
        ->assertOk()
        ->assertSee('Tinjau & Setujui', false)
        ->assertSee('Pendaftaran ditolak');
});
