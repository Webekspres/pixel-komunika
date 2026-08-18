<?php

use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Support\Facades\Storage;

it('wipes product media files when the database is refreshed in local', function () {
    Storage::fake('public');
    Storage::disk('public')->put('product-media/foto.jpg', 'foto');

    app()->instance('env', 'local');
    event(new DatabaseRefreshed);

    expect(Storage::disk('public')->exists('product-media/foto.jpg'))->toBeFalse();
});

it('keeps product media files when the database is refreshed outside local', function () {
    Storage::fake('public');
    Storage::disk('public')->put('product-media/foto.jpg', 'foto');

    app()->instance('env', 'testing');
    event(new DatabaseRefreshed);

    expect(Storage::disk('public')->exists('product-media/foto.jpg'))->toBeTrue();
});
