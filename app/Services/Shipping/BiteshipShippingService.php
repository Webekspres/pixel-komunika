<?php

namespace App\Services\Shipping;

use App\Models\Shipment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipShippingService implements ShippingCalculatorInterface
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $originAreaId;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('biteship.base_url'), '/');
        $this->apiKey = config('biteship.api_key');
        $this->originAreaId = config('biteship.origin_area_id') ?? '';
        $this->timeout = config('biteship.timeout', 5);
    }

    public function calculateRates(string $destinationCity, int $weightGrams): array
    {
        if (empty($this->apiKey) || empty($this->originAreaId)) {
            Log::warning('Biteship API key or origin_area_id not configured');
            return [];
        }

        try {
            $destinationAreaId = $this->resolveDestinationAreaId($destinationCity);
            if (! $destinationAreaId) {
                Log::warning("Biteship: no area found for city: {$destinationCity}");
                return [];
            }

            $rates = $this->fetchRates($destinationAreaId, $weightGrams);

            return $this->mapRates($rates);
        } catch (\Throwable $e) {
            Log::error('Biteship calculateRates failed: '.$e->getMessage(), [
                'city' => $destinationCity,
                'weight' => $weightGrams,
                'trace' => $e->getTraceAsString(),
            ]);

            return [];
        }
    }

    protected function resolveDestinationAreaId(string $city): ?string
    {
        $cacheKey = 'biteship_area_'.strtolower(trim($city));

        return Cache::remember($cacheKey, 86400, function () use ($city) {
            try {
                $response = Http::withToken($this->apiKey)
                    ->timeout($this->timeout)
                    ->get("{$this->baseUrl}/v1/maps/areas", [
                        'search' => $city,
                        'limit' => 10,
                    ]);

                if (! $response->successful()) {
                    Log::warning('Biteship maps/areas failed', [
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ]);

                    return null;
                }

                $data = $response->json();
                $areas = $data['areas'] ?? [];

                foreach ($areas as $area) {
                    if (stripos($area['city'] ?? '', $city) !== false) {
                        return $area['area_id'] ?? null;
                    }
                }

                return $areas[0]['area_id'] ?? null;
            } catch (\Throwable $e) {
                Log::error('Biteship maps/areas exception: '.$e->getMessage());
                return null;
            }
        });
    }

    protected function fetchRates(string $destinationAreaId, int $weightGrams): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout($this->timeout)
                ->post("{$this->baseUrl}/v1/rates/couriers", [
                    'origin_area_id' => $this->originAreaId,
                    'destination_area_id' => $destinationAreaId,
                    'weight' => max(100, $weightGrams),
                    'couriers' => ['jne', 'jnt', 'pos', 'sicepat', 'anteraja', 'ninja', 'lion', 'ide', 'grab', 'gojek'],
                ]);

            if (! $response->successful()) {
                Log::warning('Biteship rates/couriers failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return [];
            }

            $data = $response->json();

            return $data['rates'] ?? [];
        } catch (\Throwable $e) {
            Log::error('Biteship rates/couriers exception: '.$e->getMessage());
            return [];
        }
    }

    protected function mapRates(array $biteshipRates): array
    {
        $mapped = [];

        foreach ($biteshipRates as $rate) {
            $courier = $rate['courier_company']['name'] ?? 'Unknown';
            $serviceCode = $rate['courier_type_code'] ?? '';
            $serviceName = $rate['courier_type_name'] ?? '';
            $cost = (float) ($rate['cost'] ?? 0);
            $etd = $rate['etd'] ?? '';

            if ($cost <= 0) {
                continue;
            }

            $mapped[] = [
                'provider' => Shipment::PROVIDER_BITESHIP,
                'code' => strtolower($serviceCode),
                'service' => strtoupper($serviceCode),
                'name' => "{$courier} {$serviceName}",
                'cost' => $cost,
                'etd' => $etd ? "{$etd} hari" : 'N/A',
            ];
        }

        return $mapped;
    }
}