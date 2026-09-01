<?php

namespace App\Services\Shipping;

interface ShippingCalculatorInterface
{
    /**
     * Calculate available shipping rates for a destination and total weight.
     *
     * @param  string|null  $destinationDistrict  Optional district/kecamatan for filtering internal fleet
     * @return array Array of shipping options e.g. [['code' => 'jne', 'service' => 'REG', 'name' => 'JNE Reguler', 'cost' => 15000, 'etd' => '2-3 Hari']]
     */
    public function calculateRates(string $destinationCity, int $weightGrams, ?string $destinationDistrict = null): array;
}
