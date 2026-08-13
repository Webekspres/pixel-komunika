<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    protected $table = 'notifications';

    public const CHANNEL_DATABASE = 'DATABASE';

    public const CHANNEL_WHATSAPP = 'WHATSAPP';

    public const TYPE_NEW_ORDER = 'NEW_ORDER';

    public const TYPE_RECEIPT_CONFIRMATION = 'RECEIPT_CONFIRMATION';

    public const PENDING = 'PENDING';

    public const SENT = 'SENT';

    public const FAILED = 'FAILED';

    protected $fillable = [
        'user_id',
        'order_id',
        'channel',
        'type',
        'data',
        'status',
        'external_message_id',
        'read_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
