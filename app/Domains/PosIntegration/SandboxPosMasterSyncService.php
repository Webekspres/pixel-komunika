<?php

namespace App\Domains\PosIntegration;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductEnrichment;
use App\Models\ProductPrice;
use App\Models\SyncError;
use App\Models\SyncRun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class SandboxPosMasterSyncService implements PosMasterSyncInterface
{
    public const SUPPORTED_PRICE_TYPES = [
        'Eceran' => ProductPrice::RETAIL,
        'Partai' => ProductPrice::BULK,
        'Grosir 1' => ProductPrice::WHOLESALE,
        'Grosir 2' => ProductPrice::WHOLESALE_2,
    ];

    public function __construct(
        protected PosApiClient $client,
    ) {}

    public function syncMasters(): SyncRun
    {
        $run = SyncRun::query()->create([
            'sync_type' => 'MASTER_FULL',
            'source' => 'SANDBOX',
            'status' => 'RUNNING',
            'started_at' => now(),
            'correlation_id' => (string) Str::uuid(),
            'created_at' => now(),
        ]);

        try {
            $categories = $this->client->categories();
            $products = $this->client->products();
            $pricelists = $this->client->pricelist();

            $validationErrors = $this->validatePayloads($categories, $products, $pricelists);

            foreach ($validationErrors as $err) {
                SyncError::query()->create([
                    'sync_run_id' => $run->id,
                    'entity_type' => $err['entity_type'],
                    'external_id' => $err['external_id'] ?? null,
                    'error_code' => $err['error_code'],
                    'error_message' => $err['error_message'],
                    'payload_excerpt_redacted' => isset($err['payload']) ? $this->redactPayload($err['payload']) : null,
                    'retryable' => false,
                    'created_at' => now(),
                ]);
            }

            // Kak Rio (7 Okt): data master bermasalah diabaikan; produk tidak valid dilewati
            // dan dicatat tanpa menggagalkan produk lain.
            $invalidItemIds = collect($validationErrors)
                ->whereIn('entity_type', ['product', 'pricelist'])
                ->pluck('external_id')->filter()->flip();
            $validProducts = array_values(array_filter($products, function ($prod) use ($invalidItemIds) {
                $itemId = trim((string) ($prod['item_id'] ?? ''));

                return $itemId !== '' && ! $invalidItemIds->has($itemId);
            }));
            $errorSummary = collect($validationErrors)->pluck('error_code')->countBy()->toArray();

            if (! empty($validationErrors) && $validProducts === []) {
                $run->update([
                    'status' => 'FAILED',
                    'finished_at' => now(),
                    'failed_count' => count($validationErrors),
                    'summary' => [
                        'message' => 'Pre-write validation failed with '.count($validationErrors).' errors; no valid product.',
                        'errors' => $errorSummary,
                    ],
                ]);

                return $run->fresh();
            }

            $validCategories = array_values(array_filter(
                $categories,
                fn ($cat) => trim((string) ($cat['category_id'] ?? '')) !== '',
            ));

            $this->persistMasters($validCategories, $validProducts, $pricelists);

            $skipped = count($products) - count($validProducts);
            $run->update([
                'status' => empty($validationErrors) ? 'SUCCEEDED' : 'PARTIAL',
                'finished_at' => now(),
                'success_count' => count($validProducts),
                'failed_count' => $skipped,
                'summary' => [
                    'message' => empty($validationErrors)
                        ? 'Master sync succeeded via SandboxPosMasterSyncService.'
                        : "Master sync partial: {$skipped} invalid product(s) skipped.",
                    'categories_count' => count($validCategories),
                    'products_count' => count($validProducts),
                    'pricelists_count' => count($pricelists),
                    'errors' => $errorSummary,
                ],
            ]);
        } catch (Throwable $e) {
            SyncError::query()->create([
                'sync_run_id' => $run->id,
                'entity_type' => 'master_sync',
                'error_code' => 'SYNC_FAILED',
                'error_message' => $this->client->redact($e->getMessage()),
                'retryable' => true,
                'created_at' => now(),
            ]);

            $run->update([
                'status' => 'FAILED',
                'finished_at' => now(),
                'failed_count' => 1,
                'summary' => [
                    'message' => 'Sync failed due to exception: '.$this->client->redact($e->getMessage()),
                ],
            ]);
        }

        return $run->fresh();
    }

    public function validatePayloads(array $categories, array $products, array $pricelists): array
    {
        $errors = [];

        // 1. Validate categories
        $categoryIds = [];
        foreach ($categories as $cat) {
            $catId = isset($cat['category_id']) ? trim((string) $cat['category_id']) : '';
            if ($catId === '') {
                $errors[] = [
                    'entity_type' => 'category',
                    'external_id' => null,
                    'error_code' => 'INVALID_IDENTIFIER',
                    'error_message' => 'Category identifier is empty or missing.',
                    'payload' => $cat,
                ];

                continue;
            }

            if (isset($categoryIds[$catId])) {
                $errors[] = [
                    'entity_type' => 'category',
                    'external_id' => $catId,
                    'error_code' => 'DUPLICATE_CATEGORY_ID',
                    'error_message' => "Duplicate category_id [{$catId}] in category payload.",
                    'payload' => $cat,
                ];
            }
            $categoryIds[$catId] = true;
        }

        // 2. Validate products
        $productIds = [];
        foreach ($products as $prod) {
            $itemId = isset($prod['item_id']) ? trim((string) $prod['item_id']) : '';
            if ($itemId === '') {
                $errors[] = [
                    'entity_type' => 'product',
                    'external_id' => null,
                    'error_code' => 'INVALID_IDENTIFIER',
                    'error_message' => 'Product item_id is empty or missing.',
                    'payload' => $prod,
                ];

                continue;
            }

            if (isset($productIds[$itemId])) {
                $errors[] = [
                    'entity_type' => 'product',
                    'external_id' => $itemId,
                    'error_code' => 'DUPLICATE_ITEM_ID',
                    'error_message' => "Duplicate item_id [{$itemId}] found in products payload.",
                    'payload' => $prod,
                ];
            }
            $productIds[$itemId] = true;

            $prodCatId = isset($prod['category_id']) ? trim((string) $prod['category_id']) : '';
            if ($prodCatId === '' || (! isset($categoryIds[$prodCatId]) && ! Category::query()->where('pos_category_id', $prodCatId)->exists())) {
                $errors[] = [
                    'entity_type' => 'product',
                    'external_id' => $itemId,
                    'error_code' => 'MISSING_CATEGORY_REFERENCE',
                    'error_message' => "Category reference [{$prodCatId}] for product [{$itemId}] not found.",
                    'payload' => $prod,
                ];
            }
        }

        // 3. Validate pricelists
        $pricelistProductIds = [];
        $pricelistsIndexed = [];

        foreach ($pricelists as $priceEntry) {
            $itemId = isset($priceEntry['item_id']) ? trim((string) $priceEntry['item_id']) : '';
            if ($itemId === '') {
                $errors[] = [
                    'entity_type' => 'pricelist',
                    'external_id' => null,
                    'error_code' => 'INVALID_IDENTIFIER',
                    'error_message' => 'Pricelist item_id is empty or missing.',
                    'payload' => $priceEntry,
                ];

                continue;
            }

            if (isset($pricelistProductIds[$itemId])) {
                $errors[] = [
                    'entity_type' => 'pricelist',
                    'external_id' => $itemId,
                    'error_code' => 'DUPLICATE_PRICELIST_ITEM_ID',
                    'error_message' => "Duplicate item_id [{$itemId}] found in pricelist payload.",
                    'payload' => $priceEntry,
                ];
            }
            $pricelistProductIds[$itemId] = true;
            $pricelistsIndexed[$itemId] = $priceEntry;

            $prices = $priceEntry['price'] ?? [];
            if (! is_array($prices) || empty($prices)) {
                $errors[] = [
                    'entity_type' => 'pricelist',
                    'external_id' => $itemId,
                    'error_code' => 'EMPTY_PRICELIST',
                    'error_message' => "Pricelist for product [{$itemId}] has no price rows.",
                    'payload' => $priceEntry,
                ];

                continue;
            }

            $seenLocalTypes = [];
            foreach ($prices as $tier) {
                $rawType = $tier['type'] ?? null;
                if (! isset(self::SUPPORTED_PRICE_TYPES[$rawType])) {
                    $errors[] = [
                        'entity_type' => 'pricelist',
                        'external_id' => $itemId,
                        'error_code' => 'UNSUPPORTED_PRICE_TYPE',
                        'error_message' => "Unsupported price type [{$rawType}] for product [{$itemId}].",
                        'payload' => $tier,
                    ];

                    continue;
                }

                $localType = self::SUPPORTED_PRICE_TYPES[$rawType];
                if (isset($seenLocalTypes[$localType])) {
                    $errors[] = [
                        'entity_type' => 'pricelist',
                        'external_id' => $itemId,
                        'error_code' => 'DUPLICATE_PRICE_TIER',
                        'error_message' => "Duplicate local price tier [{$localType}] for product [{$itemId}].",
                        'payload' => $tier,
                    ];
                }
                $seenLocalTypes[$localType] = true;

                $amount = $tier['amount'] ?? null;
                if (! is_numeric($amount) || (float) $amount < 0) {
                    $errors[] = [
                        'entity_type' => 'pricelist',
                        'external_id' => $itemId,
                        'error_code' => 'NEGATIVE_OR_INVALID_AMOUNT',
                        'error_message' => "Negative or non-numeric price amount [{$amount}] for product [{$itemId}].",
                        'payload' => $tier,
                    ];
                }

                $minQty = $tier['min_qty'] ?? null;
                if ($minQty !== null && (! is_numeric($minQty) || (int) $minQty < 1)) {
                    $errors[] = [
                        'entity_type' => 'pricelist',
                        'external_id' => $itemId,
                        'error_code' => 'INVALID_MIN_QTY',
                        'error_message' => "Invalid min_qty [{$minQty}] for product [{$itemId}].",
                        'payload' => $tier,
                    ];
                }
            }
        }

        // 4. Validate all products have pricelist
        foreach (array_keys($productIds) as $itemId) {
            if (! isset($pricelistsIndexed[$itemId]) || empty($pricelistsIndexed[$itemId]['price'])) {
                $errors[] = [
                    'entity_type' => 'product',
                    'external_id' => $itemId,
                    'error_code' => 'PRODUCT_WITHOUT_PRICE',
                    'error_message' => "Product [{$itemId}] does not have pricelist entry.",
                ];
            }
        }

        return $errors;
    }

    protected function persistMasters(array $categories, array $products, array $pricelists): void
    {
        $pricelistsIndexed = [];
        foreach ($pricelists as $pl) {
            if (isset($pl['item_id'])) {
                $pricelistsIndexed[(string) $pl['item_id']] = $pl;
            }
        }

        DB::transaction(function () use ($categories, $products, $pricelistsIndexed): void {
            $categoryMap = [];
            foreach ($categories as $cat) {
                $posCatId = (string) $cat['category_id'];
                $category = Category::query()->firstOrNew(['pos_category_id' => $posCatId]);
                $category->name = (string) $cat['category_name'];
                $category->is_active = true;
                $category->synced_at = now();
                $category->save();

                $categoryMap[$posCatId] = $category;
            }

            $brandMap = [];
            foreach ($products as $prod) {
                $rawBrand = trim((string) ($prod['brand_name'] ?? ''));
                if ($rawBrand !== '' && ! isset($brandMap[$rawBrand])) {
                    $brand = Brand::query()->firstOrCreate(
                        ['name' => $rawBrand],
                        [
                            'pos_brand_id' => null,
                            'is_active' => true,
                            'synced_at' => now(),
                        ]
                    );
                    $brandMap[$rawBrand] = $brand;
                }
            }

            foreach ($products as $prod) {
                $posProductId = (string) $prod['item_id'];
                $rawBrand = trim((string) ($prod['brand_name'] ?? ''));
                $brandId = $rawBrand !== '' ? ($brandMap[$rawBrand]->id ?? null) : null;

                $posCatId = (string) $prod['category_id'];
                $categoryId = $categoryMap[$posCatId]->id
                    ?? Category::query()->where('pos_category_id', $posCatId)->value('id');

                $existingProduct = Product::query()->where('pos_product_id', $posProductId)->first();
                $pph22Applicable = (int) ($prod['pph'] ?? 0) === 1;

                if ($existingProduct) {
                    $product = $existingProduct;
                    $product->update([
                        'name' => (string) $prod['item_name'],
                        'category_id' => $categoryId,
                        'brand_id' => $brandId,
                        'pph22_applicable' => $pph22Applicable,
                        'synced_at' => now(),
                    ]);
                } else {
                    $product = Product::query()->create([
                        'pos_product_id' => $posProductId,
                        'sku' => $posProductId,
                        'name' => (string) $prod['item_name'],
                        'category_id' => $categoryId,
                        'brand_id' => $brandId,
                        'is_active' => true,
                        'pph22_applicable' => $pph22Applicable,
                        'synced_at' => now(),
                    ]);

                    ProductEnrichment::query()->create([
                        'product_id' => $product->id,
                        'slug' => Str::slug($product->name).'-'.strtolower($posProductId),
                        'display_order' => 0,
                        'is_visible' => true,
                    ]);
                }

                $priceRows = $pricelistsIndexed[$posProductId]['price'] ?? [];
                foreach ($priceRows as $row) {
                    $typeKey = $row['type'];
                    if (! isset(self::SUPPORTED_PRICE_TYPES[$typeKey])) {
                        continue;
                    }

                    $localType = self::SUPPORTED_PRICE_TYPES[$typeKey];
                    $minQty = isset($row['min_qty']) ? (int) $row['min_qty'] : null;

                    ProductPrice::query()->updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'price_type' => $localType,
                        ],
                        [
                            'amount' => $row['amount'],
                            'minimum_quantity' => $minQty,
                            'pos_price_id' => $posProductId.'-'.$localType,
                            'synced_at' => now(),
                        ]
                    );
                }
            }
        });
    }

    protected function redactPayload(array $payload): array
    {
        return array_map(function ($val) {
            return is_string($val) ? $this->client->redact($val) : $val;
        }, $payload);
    }
}
