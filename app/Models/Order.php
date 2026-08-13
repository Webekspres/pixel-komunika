<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    protected $fillable = [
        'order_number',
        'idempotency_key',
        'user_id',
        'address_id',
        'status',
        'order_date_local',
        'recipient_name',
        'recipient_phone',
        'shipping_address_line',
        'shipping_province',
        'shipping_city',
        'shipping_district',
        'shipping_postal_code',
        'courier_code',
        'courier_service',
        'shipping_cost',
        'subtotal',
        'tax_pph22',
        'tax_pph22_snapshot',
        'grand_total',
        'expires_at',
        'cancellation_source',
        'cancelled_by_user_id',
        'cancellation_reason',
        'cancelled_at',
        'completion_source',
        'receipt_confirmed_at',
        'receipt_token_hash',
        'receipt_token_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_pph22' => 'decimal:2',
            'tax_pph22_snapshot' => 'array',
            'grand_total' => 'decimal:2',
            'expires_at' => 'datetime',
            'order_date_local' => 'date',
            'cancelled_at' => 'datetime',
            'receipt_confirmed_at' => 'datetime',
            'receipt_token_expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function paymentProofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }

    public function latestPaymentProof(): HasOne
    {
        return $this->hasOne(PaymentProof::class)->latestOfMany();
    }

    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function chargeComponents(): HasMany
    {
        return $this->hasMany(OrderChargeComponent::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function salesReturn(): HasOne
    {
        return $this->hasOne(SalesReturn::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AppNotification::class, 'order_id');
    }
}
