<?php

use App\Domains\Reporting\ReportingService;
use App\Http\Middleware\EnsureActiveCustomer;
use App\Http\Middleware\EnsureAdmin;
use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Order;
use App\Models\StoreCourierRate;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\Admin\AdminDashboardService;
use Carbon\Carbon;
use Livewire\Livewire;

beforeEach(function () {
    StoreProfile::query()->updateOrCreate(
        ['store_name' => 'Pixel Komunika'],
        [
            'address' => 'Jl. Test',
            'contact_number' => '081546407702',
            'company_npwp' => '0821.4146.0442.4000',
            'partai_minimum_quantity' => 5,
            'is_active' => true,
        ],
    );
});

afterEach(fn () => Carbon::setTestNow());

// ADM-03 / FR-PRC-003: tax rule changes must be audited.
it('probe: tax rule change is written to audit log', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::query()->create(['pos_category_id' => 'PROBE-CAT', 'name' => 'Probe Kategori', 'is_active' => true]);

    $this->actingAs($admin)
        ->patch(route('admin.tax-rules.update', $category), [
            'threshold_amount' => 1000000,
            'rate_percent' => 1.5,
            'is_active' => 1,
        ])
        ->assertRedirect();

    expect(AuditLog::query()->count())->toBeGreaterThan(0);
});

// ADM-04 / SRS 15: courier configuration changes must be audited.
it('probe: store courier rate create is written to audit log', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.settings.courier-rates.store'), [
            'area_name' => 'Coblong',
            'rate_amount' => 10000,
            'is_active' => 1,
        ])
        ->assertRedirect();

    expect(AuditLog::query()->count())->toBeGreaterThan(0);
});

// ADM-04: NPWP/identity change (identitas tab has no partai field) leaves no audit trail.
it('probe: NPWP change from identitas tab is audited', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patch(route('admin.settings.store-profile.update'), [
            'store_name' => 'Pixel Komunika',
            'address' => 'Jl. Test',
            'contact_number' => '081546407702',
            'company_npwp' => '99.999.999.9-999.999',
            'tab' => 'identitas',
        ])
        ->assertRedirect();

    expect(StoreProfile::active()->company_npwp)->toBe('99.999.999.9-999.999')
        ->and(AuditLog::query()->count())->toBeGreaterThan(0);
});

// ADM-04: bank account (payment destination) change is not audited.
it('probe: bank account number change is audited', function () {
    $admin = User::factory()->admin()->create();
    $bank = BankAccount::query()->create(['bank_name' => 'BCA', 'account_number' => '111', 'account_holder' => 'PK', 'is_active' => true]);

    $this->actingAs($admin)
        ->patch(route('admin.settings.bank-accounts.update', $bank), [
            'bank_name' => 'BCA',
            'account_number' => '222',
            'account_holder' => 'PK',
            'is_active' => 1,
        ])
        ->assertRedirect();

    expect(AuditLog::query()->count())->toBeGreaterThan(0);
});

// AGENTS.md destructive-action rule: courier rate delete must be confirmed.
it('probe: courier rate delete button is behind a confirmation', function () {
    $admin = User::factory()->admin()->create();
    $rate = StoreCourierRate::query()->create(['area_name' => 'Coblong', 'rate_amount' => 10000, 'is_active' => true]);

    $html = $this->actingAs($admin)->get(route('admin.settings.index'))->assertOk()->getContent();
    $url = e(route('admin.settings.courier-rates.destroy', $rate));
    $pos = strpos($html, 'action="'.$url.'"');

    expect($pos)->not->toBeFalse();
    $around = substr($html, max(0, $pos - 1500), 2000);
    expect(preg_match('/confirm|modal/i', $around))->toBe(1);
});

// ADM-05: dashboard "Total Omzet" must match report omzet (FR-RPT-001, PPh22 separate).
it('probe: dashboard total omzet equals report omzet', function () {
    Order::factory()->create(['status' => 'shipped', 'subtotal' => 100000, 'shipping_cost' => 15000, 'tax_pph22' => 1000, 'grand_total' => 116000]);

    $dashboard = app(AdminDashboardService::class)->build();
    $report = app(ReportingService::class)->salesSummary();

    expect($dashboard['totalRevenue'])->toBe($report['omzet']);
});

// ADM-05: orders on the last day of last month are dropped from "vs bulan lalu".
it('probe: dashboard last-month revenue includes orders on the last day of the month', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-05 12:00:00', 'Asia/Jakarta'));
    $order = Order::factory()->create(['status' => 'shipped', 'grand_total' => 116000]);
    $order->forceFill(['created_at' => Carbon::parse('2026-09-30 10:00:00', 'Asia/Jakarta')])->saveQuietly();

    // Nothing this month, 116000 last month => -100%.
    expect(app(AdminDashboardService::class)->build()['revenueGrowthPercent'])->toBe(-100.0);
});

// ADM-05: dashboard must show real data, not placeholders, on an empty store.
it('probe: dashboard shows no hard-coded placeholder figure on empty data', function (string $placeholder) {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertDontSee($placeholder);
})->with(['+12.4%', '+5 registrasi baru minggu ini', '10 menit yang lalu', '3.550.000', '7.250.000']);

// CHK-08 / FR-NTF-002: 50 permanently failing WA rows starve every newer notification.
// Starvation WhatsApp diuji di AdminNotificationTest (butuh time travel per putaran scheduler).

// Livewire::test tidak menjalankan middleware; yang dijaga adalah registrasi persistent
// middleware sehingga /livewire/update mengulang EnsureAdmin/EnsureActiveCustomer.
it('re-runs admin and active-customer middleware on Livewire updates', function () {
    expect(app('livewire')->getPersistentMiddleware())
        ->toContain(EnsureAdmin::class)
        ->toContain(EnsureActiveCustomer::class);
});
