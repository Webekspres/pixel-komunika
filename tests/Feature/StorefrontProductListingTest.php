<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Category;
use App\Models\CustomerProfile;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('renders product listing page with catalog items', function () {
    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Katalog Produk')
        ->assertSee('Filter Produk')
        ->assertSee('Verifikasi');
});

it('keeps Semua Produk visible and hides empty category filter when no categories exist', function () {
    Category::query()->delete();

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Semua Produk')
        ->assertDontSee('Semua Kategori');
});

it('renders enrichment label as badge on storefront', function () {
    $product = Product::with('enrichment')
        ->whereHas('enrichment', fn ($q) => $q->whereNotNull('label'))
        ->firstOrFail();

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee($product->enrichment->label);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee($product->enrichment->label);
});

it('filters products by search keyword', function () {
    $product = Product::first();

    $this->get(route('products.index', ['cari' => $product->name]))
        ->assertOk()
        ->assertSee($product->name);
});

it('uses display name on storefront when set, falls back to pos name', function () {
    $product = Product::with('enrichment')->first();

    $product->enrichment()->update(['display_name' => 'Power Bank Premium Edition']);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Power Bank Premium Edition');

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Power Bank Premium Edition')
        ->assertDontSee($product->name);

    $product->enrichment()->update(['display_name' => null]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee($product->name);
});

it('renders product detail page', function () {
    $product = Product::first();

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee($product->name)
        ->assertSee($product->sku)
        ->assertSee('Harga tersembunyi')
        ->assertSee('Deskripsi')
        ->assertSee('Spesifikasi')
        ->assertSee('Produk Original');
});

it('shows wholesale prices on PDP to active verified customer', function () {
    $customer = User::factory()->create();
    $customer->customerProfile()->create([
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $product = Product::with('prices')->first();

    $this->actingAs($customer)
        ->get(route('products.show', $product))
        ->assertOk()
        ->assertSee($product->name)
        ->assertSee(number_format($product->listPriceAmount() ?? 0, 0, ',', '.'));
});

it('renders clean related products links on PDP without escaped quotes', function () {
    $product = Product::first();
    $related = Product::where('id', '!=', $product->id)->first();

    if ($related) {
        $expectedUrl = route('products.show', $related);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('href="' . $expectedUrl . '"', false)
            ->assertDontSee('href="&quot;' . $expectedUrl, false);
    }
});

