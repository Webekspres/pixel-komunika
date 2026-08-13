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
        'calculation_basis',
        'is_active',
        'effective_from',
        'effective_until',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'threshold_amount' => 'decimal:2',
            'rate_percent' => 'decimal:4',
            'is_active' => 'boolean',
            'effective_from' => 'datetime',
            'effective_until' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
