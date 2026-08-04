<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventorySnapshot extends Model
{
    public const AVAILABLE = 'TERSEDIA';
    public const LOW = 'MENIPIS';
    public const OUT = 'HABIS';

    protected $fillable = [
        'product_id',
        'quantity_available',
        'low_stock_threshold',
        'stock_status',
        'source_updated_at',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'source_updated_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(InventoryLedger::class, 'product_id', 'product_id');
    }
}
