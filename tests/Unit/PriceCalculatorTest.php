<?php

use App\Domains\Pricing\PriceCalculator;
use App\Models\Category;
use App\Models\CategoryTaxRule;
use App\Models\Product;
use App\Models\ProductPrice;

// CR-022: Partai harga dasar; grosir termurah yang minimumnya tercapai menang. Contoh klien 11PDNINT03GB.
it('uses partai below grosir minimums and the cheapest reached grosir tier above them', function () {
    $category = Category::query()->create([
        'pos_category_id' => 'CAT-TEST',
        'name' => 'Test',
        'is_active' => true,
    ]);

    $product = Product::query()->create([
        'category_id' => $category->id,
        'pos_product_id' => '11PDNINT03GB',
        'sku' => '11PDNINT03GB',
        'name' => 'Produk Test',
        'is_active' => true,
    ]);

    $product->prices()->createMany([
        ['price_type' => ProductPrice::RETAIL, 'amount' => 35000],
        ['price_type' => ProductPrice::BULK, 'amount' => 25500],
        ['price_type' => ProductPrice::WHOLESALE, 'amount' => 25000, 'minimum_quantity' => 10],
        ['price_type' => ProductPrice::WHOLESALE_2, 'amount' => 21500, 'minimum_quantity' => 20],
    ]);

    $calculator = app(PriceCalculator::class);
    $amount = fn (int $qty) => (float) $calculator->resolvePrice($product->fresh('prices'), $qty)->amount;

    expect($amount(1))->toBe(25500.0)
        ->and($amount(9))->toBe(25500.0)
        ->and($amount(10))->toBe(25000.0)
        ->and($amount(20))->toBe(21500.0);
});

it('never picks a grosir tier that costs more than partai or has no minimum', function () {
    $category = Category::query()->create(['pos_category_id' => 'CAT-TEST', 'name' => 'Test', 'is_active' => true]);
    $product = Product::query()->create(['category_id' => $category->id, 'pos_product_id' => 'P2', 'sku' => 'P2', 'name' => 'P2', 'is_active' => true]);
    $product->prices()->createMany([
        ['price_type' => ProductPrice::BULK, 'amount' => 10000],
        ['price_type' => ProductPrice::WHOLESALE, 'amount' => 11000, 'minimum_quantity' => 5],
        ['price_type' => ProductPrice::WHOLESALE_2, 'amount' => 9000, 'minimum_quantity' => 0],
    ]);

    expect(app(PriceCalculator::class)->resolvePrice($product->fresh('prices'), 50)->price_type)->toBe(ProductPrice::BULK);
});

it('calculates pph22 as (triggered subtotal / 1.11) × rate', function () {
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

    // (1500000 / 1.11) * 0.5% = 6756.756... → 6757.0
    expect($result['total'])->toBe(6757.0)
        ->and($result['aggregate']['basis_amount'])->toBe(1500000.0)
        ->and($result['aggregate']['divisor'])->toBe(1.11);
});
