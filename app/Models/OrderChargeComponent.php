<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderChargeComponent extends Model
{
    public const PPH22 = 'PPH22';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'category_tax_rule_id',
        'component_code',
        'label_snapshot',
        'basis_amount',
        'divisor',
        'rate_percent',
        'amount',
        'config_snapshot',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'basis_amount' => 'decimal:2',
            'divisor' => 'decimal:4',
            'rate_percent' => 'decimal:4',
            'amount' => 'decimal:2',
            'config_snapshot' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function categoryTaxRule(): BelongsTo
    {
        return $this->belongsTo(CategoryTaxRule::class);
    }
}
