<?php

use App\Domains\PosIntegration\PosApiClient;
use App\Domains\PosIntegration\PosMasterSyncInterface;
use App\Domains\PosIntegration\SamplePosSyncService;
use App\Domains\PosIntegration\SandboxPosMasterSyncService;
use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Category;
use App\Models\CategoryTaxRule;
use App\Models\Product;
use App\Models\ProductEnrichment;
use App\Models\ProductPrice;
use App\Models\SyncError;
use Illuminate\Support\Facades\Http;

it('defaults pos driver to sample', function () {
    expect(config('pos.driver'))->toBe('sample');

    $resolved = app(PosMasterSyncInterface::class);
    expect($resolved)->toBeInstanceOf(SamplePosSyncService::class);
});

it('prevents sample catalog import in production', function () {
    $originalEnv = app()->environment();
    app()->detectEnvironment(fn () => 'production');

    try {
        $importer = app(SampleCatalogImporter::class);
        $importer->import();
        $this->fail('Expected exception was not thrown.');
    } catch (RuntimeException $e) {
        expect($e->getMessage())->toContain('Sample catalog data must never be imported in production');
    } finally {
        app()->detectEnvironment(fn () => $originalEnv);
    }
});

it('fails sync and records sync error when duplicate item_id is present', function () {
    Http::fake([
        'https://pos-sandbox.test/master/category' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                ['category_id' => 'CAT1', 'category_name' => 'Kategori 1'],
            ],
        ], 200),
        'https://pos-sandbox.test/master/product' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                ['item_id' => 'ITEM-DUP', 'item_name' => 'Produk A', 'category_id' => 'CAT1'],
                ['item_id' => 'ITEM-DUP', 'item_name' => 'Produk B', 'category_id' => 'CAT1'],
            ],
        ], 200),
        'https://pos-sandbox.test/master/pricelist' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                [
                    'item_id' => 'ITEM-DUP',
                    'price' => [
                        ['type' => 'Eceran', 'amount' => 1000, 'min_qty' => 1],
                    ],
                ],
            ],
        ], 200),
    ]);

    $client = new PosApiClient('https://pos-sandbox.test', 'secret123');
    $service = new SandboxPosMasterSyncService($client);

    $run = $service->syncMasters();

    expect($run->status)->toBe('FAILED')
        ->and(SyncError::query()->where('error_code', 'DUPLICATE_ITEM_ID')->exists())->toBeTrue()
        ->and(Product::query()->where('pos_product_id', 'ITEM-DUP')->exists())->toBeFalse();
});

it('fails sync when duplicate price tier or negative price is present', function () {
    Http::fake([
        'https://pos-sandbox.test/master/category' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [['category_id' => 'CAT1', 'category_name' => 'Kategori 1']],
        ], 200),
        'https://pos-sandbox.test/master/product' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                ['item_id' => 'ITEM-1', 'item_name' => 'Produk 1', 'category_id' => 'CAT1'],
            ],
        ], 200),
        'https://pos-sandbox.test/master/pricelist' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                [
                    'item_id' => 'ITEM-1',
                    'price' => [
                        ['type' => 'Eceran', 'amount' => -500, 'min_qty' => 1],
                        ['type' => 'Eceran', 'amount' => 1000, 'min_qty' => 1],
                    ],
                ],
            ],
        ], 200),
    ]);

    $client = new PosApiClient('https://pos-sandbox.test', 'secret123');
    $service = new SandboxPosMasterSyncService($client);

    $run = $service->syncMasters();

    expect($run->status)->toBe('FAILED')
        ->and(SyncError::query()->where('error_code', 'NEGATIVE_OR_INVALID_AMOUNT')->exists())->toBeTrue()
        ->and(SyncError::query()->where('error_code', 'DUPLICATE_PRICE_TIER')->exists())->toBeTrue();
});

it('rejects foreign price types like Grosir 2 and tes as unsupported', function () {
    Http::fake([
        'https://pos-sandbox.test/master/category' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [['category_id' => 'CAT1', 'category_name' => 'Kategori 1']],
        ], 200),
        'https://pos-sandbox.test/master/product' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                ['item_id' => 'ITEM-FOREIGN', 'item_name' => 'Produk Asing', 'category_id' => 'CAT1'],
            ],
        ], 200),
        'https://pos-sandbox.test/master/pricelist' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                [
                    'item_id' => 'ITEM-FOREIGN',
                    'price' => [
                        ['type' => 'Grosir 2', 'amount' => 2000, 'min_qty' => 10],
                        ['type' => 'tes', 'amount' => 1500, 'min_qty' => 5],
                    ],
                ],
            ],
        ], 200),
    ]);

    $client = new PosApiClient('https://pos-sandbox.test', 'secret123');
    $service = new SandboxPosMasterSyncService($client);

    $run = $service->syncMasters();

    expect($run->status)->toBe('FAILED')
        ->and(SyncError::query()->where('error_code', 'UNSUPPORTED_PRICE_TYPE')->count())->toBe(2);
});

it('fails sync when a product does not have any price tier', function () {
    Http::fake([
        'https://pos-sandbox.test/master/category' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [['category_id' => 'CAT1', 'category_name' => 'Kategori 1']],
        ], 200),
        'https://pos-sandbox.test/master/product' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                ['item_id' => 'ITEM-NOPRICE', 'item_name' => 'Produk Tanpa Harga', 'category_id' => 'CAT1'],
            ],
        ], 200),
        'https://pos-sandbox.test/master/pricelist' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [],
        ], 200),
    ]);

    $client = new PosApiClient('https://pos-sandbox.test', 'secret123');
    $service = new SandboxPosMasterSyncService($client);

    $run = $service->syncMasters();

    expect($run->status)->toBe('FAILED')
        ->and(SyncError::query()->where('error_code', 'PRODUCT_WITHOUT_PRICE')->exists())->toBeTrue()
        ->and(Product::query()->where('pos_product_id', 'ITEM-NOPRICE')->exists())->toBeFalse();
});

it('successfully syncs valid master data and preserves local enrichment and tax rules', function () {
    // 1. Seed prior valid category and product with local enrichment and custom weight/dimensions
    $cat = Category::query()->create([
        'pos_category_id' => 'CAT-ORIG',
        'name' => 'Kategori Asli',
        'is_active' => true,
    ]);

    CategoryTaxRule::query()->create([
        'category_id' => $cat->id,
        'threshold_amount' => 1000000,
        'rate_percent' => 0.5,
        'is_active' => true,
    ]);

    $prod = Product::query()->create([
        'category_id' => $cat->id,
        'pos_product_id' => 'PROD-1',
        'sku' => 'CUSTOM-SKU-001',
        'name' => 'Nama Lama POS',
        'weight_grams' => 750,
        'length_cm' => 20,
        'width_cm' => 15,
        'height_cm' => 10,
        'is_active' => true,
    ]);

    ProductEnrichment::query()->create([
        'product_id' => $prod->id,
        'display_name' => 'Nama Tampilan Khusus Website',
        'slug' => 'custom-display-slug',
        'short_description' => 'Deskripsi website',
        'display_order' => 5,
        'is_visible' => true,
    ]);

    // 2. Incoming POS Sandbox payload with updated name and 3 tiers
    Http::fake([
        'https://pos-sandbox.test/master/category' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                ['category_id' => 'CAT-ORIG', 'category_name' => 'Kategori Baru POS', 'category_txt' => ''],
            ],
        ], 200),
        'https://pos-sandbox.test/master/product' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                [
                    'item_id' => 'PROD-1',
                    'item_name' => 'Nama Baru POS',
                    'brand_name' => ' Brand Ternama ',
                    'category_id' => 'CAT-ORIG',
                    'category_name' => 'Kategori Baru POS',
                    'ppn' => 1,
                    'pph' => 0,
                    'item_txt' => 'POS raw note',
                ],
            ],
        ], 200),
        'https://pos-sandbox.test/master/pricelist' => Http::response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                [
                    'item_id' => 'PROD-1',
                    'price' => [
                        ['type' => 'Eceran', 'amount' => 10000, 'min_qty' => 1],
                        ['type' => 'Partai', 'amount' => 9000, 'min_qty' => 5],
                        ['type' => 'Grosir 1', 'amount' => 8000, 'min_qty' => 20],
                    ],
                ],
            ],
        ], 200),
    ]);

    $client = new PosApiClient('https://pos-sandbox.test', 'secret123');
    $service = new SandboxPosMasterSyncService($client);

    $run = $service->syncMasters();

    expect($run->status)->toBe('SUCCEEDED');

    $prod->refresh();
    expect($prod->name)->toBe('Nama Baru POS')
        ->and($prod->sku)->toBe('CUSTOM-SKU-001') // Preserved!
        ->and($prod->weight_grams)->toBe(750) // Preserved!
        ->and((float) $prod->length_cm)->toBe(20.0) // Preserved!
        ->and($prod->brand->name)->toBe('Brand Ternama'); // Normalized!

    $enrichment = $prod->enrichment;
    expect($enrichment->display_name)->toBe('Nama Tampilan Khusus Website') // Preserved!
        ->and($enrichment->slug)->toBe('custom-display-slug') // Preserved!
        ->and($enrichment->description)->toBeNull(); // item_txt not leaked!

    // Assert prices mapped correctly
    $prices = $prod->prices()->pluck('amount', 'price_type')->toArray();
    expect((float) $prices[ProductPrice::RETAIL])->toBe(10000.0)
        ->and((float) $prices[ProductPrice::BULK])->toBe(9000.0)
        ->and((float) $prices[ProductPrice::WHOLESALE])->toBe(8000.0);

    // Assert tax rule preserved
    $taxRule = CategoryTaxRule::query()->where('category_id', $cat->id)->first();
    expect((float) $taxRule->rate_percent)->toBe(0.5);
});
