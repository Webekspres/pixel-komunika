<?php

it('shows the branded home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Pixel Komunika')
        ->assertSee('Aksesoris')
        ->assertSee('konektivitas')
        ->assertSee('Tiga jalur utama')
        ->assertSee('Aksesoris elektronik')
        ->assertSee('Kartu')
        ->assertSee('voucher')
        ->assertSee('Pulsa')
        ->assertSee('Permintaan tinggi')
        ->assertSee('Dirancang untuk reseller')
        ->assertSee('Akun terverifikasi');
});
