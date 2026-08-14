<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Address;
use App\Models\BankAccount;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;

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

function reportFixtureOrder(): Order
{
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

    $order->update(['status' => 'shipped']);

    return $order;
}

it('shows KPIs and transaction table for shipped orders', function () {
    $admin = User::factory()->admin()->create();
    $order = reportFixtureOrder();

    $this->actingAs($admin)
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('Laporan Penjualan')
        ->assertSee('Omzet')
        ->assertSee('PPh 22 Terutang')
        ->assertSee('Jumlah Pesanan')
        ->assertSee($order->order_number);
});

it('filters reports by period and district', function () {
    $admin = User::factory()->admin()->create();
    $order = reportFixtureOrder();

    $this->actingAs($admin)
        ->get(route('admin.reports.index', ['period' => '7d']))
        ->assertOk()
        ->assertSee($order->order_number);

    $this->actingAs($admin)
        ->get(route('admin.reports.index', ['period' => 'month']))
        ->assertOk()
        ->assertSee($order->order_number);

    $this->actingAs($admin)
        ->get(route('admin.reports.index', ['district' => 'Coblong']))
        ->assertOk()
        ->assertSee($order->order_number);

    // Order dengan kecamatan berbeda tidak masuk tabel laporan (nomor order tetap
    // muncul di panel notifikasi bell admin, sehingga validasi memakai isi tabel).
    $this->actingAs($admin)
        ->get(route('admin.reports.index', ['district' => 'Kecamatan Fiktif']))
        ->assertOk()
        ->assertSee('Tidak ada transaksi pada periode ini.')
        ->assertDontSee('Total omzet periode');
});

it('excludes unpaid orders from omzet report', function () {
    $admin = User::factory()->admin()->create();
    $order = reportFixtureOrder();
    $order->update(['status' => 'unpaid']);

    $this->actingAs($admin)
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('Tidak ada transaksi pada periode ini.')
        ->assertDontSee('Total omzet periode');
});

it('blocks non-admins from reports', function () {
    $customer = User::factory()->activeCustomer()->create();

    $this->actingAs($customer)
        ->get(route('admin.reports.index'))
        ->assertForbidden();
});
