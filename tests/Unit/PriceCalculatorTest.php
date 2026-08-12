<?php

use App\Domains\Pricing\PriceCalculator;
use App\Models\Category;
use App\Models\CategoryTaxRule;
use App\Models\Product;
use App\Models\ProductPrice;

it('picks wholesale before bulk and bulk before retail fallback', function () {
    $category = Category::query()->create([
        'pos_category_id' => 'CAT-TEST',
        'name' => 'Test',
        'is_active' => true,
    ]);

    $product = Product::query()->create([
        'category_id' => $category->id,
        'pos_product_id' => 'PROD-TEST',
        'sku' => 'SKU-TEST',
        'name' => 'Produk Test',
        'is_active' => true,
    ]);

    $product->prices()->createMany([
        ['price_type' => ProductPrice::RETAIL, 'amount' => 100000],
        ['price_type' => ProductPrice::BULK, 'amount' => 90000, 'minimum_quantity' => 5],
        ['price_type' => ProductPrice::WHOLESALE, 'amount' => 80000, 'minimum_quantity' => 10],
    ]);

    $calculator = app(PriceCalculator::class);

    expect($calculator->resolvePrice($product->fresh('prices'), 2, false)->price_type)->toBe(ProductPrice::RETAIL)
        ->and($calculator->resolvePrice($product->fresh('prices'), 5, false)->price_type)->toBe(ProductPrice::BULK)
        ->and($calculator->resolvePrice($product->fresh('prices'), 2, true)->price_type)->toBe(ProductPrice::BULK)
        ->and($calculator->resolvePrice($product->fresh('prices'), 12, true)->price_type)->toBe(ProductPrice::WHOLESALE);
});

it('aggregates pph22 per category rule that passes its threshold', function () {
    $category = Category::query()->create([
        'pos_category_id' => 'CAT-TAX',
        'name' => 'Taxed',
        'is_active' => true,
    ]);

    CategoryTaxRule::query()->create([
        'category_id' => $category->id,
        'threshold_amount' => 1000000,
        'rate_percent' => 0.5000,
        'is_active' => true,
    ]);

    $calculator = app(PriceCalculator::class);

    $result = $calculator->calculatePph22(collect([
        ['category_id' => $category->id, 'line_total' => 1500000],
    ]));

    expect($result['total'])->toBe(7500.0)
        ->and($result['components'])->toHaveCount(1);
});
