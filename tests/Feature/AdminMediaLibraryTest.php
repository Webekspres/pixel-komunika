<?php

use App\Domains\Catalog\MediaLibraryService;
use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Livewire\Admin\MediaLibrary;
use App\Models\MediaLibrary as MediaLibraryModel;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    app(SampleCatalogImporter::class)->import();
});

it('opens the media library for admins only', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.media.index'))
        ->assertOk()
        ->assertSee('Media Library');

    $customer = User::factory()->activeCustomer()->create();

    $this->actingAs($customer)
        ->get(route('admin.media.index'))
        ->assertForbidden();
});

it('uploads media to the library', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(MediaLibrary::class)
        ->set('uploadFile', UploadedFile::fake()->image('produk.jpg'));

    $media = MediaLibraryModel::query()->first();

    expect($media)->not->toBeNull()
        ->and($media->original_name)->toBe('produk.jpg')
        ->and($media->url())->toContain('/storage/product-media/')
        ->and(Storage::disk('public')->exists($media->object_key))->toBeTrue();
});

it('searches media by filename', function () {
    Storage::fake('public');
    $service = app(MediaLibraryService::class);
    $admin = User::factory()->admin()->create();
    $service->upload(UploadedFile::fake()->image('kabel.jpg'));
    $service->upload(UploadedFile::fake()->image('adaptor.jpg'));

    Livewire::actingAs($admin)
        ->test(MediaLibrary::class)
        ->set('search', 'kabel')
        ->assertSee('kabel.jpg')
        ->assertDontSee('adaptor.jpg');
});

it('shows usage count and blocks delete of used media', function () {
    Storage::fake('public');
    $service = app(MediaLibraryService::class);
    $admin = User::factory()->admin()->create();
    $product = Product::query()->firstOrFail();
    $media = $service->upload(UploadedFile::fake()->image('dipakai.jpg'));
    $service->attachToProduct($product, $media);

    Livewire::actingAs($admin)
        ->test(MediaLibrary::class)
        ->assertSee('1 produk')
        ->call('delete', $media->id)
        ->assertSee('sedang dipakai');

    expect(MediaLibraryModel::query()->count())->toBe(1)
        ->and(Storage::disk('public')->exists($media->object_key))->toBeTrue();
});

it('deletes unused media', function () {
    Storage::fake('public');
    $service = app(MediaLibraryService::class);
    $admin = User::factory()->admin()->create();
    $media = $service->upload(UploadedFile::fake()->image('bekas.jpg'));

    Livewire::actingAs($admin)
        ->test(MediaLibrary::class)
        ->call('delete', $media->id);

    expect(MediaLibraryModel::query()->count())->toBe(0)
        ->and(Storage::disk('public')->exists($media->object_key))->toBeFalse();
});
