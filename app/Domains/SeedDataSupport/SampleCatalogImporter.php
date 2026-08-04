<?php

namespace App\Domains\SeedDataSupport;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryTaxRule;
use App\Models\InventoryLedger;
use App\Models\InventorySnapshot;
use App\Models\Product;
use App\Models\ProductEnrichment;
use App\Models\ProductPrice;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleCatalogImporter
{
    public function import(): void
    {
        DB::transaction(function (): void {
            foreach ($this->dataset() as $categoryData) {
                $category = Category::query()->updateOrCreate(
                    ['pos_category_id' => $categoryData['pos_category_id']],
                    Arr::except($categoryData, ['tax_rule', 'products']),
                );

                CategoryTaxRule::query()->updateOrCreate(
                    ['category_id' => $category->id],
                    $categoryData['tax_rule'],
                );

                foreach ($categoryData['products'] as $productData) {
                    $brand = Brand::query()->updateOrCreate(
                        ['pos_brand_id' => $productData['brand']['pos_brand_id']],
                        $productData['brand'],
                    );

                    $product = Product::query()->updateOrCreate(
                        ['pos_product_id' => $productData['pos_product_id']],
                        array_merge(
                            Arr::except($productData, ['brand', 'prices', 'inventory', 'enrichment']),
                            [
                                'category_id' => $category->id,
                                'brand_id' => $brand->id,
                            ],
                        ),
                    );

                    ProductEnrichment::query()->updateOrCreate(
                        ['product_id' => $product->id],
                        $productData['enrichment'],
                    );

                    foreach ($productData['prices'] as $price) {
                        ProductPrice::query()->updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'price_type' => $price['price_type'],
                            ],
                            $price,
                        );
                    }

                    $snapshot = InventorySnapshot::query()->firstOrNew([
                        'product_id' => $product->id,
                    ]);

                    $before = (int) ($snapshot->quantity_available ?? 0);
                    $after = (int) $productData['inventory']['quantity_available'];

                    $snapshot->fill($productData['inventory'])->save();

                    InventoryLedger::query()->create([
                        'product_id' => $product->id,
                        'source' => 'FULL_SYNC',
                        'quantity_before' => $before,
                        'quantity_after' => $after,
                        'quantity_delta' => $after - $before,
                        'external_reference' => 'seed:'.$product->sku,
                        'occurred_at' => now(),
                        'meta' => ['dataset' => 'sample_catalog'],
                    ]);
                }
            }
        });
    }

    public function dataset(): array
    {
        return [
            [
                'pos_category_id' => 'CAT-ACCESSORIES',
                'name' => 'Aksesoris Elektronik',
                'is_active' => true,
                'source_updated_at' => now(),
                'synced_at' => now(),
                'tax_rule' => [
                    'threshold_amount' => 5000000,
                    'rate_percent' => 0.5000,
                    'is_active' => true,
                ],
                'products' => [
                    $this->product(
                        posProductId: 'PROD-PB10000',
                        sku: 'PB-10000',
                        name: 'Power Bank 10.000 mAh',
                        brand: ['pos_brand_id' => 'BRAND-PIXEL', 'name' => 'Pixel Gear', 'is_active' => true, 'source_updated_at' => now(), 'synced_at' => now()],
                        prices: [175000, 165000, 155000],
                        wholesaleMinimum: 12,
                        stock: 48,
                        lowStockThreshold: 8,
                    ),
                    $this->product(
                        posProductId: 'PROD-HP-WL',
                        sku: 'HP-WL',
                        name: 'Headphone Wireless',
                        brand: ['pos_brand_id' => 'BRAND-AUDIO', 'name' => 'Audio Plus', 'is_active' => true, 'source_updated_at' => now(), 'synced_at' => now()],
                        prices: [220000, 205000, 195000],
                        wholesaleMinimum: 10,
                        stock: 9,
                        lowStockThreshold: 10,
                    ),
                ],
            ],
            [
                'pos_category_id' => 'CAT-VOUCHER',
                'name' => 'Kartu Data & Voucher',
                'is_active' => true,
                'source_updated_at' => now(),
                'synced_at' => now(),
                'tax_rule' => [
                    'threshold_amount' => 2500000,
                    'rate_percent' => 0.3000,
                    'is_active' => true,
                ],
                'products' => [
                    $this->product(
                        posProductId: 'PROD-VCHR-TSEL',
                        sku: 'VCHR-TSEL-25',
                        name: 'Voucher Internet 25GB',
                        brand: ['pos_brand_id' => 'BRAND-TSEL', 'name' => 'Telkomsel', 'is_active' => true, 'source_updated_at' => now(), 'synced_at' => now()],
                        prices: [82000, 79000, 76000],
                        wholesaleMinimum: 20,
                        stock: 120,
                        lowStockThreshold: 25,
                    ),
                ],
            ],
            [
                'pos_category_id' => 'CAT-PULSA',
                'name' => 'Pulsa',
                'is_active' => true,
                'source_updated_at' => now(),
                'synced_at' => now(),
                'tax_rule' => [
                    'threshold_amount' => 0,
                    'rate_percent' => 0,
                    'is_active' => true,
                ],
                'products' => [
                    $this->product(
                        posProductId: 'PROD-PLS-100',
                        sku: 'PLS-100',
                        name: 'Pulsa Reguler 100K',
                        brand: ['pos_brand_id' => 'BRAND-XL', 'name' => 'XL', 'is_active' => true, 'source_updated_at' => now(), 'synced_at' => now()],
                        prices: [101500, 100500, 99500],
                        wholesaleMinimum: 25,
                        stock: 300,
                        lowStockThreshold: 50,
                    ),
                ],
            ],
        ];
    }

    private function product(
        string $posProductId,
        string $sku,
        string $name,
        array $brand,
        array $prices,
        int $wholesaleMinimum,
        int $stock,
        int $lowStockThreshold,
    ): array {
        $slug = Str::slug($name);

        return [
            'pos_product_id' => $posProductId,
            'sku' => $sku,
            'name' => $name,
            'weight_grams' => 500,
            'length_cm' => 10,
            'width_cm' => 8,
            'height_cm' => 4,
            'is_active' => true,
            'synced_at' => now(),
            'brand' => $brand,
            'enrichment' => [
                'slug' => $slug,
                'short_description' => 'Data contoh untuk development dan UAT.',
                'description' => 'Placeholder katalog yang mengikuti jalur import yang sama dengan adapter POS.',
                'label' => 'Sample',
                'display_order' => 0,
                'is_visible' => true,
            ],
            'prices' => [
                [
                    'price_type' => ProductPrice::RETAIL,
                    'amount' => $prices[0],
                    'minimum_quantity' => null,
                    'pos_price_id' => $posProductId.'-ECERAN',
                    'synced_at' => now(),
                ],
                [
                    'price_type' => ProductPrice::BULK,
                    'amount' => $prices[1],
                    'minimum_quantity' => 5,
                    'pos_price_id' => $posProductId.'-PARTAI',
                    'synced_at' => now(),
                ],
                [
                    'price_type' => ProductPrice::WHOLESALE,
                    'amount' => $prices[2],
                    'minimum_quantity' => $wholesaleMinimum,
                    'pos_price_id' => $posProductId.'-GROSIR',
                    'synced_at' => now(),
                ],
            ],
            'inventory' => [
                'quantity_available' => $stock,
                'low_stock_threshold' => $lowStockThreshold,
                'stock_status' => $stock <= 0
                    ? InventorySnapshot::OUT
                    : ($stock <= $lowStockThreshold ? InventorySnapshot::LOW : InventorySnapshot::AVAILABLE),
                'source_updated_at' => now(),
                'synced_at' => now(),
            ],
        ];
    }
}
