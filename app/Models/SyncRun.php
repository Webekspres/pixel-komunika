<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SyncRun extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sync_type',
        'source',
        'status',
        'started_at',
        'finished_at',
        'success_count',
        'failed_count',
        'correlation_id',
        'summary',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'summary' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function errors(): HasMany
    {
        return $this->hasMany(SyncError::class);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(PosIntegrationOperation::class);
    }
}
