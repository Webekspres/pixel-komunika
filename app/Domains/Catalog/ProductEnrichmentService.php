<?php

namespace App\Domains\Catalog;

use App\Models\Product;
use App\Models\ProductEnrichment;
use App\Models\ProductMedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductEnrichmentService
{
    public function upsertEnrichment(Product $product, array $data): ProductEnrichment
    {
        $payload = collect($data)->only([
            'display_name',
            'slug',
            'short_description',
            'description',
            'seo_title',
            'seo_description',
            'label',
            'display_order',
            'is_visible',
        ])->all();

        if (empty($payload['slug'])) {
            $payload['slug'] = Str::slug($payload['display_name'] ?? $product->name).'-'.$product->id;
        }

        return $product->enrichment()->updateOrCreate([], $payload);
    }

    public function addMedia(Product $product, array $data): ProductMedia
    {
        return DB::transaction(function () use ($product, $data) {
            if (! empty($data['is_primary'])) {
                $product->media()->update(['is_primary' => false]);
            }

            return $product->media()->create([
                'media_type' => $data['media_type'] ?? ProductMedia::IMAGE,
                'object_key' => $data['object_key'],
                'alt_text' => $data['alt_text'] ?? null,
                'mime_type' => $data['mime_type'],
                'file_size' => $data['file_size'],
                'sort_order' => $data['sort_order'] ?? 0,
                'is_primary' => (bool) ($data['is_primary'] ?? false),
            ]);
        });
    }

    public function removeMedia(ProductMedia $media): void
    {
        $media->delete();
    }
}
