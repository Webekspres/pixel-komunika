<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreProfile extends Model
{
    protected $fillable = [
        'store_name',
        'address',
        'contact_number',
        'company_name',
        'company_npwp',
        'partai_minimum_quantity',
        'origin_biteship_area_id',
        'origin_postal_code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'partai_minimum_quantity' => 'integer',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public static function active(): ?self
    {
        return static::query()->where('is_active', true)->latest('id')->first();
    }
}
