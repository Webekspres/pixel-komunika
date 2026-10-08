<?php

use App\Domains\Pricing\PriceCalculator;
use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Livewire\Storefront\ProductIndex;
use App\Livewire\Storefront\ProductShow;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\CategoryTaxRule;
use App\Models\InventorySnapshot;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Shipping\BiteshipShippingService;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

function checkoutCustomer(): User
{
    $user = User::factory()->activeCustomer()->create();
    $user->customerProfile->update(['reseller_account_number' => 'PKR-000777']);

    return $user->fresh();
}

function checkoutAddress(User $user): Address
{
    return Address::create([
        'user_id' => $user->id,
        'label' => 'Toko',
        'recipient_name' => 'Budi',
        'recipient_phone' => '08123456789',
        'address_line' => 'Jl. Merdeka 10',
        'province_name' => 'DKI Jakarta',
        'city_name' => 'Jakarta Selatan',
        'district_name' => 'Kebayoran Baru',
        'postal_code' => '12110',
        'is_default' => true,
    ]);
}

$jne = ['provider' => 'BITESHIP', 'code' => 'jne', 'service' => 'REG', 'cost' => 15000];

it('PROBE-01 rejects checkout when stock dropped below cart qty after add-to-cart (FRD 15: stok berubah saat checkout)', function () use ($jne) {
    $user = checkoutCustomer();
    $cart = app(CartService::class)->getOrCreateCart($user);
    $product = Product::where('sku', 'PB-10000')->firstOrFail();
    app(CartService::class)->addItem($cart, $product->id, 10);

    // POS sync / other customer reduces stock to 2
    InventorySnapshot::where('product_id', $product->id)->update(['quantity_available' => 2]);

    expect(fn () => app(OrderService::class)->createOrderFromCart($user, $cart, checkoutAddress($user), $jne))
        ->toThrow(InvalidArgumentException::class);
});

it('PROBE-02 hidden product (is_visible=false) cannot be added to cart (FR-CAT-006)', function () {
    $user = checkoutCustomer();
    $product = Product::where('sku', 'PB-10000')->firstOrFail();
    $product->enrichment()->update(['is_visible' => false]);
    $cart = app(CartService::class)->getOrCreateCart($user);

    expect(fn () => app(CartService::class)->addItem($cart, $product->id, 1))
        ->toThrow(InvalidArgumentException::class);
});

it('PROBE-03 inactive product (is_active=false) is not listed in catalog (FR-CAT-007, KAT-01)', function () {
    $product = Product::where('sku', 'PB-10000')->firstOrFail();
    $product->update(['is_active' => false]);
    $product->enrichment()->update(['is_visible' => false]);

    Livewire::test(ProductIndex::class)->assertDontSee($product->displayName());
});

it('PROBE-04 product added to cart then hidden by admin cannot be checked out (FR-CART-004)', function () use ($jne) {
    $user = checkoutCustomer();
    $cart = app(CartService::class)->getOrCreateCart($user);
    $product = Product::where('sku', 'PB-10000')->firstOrFail();
    app(CartService::class)->addItem($cart, $product->id, 5);
    $product->update(['is_active' => false]);
    $product->enrichment()->update(['is_visible' => false]);

    expect(fn () => app(OrderService::class)->createOrderFromCart($user, $cart, checkoutAddress($user), $jne))
        ->toThrow(InvalidArgumentException::class);
});

it('PROBE-05 tampered quantity 0 / negative on product page is rejected', function () {
    $user = checkoutCustomer();
    $product = Product::where('sku', 'PB-10000')->firstOrFail();

    Livewire::actingAs($user)->test(ProductShow::class, ['product' => $product])
        ->set('quantity', 0)->call('addToCart');

    expect(CartItem::query()->where('product_id', $product->id)->count())->toBe(0);
});

it('PROBE-06 PPh 22 is rounded to the nearest rupiah (K-3)', function () {
    $rule = CategoryTaxRule::query()->where('is_active', true)->where('rate_percent', '>', 0)->firstOrFail();
    $rule->update(['threshold_amount' => 0, 'rate_percent' => 1.5]);

    $result = app(PriceCalculator::class)->calculatePph22(collect([
        ['category_id' => $rule->category_id, 'line_total' => 1000001.0],
    ]));

    // 1_000_001 / 1.11 * 1.5% = 13513.527...
    expect($result['total'])->toBe(13514.0);
});

it('PROBE-07 invoice HTML and PDF show reseller account number (CHK-09)', function () use ($jne) {
    $user = checkoutCustomer();
    $cart = app(CartService::class)->getOrCreateCart($user);
    app(CartService::class)->addItem($cart, Product::where('sku', 'PB-10000')->value('id'), 5);
    $order = app(OrderService::class)->createOrderFromCart($user, $cart, checkoutAddress($user), $jne);

    expect($order->invoice->reseller_account_number_snapshot)->toBe('PKR-000777');

    $this->actingAs($user)->get(route('orders.invoice', $order))->assertOk()->assertSee('PKR-000777');

    // CHK-09: invoice bisa dibuka dari detail pesanan sejak terbit, sebelum dibayar.
    $this->actingAs($user)->get(route('orders.show', $order))->assertOk()
        ->assertSee(route('orders.invoice', $order))
        ->assertSee(route('orders.invoice.download', $order));
});

it('PROBE-08 invoice keeps store NPWP snapshot after admin edits store profile (FR-CAT-009/FR-PRC-006)', function () use ($jne) {
    StoreProfile::query()->update(['is_active' => false]);
    $store = StoreProfile::create(['store_name' => 'Pixel Komunika', 'address' => 'Bandung', 'contact_number' => '0815', 'company_name' => 'Pixel Komunika', 'company_npwp' => '11.111.111.1-111.000', 'is_active' => true]);
    $user = checkoutCustomer();
    $cart = app(CartService::class)->getOrCreateCart($user);
    app(CartService::class)->addItem($cart, Product::where('sku', 'PB-10000')->value('id'), 5);
    $order = app(OrderService::class)->createOrderFromCart($user, $cart, checkoutAddress($user), $jne);

    $store->update(['company_npwp' => '99.999.999.9-999.000']);

    $this->actingAs($user)->get(route('orders.invoice', $order))->assertOk()
        ->assertSee('11.111.111.1-111.000')->assertDontSee('99.999.999.9-999.000');
});

// BR-006 (keputusan klien 7 Okt): checkout butuh minimal satu SKU mencapai minimum partai.
it('KAT-02/05 blocks checkout until one SKU reaches the partai minimum and never charges ECERAN', function () use ($jne) {
    $user = checkoutCustomer();
    $address = checkoutAddress($user);
    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($user);
    $powerBank = Product::where('sku', 'PB-10000')->value('id');
    $headphone = Product::where('sku', 'HP-WL')->value('id');

    $cartService->addItem($cart, $powerBank, 4);
    $cartService->addItem($cart, $headphone, 2);

    expect($cartService->getCartSummary($cart)['items']->pluck('price_type')->unique()->all())->toBe([ProductPrice::BULK])
        ->and(fn () => app(OrderService::class)->createOrderFromCart($user, $cart, $address, $jne))
        ->toThrow(InvalidArgumentException::class, 'Target pembelian minimal 5 unit di salah satu produk belum terpenuhi');

    $this->actingAs($user)->get(route('cart.index'))->assertSee('Target pembelian minimal 5 unit di salah satu produk belum terpenuhi')->assertDontSee('Lanjut Checkout');
    $this->actingAs($user)->get(route('checkout.index'))->assertRedirect(route('cart.index'));

    $cartService->addItem($cart, $powerBank, 1);
    $order = app(OrderService::class)->createOrderFromCart($user, $cart, $address, $jne);

    expect($order->items()->pluck('quantity')->sort()->values()->all())->toBe([2, 5])
        ->and($order->items()->where('price_type', '!=', ProductPrice::BULK)->exists())->toBeFalse();
});

it('PROBE-10 Biteship rates are parsed from the real /v1/rates/couriers schema (CHK-10)', function () {
    config(['biteship.api_key' => 'test-key', 'biteship.origin_area_id' => 'ORIGIN']);
    Http::fake([
        'api.biteship.com/v1/maps/areas*' => Http::response(['success' => true, 'areas' => [[
            'id' => 'IDNP6IDNC148', 'name' => 'Kebayoran Baru, Jakarta Selatan, DKI Jakarta. 12110',
            'administrative_division_level_1_name' => 'DKI Jakarta',
            'administrative_division_level_2_name' => 'Jakarta Selatan',
            'administrative_division_level_3_name' => 'Kebayoran Baru', 'postal_code' => 12110,
        ]]]),
        'api.biteship.com/v1/rates/couriers*' => Http::response(['success' => true, 'pricing' => [
            ['courier_name' => 'JNE', 'courier_code' => 'jne', 'courier_service_name' => 'Reguler', 'courier_service_code' => 'reg', 'duration' => '2 - 3 days', 'price' => 15000],
            ['courier_name' => 'SiCepat', 'courier_code' => 'sicepat', 'courier_service_name' => 'Reguler', 'courier_service_code' => 'reg', 'duration' => '2 - 3 days', 'price' => 12000],
        ]]),
    ]);

    $rates = collect((new BiteshipShippingService)->calculateRates('Jakarta Selatan', 1000, 'Kebayoran Baru'))
        ->where('provider', 'BITESHIP');

    expect($rates)->toHaveCount(2)
        ->and($rates->map(fn ($r) => $r['code'].':'.$r['service'])->unique())->toHaveCount(2);
});

it('PROBE-11 guest cannot infer prices through catalog price filter (FR-CAT-008)', function () {
    $product = Product::where('sku', 'PB-10000')->firstOrFail();

    Livewire::withQueryParams(['max_harga' => '1'])->test(ProductIndex::class)
        ->assertSee($product->displayName());
});

it('PROBE-12 catalog search finds product by website display name (KAT-01, FR-CAT-004)', function () {
    $product = Product::where('sku', 'PB-10000')->firstOrFail();
    $product->enrichment()->update(['display_name' => 'Zentrix Powerbank Ultra']);

    Livewire::test(ProductIndex::class)->set('search', 'Zentrix')
        ->assertSee('Zentrix Powerbank Ultra');
});

it('PROBE-13 product page shows admin-configured partai minimum (KAT-07)', function () {
    StoreProfile::query()->update(['is_active' => false]);
    StoreProfile::create(['store_name' => 'Pixel Komunika', 'address' => 'Bandung', 'contact_number' => '0815', 'company_npwp' => '1', 'partai_minimum_quantity' => 6, 'is_active' => true]);
    $user = checkoutCustomer();
    $product = Product::where('sku', 'PB-10000')->firstOrFail();

    $this->actingAs($user)->get(route('products.show', $product))->assertOk()
        ->assertSee('min. 6 unit')->assertDontSee('min. 5 unit');
});
