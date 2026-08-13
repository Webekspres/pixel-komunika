<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncError extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sync_run_id',
        'entity_type',
        'external_id',
        'error_code',
        'error_message',
        'payload_excerpt_redacted',
        'retryable',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'payload_excerpt_redacted' => 'array',
            'retryable' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function syncRun(): BelongsTo
    {
        return $this->belongsTo(SyncRun::class);
    }
}
