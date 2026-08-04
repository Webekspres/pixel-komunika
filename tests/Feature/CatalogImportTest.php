<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Category;
use App\Models\InventoryLedger;
use App\Models\Product;
use App\Models\ProductPrice;

it('imports sample catalog data through an idempotent path', function () {
    $importer = app(SampleCatalogImporter::class);

    $importer->import();
    $importer->import();

    expect(Category::query()->count())->toBe(3)
        ->and(Product::query()->count())->toBe(4)
        ->and(ProductPrice::query()->count())->toBe(12)
        ->and(InventoryLedger::query()->count())->toBe(8);

    $product = Product::query()->where('sku', 'PB-10000')->firstOrFail();

    expect($product->enrichment)->not->toBeNull()
        ->and($product->inventorySnapshot->stock_status)->toBe('TERSEDIA');
});
