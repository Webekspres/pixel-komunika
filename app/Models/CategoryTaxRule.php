<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryTaxRule extends Model
{
    protected $fillable = [
        'category_id',
        'threshold_amount',
        'rate_percent',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'threshold_amount' => 'decimal:2',
            'rate_percent' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
