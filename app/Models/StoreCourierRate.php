<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreCourierRate extends Model
{
    protected $fillable = [
        'area_code',
        'area_name',
        'rate_amount',
        'eta_text',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
