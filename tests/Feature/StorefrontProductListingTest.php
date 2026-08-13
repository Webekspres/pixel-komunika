<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
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

it('filters products by search keyword', function () {
    $product = Product::first();

    $this->get(route('products.index', ['cari' => $product->name]))
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
