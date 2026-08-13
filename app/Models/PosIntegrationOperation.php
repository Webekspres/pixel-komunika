<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosIntegrationOperation extends Model
{
    public const WEB_SALE_REPORT = 'WEB_SALE_REPORT';

    public const WEB_RETURN_REPORT = 'WEB_RETURN_REPORT';

    public const PENDING = 'PENDING';

    public const SUCCEEDED = 'SUCCEEDED';

    public const FAILED = 'FAILED';

    public const AMBIGUOUS = 'AMBIGUOUS';

    public const RECONCILIATION_REQUIRED = 'RECONCILIATION_REQUIRED';

    protected $fillable = [
        'order_id',
        'sync_run_id',
        'operation',
        'external_reference',
        'request_hash',
        'status',
        'attempt_count',
        'correlation_id',
        'response_reference',
        'request_payload_redacted',
        'response_payload_redacted',
        'error_code',
        'error_message',
        'last_attempt_at',
        'reconciled_at',
    ];

    protected function casts(): array
    {
        return [
            'request_payload_redacted' => 'array',
            'response_payload_redacted' => 'array',
            'last_attempt_at' => 'datetime',
            'reconciled_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function syncRun(): BelongsTo
    {
        return $this->belongsTo(SyncRun::class);
    }
}
