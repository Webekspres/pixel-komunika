<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Category;
use App\Models\CategoryTaxRule;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('allows admins to manage category tax rules including zero percent rate', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::query()->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.tax-rules.index'))
        ->assertOk()
        ->assertSee($category->name);

    $this->actingAs($admin)
        ->patch(route('admin.tax-rules.update', $category), [
            'threshold_amount' => 100000,
            'rate_percent' => 0,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.tax-rules.index'));

    $rule = CategoryTaxRule::query()->where('category_id', $category->id)->first();

    expect($rule)->not->toBeNull()
        ->and((float) $rule->threshold_amount)->toBe(100000.0)
        ->and((float) $rule->rate_percent)->toBe(0.0)
        ->and($rule->is_active)->toBeTrue();
});

it('applies updated tax rule to cart summary', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::query()->firstOrFail();

    $this->actingAs($admin)
        ->patch(route('admin.tax-rules.update', $category), [
            'threshold_amount' => 1000,
            'rate_percent' => 1.5,
            'is_active' => '1',
        ]);

    $user = User::factory()->create();
    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($user);

    $product = Product::query()->where('category_id', $category->id)->firstOrFail();
    $cartService->addItem($cart, $product->id, 5);

    $summary = $cartService->getCartSummary($cart);

    expect($summary['pph22'])->toBeGreaterThan(0);
});

it('blocks non-admins from tax rule management', function () {
    $customer = User::factory()->create();
    $customer->customerProfile()->create(['verification_status' => 'ACTIVE']);

    $this->actingAs($customer)
        ->get(route('admin.tax-rules.index'))
        ->assertForbidden();
});
