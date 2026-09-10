<?php

use App\Models\Order;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Storage;

it('seeds sample orders without repeating the processing transition', function () {
    Storage::fake('local');

    app(DatabaseSeeder::class)->run();

    expect(Order::query()->where('status', 'processing')->count())->toBe(1)
        ->and(Order::query()->where('status', 'shipped')->count())->toBe(1)
        ->and(Order::query()->where('status', 'cancelled')->count())->toBe(1);
});
