<?php

namespace App\Services\Shipping;

use App\Models\Shipment;
use App\Models\StoreCourierRate;
use Illuminate\Support\Collection;

class MockBiteshipShippingService implements ShippingCalculatorInterface
{
    public function calculateRates(string $destinationCity, int $weightGrams, ?string $destinationDistrict = null): array
    {
        $weightKg = max(1, (int) ceil($weightGrams / 1000));
        $city = strtolower($destinationCity);

        $rates = [];

        // Store courier — filtered by district/kecamatan (any city). Fallback Bandung when no district.
        $storeRates = collect();
        if (! empty($destinationDistrict)) {
            $storeRates = $this->getFilteredStoreRates($destinationCity, $destinationDistrict);
        } elseif (str_contains($city, 'bandung')) {
            $storeRates = $this->getFilteredStoreRates($destinationCity, $destinationDistrict);
        }

        foreach ($storeRates as $rate) {
            $rates[] = [
                'provider' => Shipment::PROVIDER_STORE,
                'code' => 'store',
                'service' => 'Kurir Toko',
                'name' => 'Kurir Toko — '.$rate->area_name,
                'cost' => (float) $rate->rate_amount,
                'etd' => $rate->eta_text ?? 'H+1 hari kerja',
                'store_courier_rate_id' => $rate->id,
            ];
        }

        if ($storeRates->isEmpty() && empty($destinationDistrict) && ! StoreCourierRate::query()->where('is_active', true)->exists()) {
            $rates[] = [
                'provider' => Shipment::PROVIDER_STORE,
                'code' => 'store',
                'service' => 'Kurir Toko',
                'name' => 'Kurir Toko Bandung',
                'cost' => 10000.0,
                'etd' => 'H+1 hari kerja',
            ];
        }

        $baseRate = 12000;
        if (str_contains($city, 'jakarta') || str_contains($city, 'tangerang')) {
            $baseRate = 9000;
        } elseif (str_contains($city, 'surabaya') || str_contains($city, 'medan')) {
            $baseRate = 22000;
        }

        $rates = array_merge($rates, [
            [
                'provider' => Shipment::PROVIDER_BITESHIP,
                'code' => 'grab',
                'service' => 'same_day',
                'name' => 'Grab Same Day',
                'cost' => ($baseRate + 8000) * $weightKg,
                'etd' => 'Same day',
            ],
            [
                'provider' => Shipment::PROVIDER_BITESHIP,
                'code' => 'gojek',
                'service' => 'same_day',
                'name' => 'Gojek Same Day',
                'cost' => ($baseRate + 7000) * $weightKg,
                'etd' => 'Same day',
            ],
            [
                'provider' => Shipment::PROVIDER_BITESHIP,
                'code' => 'jne',
                'service' => 'REG',
                'name' => 'JNE Reguler',
                'cost' => $baseRate * $weightKg,
                'etd' => '2-3 Hari',
            ],
        ]);

        return $rates;
    }

    protected function getFilteredStoreRates(string $city, ?string $district): Collection
    {
        $query = StoreCourierRate::query()->where('is_active', true)->orderBy('rate_amount');

        $district = $district ? trim($district) : null;

        if ($district) {
            $normalizedDistrict = mb_strtolower($district);
            $normalizedCity = mb_strtolower(trim($city));

            return $query->get()->filter(function ($rate) use ($normalizedDistrict, $normalizedCity) {
                $area = mb_strtolower(trim($rate->area_name));
                $code = $rate->area_code ? mb_strtolower(trim($rate->area_code)) : null;

                // Exact match on district/kecamatan or city or area_code
                return $area === $normalizedDistrict
                    || $area === $normalizedCity
                    || ($code && ($code === $normalizedDistrict || $code === $normalizedCity));
            })->values();
        }

        return $query->get();
    }
}
