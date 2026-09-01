<?php

namespace App\Services\Shipping;

use App\Models\Shipment;

class StoreCourierFreeShipping
{
    public static function threshold(): float
    {
        return (float) config('store.shipping.free_store_courier_threshold', 1_000_000);
    }

    public static function qualifies(float $subtotal, float $pph22): bool
    {
        return ($subtotal + $pph22) >= self::threshold();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rates
     * @return array<int, array<string, mixed>>
     */
    public static function applyToRates(array $rates, float $subtotal, float $pph22): array
    {
        if (! self::qualifies($subtotal, $pph22)) {
            return $rates;
        }

        return array_map(static function (array $rate): array {
            if (($rate['provider'] ?? null) === Shipment::PROVIDER_STORE) {
                $rate['cost'] = 0.0;
            }

            return $rate;
        }, $rates);
    }

    /**
     * @param  array<string, mixed>  $option
     * @return array<string, mixed>
     */
    public static function applyToOption(array $option, float $subtotal, float $pph22): array
    {
        if (
            self::qualifies($subtotal, $pph22)
            && ($option['provider'] ?? null) === Shipment::PROVIDER_STORE
        ) {
            $option['cost'] = 0.0;
        }

        return $option;
    }
}
