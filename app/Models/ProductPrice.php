<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    public const RETAIL = 'ECERAN';

    public const BULK = 'PARTAI';

    public const WHOLESALE = 'GROSIR';

    public const WHOLESALE_2 = 'GROSIR_2';

    public const LABELS = [
        self::RETAIL => 'Eceran',
        self::BULK => 'Partai',
        self::WHOLESALE => 'Grosir 1',
        self::WHOLESALE_2 => 'Grosir 2',
    ];

    protected $fillable = [
        'product_id',
        'price_type',
        'amount',
        'minimum_quantity',
        'pos_price_id',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'synced_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
