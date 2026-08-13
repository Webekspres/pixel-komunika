<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    public const NOT_SUBMITTED = 'NOT_SUBMITTED';

    public const SUBMITTED = 'SUBMITTED';

    public const VERIFIED = 'VERIFIED';

    public const REJECTED = 'REJECTED';

    protected $fillable = [
        'order_id',
        'bank_account_id',
        'status',
        'amount',
        'submitted_at',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function proofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }
}
