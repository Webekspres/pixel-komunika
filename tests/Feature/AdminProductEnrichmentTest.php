<?php

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('lists products with search for admins', function () {
    $admin = User::factory()->admin()->create();
    $product = Product::query()->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.products.index'))
        ->assertOk()
        ->assertSee($product->sku)
        ->assertSee('Kelola presentasi');

    $this->actingAs($admin)
        ->get(route('admin.products.index', ['q' => $product->sku]))
        ->assertOk()
        ->assertSee($product->sku);
});

it('updates product enrichment presentation', function () {
    $admin = User::factory()->admin()->create();
    $product = Product::query()->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.products.edit', $product))
        ->assertOk()
        ->assertSee('Presentasi storefront')
        ->assertSee($product->sku);

    $this->actingAs($admin)
        ->patch(route('admin.products.update', $product), [
            'display_name' => 'Power Bank Premium',
            'slug' => 'power-bank-premium',
            'short_description' => 'Deskripsi singkat baru',
            'description' => 'Deskripsi lengkap baru',
            'display_order' => '3',
            'is_visible' => '0',
        ])
        ->assertRedirect(route('admin.products.edit', $product));

    $enrichment = $product->enrichment->fresh();

    expect($enrichment->display_name)->toBe('Power Bank Premium')
        ->and($enrichment->slug)->toBe('power-bank-premium')
        ->and($enrichment->short_description)->toBe('Deskripsi singkat baru')
        ->and($enrichment->display_order)->toBe(3)
        ->and($enrichment->is_visible)->toBeFalse();
});

it('manages product media upload and removal', function () {
    Storage::fake('local');
    $admin = User::factory()->admin()->create();
    $product = Product::query()->firstOrFail();

    $this->actingAs($admin)
        ->post(route('admin.products.media.store', $product), [
            'image' => UploadedFile::fake()->image('foto.jpg'),
            'is_primary' => '1',
        ])
        ->assertRedirect();

    $media = $product->media()->first();

    expect($media)->not->toBeNull()
        ->and($media->is_primary)->toBeTrue()
        ->and(Storage::disk('local')->exists($media->object_key))->toBeTrue();

    $this->actingAs($admin)
        ->get(route('admin.products.media.show', $media))
        ->assertOk();

    $this->actingAs($admin)
        ->delete(route('admin.products.media.destroy', $media))
        ->assertRedirect();

    expect($product->media()->count())->toBe(0);
});

it('blocks non-admins from product enrichment', function () {
    $customer = User::factory()->activeCustomer()->create();
    $product = Product::query()->firstOrFail();

    $this->actingAs($customer)
        ->get(route('admin.products.index'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->patch(route('admin.products.update', $product), ['display_name' => 'X'])
        ->assertForbidden();
});
