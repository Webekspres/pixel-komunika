<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('shows the branded home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Pixel Komunika')
        ->assertSee('Aksesoris')
        ->assertSee('konektivitas')
        ->assertSee('Katalog Produk')
        ->assertSee('Dirancang untuk reseller')
        ->assertSee('Akun terverifikasi');
});
