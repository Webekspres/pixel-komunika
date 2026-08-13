<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('shows the branded home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Pixel Komunika')
        ->assertSee('Belanja Elektronik')
        ->assertSee('Harga Grosir')
        ->assertSee('Kategori Produk')
        ->assertSee('Produk Pilihan')
        ->assertSee('Mengapa Pixel Komunika');
});
