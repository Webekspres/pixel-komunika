<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'order_id',
        'user_id',
        'store_name',
        'store_address',
        'store_phone',
        'store_npwp',
        'subtotal',
        'tax_pph22',
        'shipping_cost',
        'amount',
        'status',
        'due_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_pph22' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'amount' => 'decimal:2',
            'due_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
