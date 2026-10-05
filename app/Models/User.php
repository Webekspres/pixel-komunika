<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['role_id', 'name', 'email', 'phone', 'password', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /** 0812-3456-7890, +6281234567890, 6281234567890 -> 081234567890 (FR-AUTH-001: nomor unik). */
    public static function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        return str_starts_with($digits, '62') ? '0'.substr($digits, 2) : $digits;
    }

    public function isAdmin(): bool
    {
        return $this->role?->code === Role::ADMIN;
    }

    public function isCustomer(): bool
    {
        return $this->role?->code === Role::CUSTOMER;
    }

    public function customerStatus(): ?string
    {
        return $this->customerProfile?->verification_status;
    }

    public function isActiveCustomer(): bool
    {
        return $this->isCustomer()
            && $this->customerStatus() === CustomerProfile::ACTIVE;
    }

    public function canViewPrices(): bool
    {
        return $this->isAdmin() || $this->isActiveCustomer();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
