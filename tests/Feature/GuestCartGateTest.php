<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Livewire\Storefront\ProductIndex;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Livewire\Livewire;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('redirects guests to login when adding a product to cart', function () {
    $product = Product::query()->firstOrFail();

    Livewire::test(ProductIndex::class)
        ->call('addToCart', $product->id)
        ->assertRedirect(route('login'));

    expect(CartItem::query()->count())->toBe(0)
        ->and(session('error'))->toContain('masuk');
});

it('rejects guest carts at the cart service level', function () {
    $product = Product::query()->firstOrFail();
    $guestCart = app(CartService::class)->getOrCreateCart(null, 'guest-session');

    expect(fn () => app(CartService::class)->addItem($guestCart, $product->id, 1))
        ->toThrow(InvalidArgumentException::class, 'masuk')
        ->and(CartItem::query()->count())->toBe(0);
});
