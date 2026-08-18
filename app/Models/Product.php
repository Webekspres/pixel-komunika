<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'pos_product_id',
        'sku',
        'name',
        'weight_grams',
        'length_cm',
        'width_cm',
        'height_cm',
        'is_active',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function enrichment(): HasOne
    {
        return $this->hasOne(ProductEnrichment::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function latestPrice(): HasOne
    {
        return $this->hasOne(ProductPrice::class)->latestOfMany();
    }

    public function inventorySnapshot(): HasOne
    {
        return $this->hasOne(InventorySnapshot::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('sort_order');
    }

    /**
     * Nama yang ditampilkan di storefront: nama tampilan website bila di-set,
     * fallback ke nama dasar POS (FR-CAT-004/005).
     */
    public function displayName(): string
    {
        $enrichment = $this->relationLoaded('enrichment')
            ? $this->enrichment
            : $this->enrichment()->first();

        return $enrichment?->display_name ?: $this->name;
    }

    public function listPriceAmount(): ?float
    {
        $prices = $this->relationLoaded('prices')
            ? $this->prices
            : $this->prices()->get();

        $byType = $prices->keyBy('price_type');

        $grosir = $byType->get(ProductPrice::WHOLESALE);
        if ($grosir) {
            return (float) $grosir->amount;
        }

        $partai = $byType->get(ProductPrice::BULK);

        return $partai ? (float) $partai->amount : null;
    }

    public function partaiPriceAmount(): ?float
    {
        $prices = $this->relationLoaded('prices')
            ? $this->prices
            : $this->prices()->get();

        $partai = $prices->firstWhere('price_type', ProductPrice::BULK);

        return $partai ? (float) $partai->amount : null;
    }

    public function grosirMinimumQuantity(): ?int
    {
        $prices = $this->relationLoaded('prices')
            ? $this->prices
            : $this->prices()->get();

        $grosir = $prices->firstWhere('price_type', ProductPrice::WHOLESALE);

        return $grosir?->minimum_quantity;
    }
}
