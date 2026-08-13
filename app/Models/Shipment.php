<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    public const PROVIDER_STORE = 'STORE_COURIER';

    public const PROVIDER_BITESHIP = 'BITESHIP';

    public const ISSUE_NONE = 'NONE';

    public const ISSUE_TERKENDALA = 'TERKENDALA';

    protected $fillable = [
        'order_id',
        'store_courier_rate_id',
        'rate_provider',
        'courier_code',
        'courier_name_snapshot',
        'service_code',
        'service_name_snapshot',
        'eta_snapshot',
        'origin_biteship_area_id_snapshot',
        'destination_biteship_area_id_snapshot',
        'recipient_name_snapshot',
        'recipient_phone_snapshot',
        'address_snapshot',
        'province_snapshot',
        'city_snapshot',
        'district_snapshot',
        'postal_code_snapshot',
        'currency',
        'shipping_amount',
        'rate_request_hash',
        'quoted_at',
        'tracking_number',
        'shipment_group_code',
        'status',
        'issue_status',
        'issue_reason',
        'issue_reported_at',
        'issue_resolved_at',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_amount' => 'decimal:2',
            'quoted_at' => 'datetime',
            'issue_reported_at' => 'datetime',
            'issue_resolved_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function storeCourierRate(): BelongsTo
    {
        return $this->belongsTo(StoreCourierRate::class);
    }

    public function isHeld(): bool
    {
        return $this->issue_status === self::ISSUE_TERKENDALA;
    }
}
