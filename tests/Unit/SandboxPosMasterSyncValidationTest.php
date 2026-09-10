<?php

namespace Tests\Unit;

use App\Domains\PosIntegration\PosApiClient;
use App\Domains\PosIntegration\SandboxPosMasterSyncService;
use PHPUnit\Framework\TestCase;

class SandboxPosMasterSyncValidationTest extends TestCase
{
    protected SandboxPosMasterSyncService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $client = new PosApiClient('https://pos-sandbox.test', 'dummy-key');
        $this->service = new SandboxPosMasterSyncService($client);
    }

    public function test_valid_payload_produces_zero_errors(): void
    {
        $categories = [
            ['category_id' => '1', 'category_name' => 'CAT 1'],
        ];
        $products = [
            ['item_id' => 'PROD-1', 'item_name' => 'Produk 1', 'category_id' => '1'],
        ];
        $pricelists = [
            [
                'item_id' => 'PROD-1',
                'price' => [
                    ['type' => 'Eceran', 'amount' => 1000, 'min_qty' => 1],
                    ['type' => 'Partai', 'amount' => 900, 'min_qty' => 5],
                    ['type' => 'Grosir 1', 'amount' => 800, 'min_qty' => 20],
                ],
            ],
        ];

        $errors = $this->service->validatePayloads($categories, $products, $pricelists);
        $this->assertEmpty($errors);
    }

    public function test_detects_duplicate_item_id_in_products(): void
    {
        $categories = [['category_id' => '1', 'category_name' => 'CAT 1']];
        $products = [
            ['item_id' => 'DUP-1', 'item_name' => 'Produk A', 'category_id' => '1'],
            ['item_id' => 'DUP-1', 'item_name' => 'Produk B', 'category_id' => '1'],
        ];
        $pricelists = [
            [
                'item_id' => 'DUP-1',
                'price' => [['type' => 'Eceran', 'amount' => 1000, 'min_qty' => 1]],
            ],
        ];

        $errors = $this->service->validatePayloads($categories, $products, $pricelists);
        $this->assertCount(1, $errors);
        $this->assertSame('DUPLICATE_ITEM_ID', $errors[0]['error_code']);
        $this->assertSame('DUP-1', $errors[0]['external_id']);
    }

    public function test_detects_duplicate_item_id_in_pricelists(): void
    {
        $categories = [['category_id' => '1', 'category_name' => 'CAT 1']];
        $products = [['item_id' => 'P1', 'item_name' => 'Produk 1', 'category_id' => '1']];
        $pricelists = [
            [
                'item_id' => 'P1',
                'price' => [['type' => 'Eceran', 'amount' => 1000, 'min_qty' => 1]],
            ],
            [
                'item_id' => 'P1',
                'price' => [['type' => 'Partai', 'amount' => 900, 'min_qty' => 5]],
            ],
        ];

        $errors = $this->service->validatePayloads($categories, $products, $pricelists);
        $errorCodes = array_column($errors, 'error_code');
        $this->assertContains('DUPLICATE_PRICELIST_ITEM_ID', $errorCodes);
    }

    public function test_detects_duplicate_price_tiers_for_same_product(): void
    {
        $categories = [['category_id' => '1', 'category_name' => 'CAT 1']];
        $products = [['item_id' => 'P1', 'item_name' => 'Produk 1', 'category_id' => '1']];
        $pricelists = [
            [
                'item_id' => 'P1',
                'price' => [
                    ['type' => 'Eceran', 'amount' => 1000, 'min_qty' => 1],
                    ['type' => 'Eceran', 'amount' => 1200, 'min_qty' => 1],
                ],
            ],
        ];

        $errors = $this->service->validatePayloads($categories, $products, $pricelists);
        $errorCodes = array_column($errors, 'error_code');
        $this->assertContains('DUPLICATE_PRICE_TIER', $errorCodes);
    }

    public function test_rejects_unsupported_price_types(): void
    {
        $categories = [['category_id' => '1', 'category_name' => 'CAT 1']];
        $products = [['item_id' => 'P1', 'item_name' => 'Produk 1', 'category_id' => '1']];
        $pricelists = [
            [
                'item_id' => 'P1',
                'price' => [
                    ['type' => 'Grosir 2', 'amount' => 1000, 'min_qty' => 1],
                    ['type' => 'tes', 'amount' => 1200, 'min_qty' => 1],
                ],
            ],
        ];

        $errors = $this->service->validatePayloads($categories, $products, $pricelists);
        $errorCodes = array_column($errors, 'error_code');
        $this->assertContains('UNSUPPORTED_PRICE_TYPE', $errorCodes);
    }

    public function test_detects_negative_or_invalid_amount_and_invalid_min_qty(): void
    {
        $categories = [['category_id' => '1', 'category_name' => 'CAT 1']];
        $products = [['item_id' => 'P1', 'item_name' => 'Produk 1', 'category_id' => '1']];
        $pricelists = [
            [
                'item_id' => 'P1',
                'price' => [
                    ['type' => 'Eceran', 'amount' => -100, 'min_qty' => 0],
                ],
            ],
        ];

        $errors = $this->service->validatePayloads($categories, $products, $pricelists);
        $errorCodes = array_column($errors, 'error_code');
        $this->assertContains('NEGATIVE_OR_INVALID_AMOUNT', $errorCodes);
        $this->assertContains('INVALID_MIN_QTY', $errorCodes);
    }

    public function test_detects_product_without_pricelist(): void
    {
        $categories = [['category_id' => '1', 'category_name' => 'CAT 1']];
        $products = [
            ['item_id' => 'P1', 'item_name' => 'Produk 1', 'category_id' => '1'],
            ['item_id' => 'P2', 'item_name' => 'Produk 2', 'category_id' => '1'],
        ];
        $pricelists = [
            [
                'item_id' => 'P1',
                'price' => [['type' => 'Eceran', 'amount' => 1000, 'min_qty' => 1]],
            ],
        ];

        $errors = $this->service->validatePayloads($categories, $products, $pricelists);
        $errorCodes = array_column($errors, 'error_code');
        $this->assertContains('PRODUCT_WITHOUT_PRICE', $errorCodes);
    }
}
