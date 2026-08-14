<?php

namespace App\Domains\Catalog;

use App\Models\Product;
use App\Models\ProductEnrichment;
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
}
