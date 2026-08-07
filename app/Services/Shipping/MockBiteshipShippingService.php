<?php

namespace App\Services\Shipping;

class MockBiteshipShippingService implements ShippingCalculatorInterface
{
    public function calculateRates(string $destinationCity, int $weightGrams): array
    {
        $weightKg = max(1, (int) ceil($weightGrams / 1000));
        
        // Base rate based on destination
        $baseRate = 12000;
        if (str_contains(strtolower($destinationCity), 'jakarta') || str_contains(strtolower($destinationCity), 'tangerang')) {
            $baseRate = 9000;
        } elseif (str_contains(strtolower($destinationCity), 'surabaya') || str_contains(strtolower($destinationCity), 'medan')) {
            $baseRate = 22000;
        }

        return [
            [
                'code' => 'jne',
                'service' => 'REG',
                'name' => 'JNE Reguler',
                'cost' => $baseRate * $weightKg,
                'etd' => '2-3 Hari',
            ],
            [
                'code' => 'jnt',
                'service' => 'EZ',
                'name' => 'J&T EZ',
                'cost' => ($baseRate + 2000) * $weightKg,
                'etd' => '1-2 Hari',
            ],
            [
                'code' => 'sicepat',
                'service' => 'BEST',
                'name' => 'SiCepat BEST',
                'cost' => ($baseRate + 5000) * $weightKg,
                'etd' => '1 Hari',
            ],
        ];
    }
}
