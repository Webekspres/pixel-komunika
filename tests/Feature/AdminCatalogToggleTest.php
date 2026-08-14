<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('lists categories with product counts and toggles status', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::query()->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSee($category->name)
        ->assertSee('Sinkron POS')
        ->assertSee('produk');

    $this->actingAs($admin)
        ->patch(route('admin.categories.toggle', $category), ['is_active' => '0'])
        ->assertRedirect();

    expect($category->fresh()->is_active)->toBeFalse();

    $this->actingAs($admin)
        ->patch(route('admin.categories.toggle', $category), ['is_active' => '1'])
        ->assertRedirect();

    expect($category->fresh()->is_active)->toBeTrue();
});

it('lists brands grid and toggles status', function () {
    $admin = User::factory()->admin()->create();
    $brand = Brand::query()->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.brands.index'))
        ->assertOk()
        ->assertSee($brand->name);

    $this->actingAs($admin)
        ->patch(route('admin.brands.toggle', $brand), ['is_active' => '0'])
        ->assertRedirect();

    expect($brand->fresh()->is_active)->toBeFalse();
});

it('blocks non-admins from catalog management', function () {
    $customer = User::factory()->activeCustomer()->create();
    $category = Category::query()->firstOrFail();

    $this->actingAs($customer)
        ->get(route('admin.categories.index'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->patch(route('admin.categories.toggle', $category), ['is_active' => '0'])
        ->assertForbidden();
});
