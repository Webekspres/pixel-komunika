<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('can add products to cart, update quantity, and get summary', function () {
    $cartService = app(CartService::class);
    $user = User::factory()->create();

    $cart = $cartService->getOrCreateCart($user);
    $product = Product::where('sku', 'PB-10000')->firstOrFail();

    // 1. Add item
    $cartItem = $cartService->addItem($cart, $product->id, 2);
    expect($cartItem->quantity)->toBe(2);

    $summary = $cartService->getCartSummary($cart);
    expect($summary['total_items'])->toBe(2)
        ->and($summary['items'])->toHaveCount(1)
        ->and($summary['subtotal'])->toBeGreaterThan(0);

    // 2. Update quantity
    $cartService->updateQuantity($cart, $cartItem->id, 5);
    $summaryAfter = $cartService->getCartSummary($cart);
    expect($summaryAfter['total_items'])->toBe(5);

    // 3. Remove item
    $cartService->removeItem($cart, $cartItem->id);
    $summaryEmpty = $cartService->getCartSummary($cart);
    expect($summaryEmpty['total_items'])->toBe(0);
});

it('throws exception if quantity added exceeds available stock', function () {
    $cartService = app(CartService::class);
    $user = User::factory()->create();
    $cart = $cartService->getOrCreateCart($user);
    $product = Product::where('sku', 'PB-10000')->firstOrFail();

    $cartService->addItem($cart, $product->id, 9999);
})->throws(InvalidArgumentException::class);
