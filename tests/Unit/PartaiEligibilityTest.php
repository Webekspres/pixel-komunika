<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use App\Services\CartService;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('triggers partai pricing when one sku reaches minimum 5 units', function () {
    $cartService = app(CartService::class);
    $user = User::factory()->create();
    $cart = $cartService->getOrCreateCart($user);

    $productA = Product::where('sku', 'PB-10000')->firstOrFail();
    $cartService->addItem($cart, $productA->id, 5);

    $summary = $cartService->getCartSummary($cart);
    $line = $summary['items']->first();

    expect($line['price_type'])->toBe(ProductPrice::BULK);
});

it('does not aggregate quantities across skus for partai eligibility', function () {
    $cartService = app(CartService::class);
    $user = User::factory()->create();
    $cart = $cartService->getOrCreateCart($user);

    $productA = Product::where('sku', 'PB-10000')->firstOrFail();
    $productB = Product::where('sku', 'HP-WL')->firstOrFail();

    $cartService->addItem($cart, $productA->id, 4);
    $cartService->addItem($cart, $productB->id, 4);

    $summary = $cartService->getCartSummary($cart);

    foreach ($summary['items'] as $line) {
        expect($line['price_type'])->toBe(ProductPrice::RETAIL);
    }
});

it('applies partai cart-wide when any single sku qualifies', function () {
    $cartService = app(CartService::class);
    $user = User::factory()->create();
    $cart = $cartService->getOrCreateCart($user);

    $productA = Product::where('sku', 'PB-10000')->firstOrFail();
    $productB = Product::where('sku', 'HP-WL')->firstOrFail();

    $cartService->addItem($cart, $productA->id, 5);
    $cartService->addItem($cart, $productB->id, 2);

    $summary = $cartService->getCartSummary($cart);
    $lines = $summary['items']->keyBy('product_id');

    expect($lines[$productA->id]['price_type'])->toBe(ProductPrice::BULK)
        ->and($lines[$productB->id]['price_type'])->toBe(ProductPrice::BULK);
});
