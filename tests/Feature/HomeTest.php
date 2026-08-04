<?php

it('shows the branded home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Pixel Komunika')
        ->assertSee('Aksesoris')
        ->assertSee('konektivitas')
        ->assertSee('Ikhtisar kategori')
        ->assertSee('Aksesoris elektronik')
        ->assertSee('Kartu')
        ->assertSee('voucher')
        ->assertSee('Pulsa')
        ->assertSee('Permintaan tinggi')
        ->assertSee('Kenapa berbelanja di sini')
        ->assertSee('Akun terverifikasi');
});
