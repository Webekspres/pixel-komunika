<?php

use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\User;
use App\Services\Admin\AdminDashboardService;

it('shows figma-style admin dashboard with live aggregates for admins', function () {
    $admin = User::factory()->admin()->create();

    $active = User::factory()->create();
    $active->customerProfile()->create([
        'business_name' => 'Toko Aktif',
        'verification_status' => CustomerProfile::ACTIVE,
        'reviewed_at' => now(),
    ]);

    $pending = User::factory()->create(['name' => 'Maya Pending']);
    $pending->customerProfile()->create([
        'business_name' => 'Toko Maya',
        'verification_status' => CustomerProfile::PENDING,
    ]);

    $buyer = User::factory()->create(['name' => 'Budi Order']);
    $buyer->customerProfile()->create([
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $order = Order::query()->create([
        'user_id' => $buyer->id,
        'order_number' => 'PK-TEST-DASH-001',
        'status' => 'shipped',
        'recipient_name' => 'Budi Order',
        'recipient_phone' => '081111111111',
        'shipping_address_line' => 'Jl. Test 1',
        'shipping_province' => 'Jawa Barat',
        'shipping_city' => 'Bandung',
        'shipping_district' => 'Coblong',
        'shipping_postal_code' => '40135',
        'courier_code' => 'jne',
        'courier_service' => 'REG',
        'shipping_cost' => 0,
        'subtotal' => 100000,
        'tax_pph22' => 0,
        'grand_total' => 150000,
    ]);

    PaymentProof::query()->create([
        'order_id' => $order->id,
        'user_id' => $buyer->id,
        'bank_name' => 'BCA',
        'account_name' => 'Budi',
        'amount' => 150000,
        'status' => 'pending',
        'proof_path' => 'proofs/test.jpg',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('Pesanan Hari Ini')
        ->assertSee('Pembayaran Pending')
        ->assertSee('Pelanggan Aktif')
        ->assertSee('Total Omzet')
        ->assertSee('Pesanan Terbaru')
        ->assertSee('PK-TEST-DASH-001')
        ->assertSee('Verifikasi Pelanggan')
        ->assertSee('Maya Pending')
        ->assertSee('Status Sinkronisasi POS')
        ->assertSee('Verifikasi Bayar')
        ->assertSee('Laporan');

    $data = app(AdminDashboardService::class)->build();
    expect($data['activeCustomers'])->toBeGreaterThanOrEqual(2)
        ->and($data['pendingPayments'])->toBeGreaterThanOrEqual(1)
        ->and((float) $data['totalRevenue'])->toBeGreaterThanOrEqual(150000)
        ->and($data['pendingVerificationCount'])->toBeGreaterThanOrEqual(1);
});

it('renders customer list tabs without inline approval forms', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create(['name' => 'Rina Tabs']);
    $customer->customerProfile()->create([
        'business_name' => 'Toko Rina',
        'verification_status' => CustomerProfile::PENDING,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertSee('Pelanggan')
        ->assertSee('Menunggu')
        ->assertSee('Rina Tabs')
        ->assertSee('Detail')
        ->assertDontSee('name="action"');
});
