<?php

use App\Domains\Catalog\MediaLibraryService;
use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Livewire\Admin\ProductMediaManager;
use App\Models\MediaLibrary as MediaLibraryModel;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

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

it('manages product media via the media manager', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();
    $product = Product::query()->firstOrFail();

    Livewire::actingAs($admin)
        ->test(ProductMediaManager::class, ['product' => $product])
        ->set('uploadFile', UploadedFile::fake()->image('foto.jpg'))
        ->assertDispatched('media-uploaded');

    $usage = $product->media()->with('library')->first();

    expect($usage)->not->toBeNull()
        ->and($usage->library)->not->toBeNull()
        ->and(Storage::disk('public')->exists($usage->library->object_key))->toBeTrue()
        ->and($usage->library->url())->toContain('/storage/product-media/');

    Livewire::actingAs($admin)
        ->test(ProductMediaManager::class, ['product' => $product])
        ->call('setPrimary', $usage->id);

    expect($usage->fresh()->is_primary)->toBeTrue();

    Livewire::actingAs($admin)
        ->test(ProductMediaManager::class, ['product' => $product])
        ->call('detach', $usage->id);

    expect($product->media()->count())->toBe(0)
        ->and(MediaLibraryModel::query()->count())->toBe(1);
});

it('attaches existing library media to a product', function () {
    Storage::fake('public');
    $service = app(MediaLibraryService::class);
    $admin = User::factory()->admin()->create();
    $product = Product::query()->firstOrFail();
    $mediaA = $service->upload(UploadedFile::fake()->image('a.jpg'));
    $mediaB = $service->upload(UploadedFile::fake()->image('b.jpg'));

    Livewire::actingAs($admin)
        ->test(ProductMediaManager::class, ['product' => $product])
        ->set('pickerOpen', true)
        ->set('selected', [$mediaA->id, $mediaB->id])
        ->call('attachSelected')
        ->assertDispatched('media-attached');

    expect($product->media()->count())->toBe(2);
});

it('reflects picker selection immediately and resets on close', function () {
    Storage::fake('public');
    $service = app(MediaLibraryService::class);
    $admin = User::factory()->admin()->create();
    $product = Product::query()->firstOrFail();
    $mediaA = $service->upload(UploadedFile::fake()->image('a.jpg'));

    Livewire::actingAs($admin)
        ->test(ProductMediaManager::class, ['product' => $product])
        ->set('pickerOpen', true)
        ->set('selected', [$mediaA->id])
        ->assertSee('1 terpilih')
        ->assertSee('Pilih (1)')
        ->call('closePicker')
        ->assertSet('pickerOpen', false)
        ->assertSet('selected', []);
});

it('exposes uploaded media to the storefront via public url', function () {
    Storage::fake('public');
    $service = app(MediaLibraryService::class);
    $product = Product::query()->firstOrFail();
    $product->enrichment()->update(['is_visible' => true]);
    $media = $service->upload(UploadedFile::fake()->image('foto.jpg'));
    $service->attachToProduct($product, $media, primary: true);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee($media->url(), false);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee($media->url(), false);
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
