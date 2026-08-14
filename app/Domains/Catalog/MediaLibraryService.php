<?php

namespace App\Domains\Catalog;

use App\Models\MediaLibrary;
use App\Models\Product;
use App\Models\ProductMedia;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaLibraryService
{
    public function upload(UploadedFile $file): MediaLibrary
    {
        $path = $file->store('product-media', 'public');

        $dims = null;
        try {
            $dims = @getimagesize($file->getRealPath());
        } catch (\Throwable) {
            // Bukan gambar nyata (mis. fake upload di test).
        }

        return MediaLibrary::query()->create([
            'object_key' => $path,
            'original_name' => $file->getClientOriginalName(),
            'media_type' => MediaLibrary::IMAGE,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'width' => $dims[0] ?? null,
            'height' => $dims[1] ?? null,
        ]);
    }

    public function attachToProduct(Product $product, MediaLibrary $media, bool $primary = false, ?string $altText = null): ProductMedia
    {
        $usage = ProductMedia::query()->firstOrNew([
            'product_id' => $product->id,
            'media_id' => $media->id,
        ]);

        if (! $usage->exists) {
            $usage->sort_order = (int) ProductMedia::query()->where('product_id', $product->id)->max('sort_order') + 1;
            $usage->alt_text = $altText ?? $product->name;
        }

        if ($primary) {
            $product->media()->update(['is_primary' => false]);
            $usage->is_primary = true;
        }

        $usage->save();

        return $usage;
    }

    public function detachFromProduct(ProductMedia $usage): void
    {
        $productId = $usage->product_id;
        $wasPrimary = $usage->is_primary;
        $usage->delete();

        if ($wasPrimary) {
            $next = ProductMedia::query()
                ->where('product_id', $productId)
                ->orderBy('sort_order')
                ->first();

            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }
    }

    public function deleteLibraryItem(MediaLibrary $media): void
    {
        throw_if($media->productUsages()->exists(), DomainException::class, 'Media sedang dipakai produk, tidak dapat dihapus.');

        Storage::disk('public')->delete($media->object_key);
        $media->delete();
    }
}
