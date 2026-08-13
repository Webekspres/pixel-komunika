<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReturn extends Model
{
    public const PENDING = 'PENDING';

    public const SUCCEEDED = 'SUCCEEDED';

    public const FAILED = 'FAILED';

    public const RECONCILIATION_REQUIRED = 'RECONCILIATION_REQUIRED';

    protected $fillable = [
        'order_id',
        'return_number',
        'reason',
        'reporting_status',
        'pos_ack_reference',
        'returned_at',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'returned_at' => 'datetime',
            'reported_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
