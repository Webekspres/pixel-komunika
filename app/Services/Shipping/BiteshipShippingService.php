<?php

namespace App\Services\Shipping;

use App\Models\Shipment;
use App\Models\StoreCourierRate;
use App\Models\StoreProfile;
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
        $this->originAreaId = $this->resolveOriginAreaId();
        $this->timeout = config('biteship.timeout', 5);
    }

    protected function resolveOriginAreaId(): string
    {
        try {
            $storeOrigin = StoreProfile::active()?->origin_biteship_area_id;
            if (! empty($storeOrigin)) {
                return $storeOrigin;
            }
        } catch (\Throwable $e) {
            // fallback to env when DB not ready (migrations, testing)
        }

        return config('biteship.origin_area_id') ?? '';
    }

    public function calculateRates(string $destinationCity, int $weightGrams, ?string $destinationDistrict = null): array
    {
        $storeRates = $this->getStoreCourierRates($destinationCity, $destinationDistrict);

        if (empty($this->apiKey) || empty($this->originAreaId)) {
            Log::warning('Biteship API key or origin_area_id not configured');

            return $storeRates;
        }

        try {
            $destinationAreaId = $this->resolveDestinationAreaId($destinationCity, $destinationDistrict);
            if (! $destinationAreaId) {
                Log::warning("Biteship: no area found for city: {$destinationCity}");

                return $storeRates;
            }

            $rates = $this->fetchRates($destinationAreaId, $weightGrams);
            $biteshipRates = $this->mapRates($rates);

            if (empty($biteshipRates)) {
                return $storeRates;
            }

            return array_merge($storeRates, $biteshipRates);
        } catch (\Throwable $e) {
            Log::error('Biteship calculateRates failed: '.$e->getMessage(), [
                'city' => $destinationCity,
                'weight' => $weightGrams,
                'trace' => $e->getTraceAsString(),
            ]);

            return $storeRates;
        }
    }

    protected function getStoreCourierRates(string $destinationCity, ?string $destinationDistrict = null): array
    {
        $city = mb_strtolower(trim($destinationCity));
        $district = $destinationDistrict ? trim($destinationDistrict) : null;

        // If district supplied, match any district regardless of city (supports Karawang etc).
        // If no district, only show store courier for Bandung (legacy fallback).
        if ($district) {
            // proceed to filter by district
        } elseif (! str_contains($city, 'bandung')) {
            return [];
        }

        try {
            $query = StoreCourierRate::query()->where('is_active', true)->orderBy('rate_amount');

            if ($district) {
                $normalizedDistrict = mb_strtolower($district);
                $normalizedCity = mb_strtolower(trim($destinationCity));

                $filtered = $query->get()->filter(function ($rate) use ($normalizedDistrict, $normalizedCity) {
                    $area = mb_strtolower(trim($rate->area_name));
                    $code = $rate->area_code ? mb_strtolower(trim($rate->area_code)) : null;

                    return $area === $normalizedDistrict
                        || $area === $normalizedCity
                        || ($code && ($code === $normalizedDistrict || $code === $normalizedCity));
                })->values();

                $storeRates = $filtered;
            } else {
                $storeRates = $query->get();
            }

            $rates = [];
            foreach ($storeRates as $rate) {
                $rates[] = [
                    'provider' => Shipment::PROVIDER_STORE,
                    'code' => 'store',
                    'service' => 'Kurir Toko',
                    'name' => 'Kurir Toko, '.$rate->area_name,
                    'cost' => (float) $rate->rate_amount,
                    'etd' => $rate->eta_text ?? 'H+1 hari kerja',
                    'store_courier_rate_id' => $rate->id,
                ];
            }

            if (empty($rates) && empty($district)) {
                $hasAny = StoreCourierRate::query()->where('is_active', true)->exists();
                if (! $hasAny) {
                    $rates[] = [
                        'provider' => Shipment::PROVIDER_STORE,
                        'code' => 'store',
                        'service' => 'Kurir Toko',
                        'name' => 'Kurir Toko Bandung',
                        'cost' => 10000.0,
                        'etd' => 'H+1 hari kerja',
                    ];
                }
            }

            return $rates;
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function resolveDestinationAreaId(string $city, ?string $district = null): ?string
    {
        // Cari sampai kecamatan bila ada; nama kota saja terlalu kasar untuk tarif.
        $input = trim(($district ? "{$district}, " : '').$city);
        $cacheKey = 'biteship_area_'.md5(strtolower($input));

        return Cache::remember($cacheKey, 86400, function () use ($input) {
            try {
                $response = Http::withToken($this->apiKey)
                    ->timeout($this->timeout)
                    ->get("{$this->baseUrl}/v1/maps/areas", [
                        'countries' => 'ID',
                        'input' => $input,
                        'type' => 'single',
                    ]);

                if (! $response->successful()) {
                    Log::warning('Biteship maps/areas failed', [
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ]);

                    return null;
                }

                // Skema Biteship: areas[].id (sama dengan BiteshipAreaSearchService).
                return $response->json('areas.0.id');
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
                    'couriers' => 'jne,jnt,pos,sicepat,anteraja,ninja,lion,ide,grab,gojek',
                    'items' => [[
                        'name' => 'Paket Pixel Komunika',
                        'value' => 0,
                        'weight' => max(100, $weightGrams),
                        'quantity' => 1,
                    ]],
                ]);

            if (! $response->successful()) {
                Log::warning('Biteship rates/couriers failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return [];
            }

            $data = $response->json();

            return $data['pricing'] ?? [];
        } catch (\Throwable $e) {
            Log::error('Biteship rates/couriers exception: '.$e->getMessage());

            return [];
        }
    }

    protected function mapRates(array $biteshipRates): array
    {
        $mapped = [];

        foreach ($biteshipRates as $rate) {
            // Skema Biteship: pricing[] dengan courier_code + courier_service_code.
            $courierCode = strtolower($rate['courier_code'] ?? '');
            $serviceCode = strtolower($rate['courier_service_code'] ?? '');
            $cost = (float) ($rate['price'] ?? 0);
            $duration = $rate['shipment_duration_range'] ?? $rate['duration'] ?? '';

            if ($cost <= 0 || $courierCode === '' || $serviceCode === '') {
                continue;
            }

            $mapped[] = [
                'provider' => Shipment::PROVIDER_BITESHIP,
                // Kunci pilihan = code:service, jadi kurir harus ikut agar JNE REG != SiCepat REG.
                'code' => $courierCode,
                'service' => strtoupper($serviceCode),
                'name' => trim(($rate['courier_name'] ?? strtoupper($courierCode)).' '.($rate['courier_service_name'] ?? '')),
                'cost' => $cost,
                'etd' => $duration ? str_replace(' days', '', $duration).' hari' : 'N/A',
            ];
        }

        return $mapped;
    }
}
