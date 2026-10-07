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
        ->assertSee('Yang perlu diketahui sebelum belanja')
        ->assertDontSee('500+')
        ->assertDontSee('200+');
});

// `\$nextTick` tercetak literal membuat x-data navbar gagal di-parse (pencarian mobile & menu kategori mati).
it('renders navbar Alpine magics without escaped dollar signs', function () {
    $this->get(route('home'))->assertOk()->assertDontSee('\\$', false);
});

it('serves the privacy policy and terms pages linked from the footer', function () {
    $this->get(route('home'))
        ->assertSee(route('legal.privacy'), false)
        ->assertSee(route('legal.terms'), false);

    $this->get(route('legal.privacy'))->assertOk()->assertSee('Kebijakan Privasi')->assertSee('UU 11/2008', false);
    $this->get(route('legal.terms'))->assertOk()->assertSee('Syarat dan Ketentuan')->assertSee('Pengadilan Negeri Bandung');
});
