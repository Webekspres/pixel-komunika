<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipAreaSearchService
{
    protected string $baseUrl;

    protected ?string $apiKey;

    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('biteship.base_url', 'https://api.biteship.com'), '/');
        $this->apiKey = config('biteship.api_key');
        $this->timeout = (int) config('biteship.timeout', 5);
    }

    /**
     * Search areas in Indonesia matching the query keyword.
     *
     * @return array<int, array{id: ?string, label: string, province_name: string, city_name: string, district_name: string, postal_code: ?string, biteship_area_id: ?string}>
     */
    public function search(string $query): array
    {
        $trimmed = trim($query);
        if (mb_strlen($trimmed) < 2) {
            return [];
        }

        $cacheKey = 'biteship_area_search_'.md5(mb_strtolower($trimmed));

        return Cache::remember($cacheKey, 86400, function () use ($trimmed) {
            if (! empty($this->apiKey)) {
                $results = $this->fetchFromBiteship($trimmed);
                if (! empty($results)) {
                    return $results;
                }
            }

            return $this->fallbackSearch($trimmed);
        });
    }

    /**
     * Get list of districts for a city, sorted A-Z.
     *
     * @return array<int, string>
     */
    public function getDistrictsForCity(string $city): array
    {
        $trimmedCity = trim($city);
        if (empty($trimmedCity)) {
            return [];
        }

        $cacheKey = 'city_districts_sorted_'.md5(mb_strtolower($trimmedCity));

        return Cache::remember($cacheKey, 86400, function () use ($trimmedCity) {
            $districts = IndonesiaRegionService::getDistrictsByCity($trimmedCity);

            if (! empty($districts)) {
                sort($districts, SORT_NATURAL | SORT_FLAG_CASE);

                return array_values(array_unique($districts));
            }

            if (! empty($this->apiKey)) {
                try {
                    $response = Http::withToken($this->apiKey)
                        ->timeout($this->timeout)
                        ->get("{$this->baseUrl}/v1/maps/areas", [
                            'countries' => 'ID',
                            'input' => $trimmedCity,
                            'type' => 'single',
                        ]);

                    if ($response->successful()) {
                        $areas = $response->json('areas') ?? [];
                        $found = [];
                        foreach ($areas as $area) {
                            $d = $area['administrative_division_level_3_name'] ?? null;
                            if (! empty($d)) {
                                $found[] = $d;
                            }
                        }

                        if (! empty($found)) {
                            sort($found, SORT_NATURAL | SORT_FLAG_CASE);

                            return array_values(array_unique($found));
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed fetching districts from Biteship: '.$e->getMessage());
                }
            }

            return [];
        });
    }

    /**
     * Fetch areas from Biteship Maps API.
     */
    protected function fetchFromBiteship(string $query): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout($this->timeout)
                ->get("{$this->baseUrl}/v1/maps/areas", [
                    'countries' => 'ID',
                    'input' => $query,
                    'type' => 'single',
                ]);

            if (! $response->successful()) {
                Log::warning('Biteship maps/areas search returned non-200 status', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return [];
            }

            $data = $response->json();
            $areas = $data['areas'] ?? [];
            $formatted = [];

            foreach ($areas as $area) {
                $province = $area['administrative_division_level_1_name'] ?? '';
                $city = $area['administrative_division_level_2_name'] ?? '';
                $district = $area['administrative_division_level_3_name'] ?? '';
                $postalCode = isset($area['postal_code']) ? (string) $area['postal_code'] : null;
                $areaId = $area['id'] ?? null;

                if (empty($district) && empty($city)) {
                    continue;
                }

                $parts = array_filter([$district, $city, $province]);
                $label = implode(', ', $parts).($postalCode ? " ({$postalCode})" : '');

                $formatted[] = [
                    'id' => $areaId,
                    'label' => $label,
                    'province_name' => $province,
                    'city_name' => $city,
                    'district_name' => $district,
                    'postal_code' => $postalCode,
                    'biteship_area_id' => $areaId,
                ];
            }

            return $formatted;
        } catch (\Throwable $e) {
            Log::warning('Biteship area search exception: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Curated fallback dataset for Indonesian areas if Biteship API is unconfigured or offline.
     */
    protected function fallbackSearch(string $query): array
    {
        $q = mb_strtolower($query);

        $dataset = [
            // Bandung & Sekitarnya
            ['district' => 'Coblong', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40132'],
            ['district' => 'Sukajadi', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40162'],
            ['district' => 'Sumur Bandung', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40111'],
            ['district' => 'Cicendo', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40171'],
            ['district' => 'Andir', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40181'],
            ['district' => 'Lengkong', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40261'],
            ['district' => 'Astana Anyar', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40241'],
            ['district' => 'Batununggal', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40266'],
            ['district' => 'Buahbatu', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40286'],
            ['district' => 'Cibeunying Kaler', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40122'],
            ['district' => 'Cibeunying Kidul', 'city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40124'],
            ['district' => 'Cimahi Tengah', 'city' => 'Kota Cimahi', 'province' => 'Jawa Barat', 'postal_code' => '40525'],
            ['district' => 'Lembang', 'city' => 'Kabupaten Bandung Barat', 'province' => 'Jawa Barat', 'postal_code' => '40391'],
            ['district' => 'Soreang', 'city' => 'Kabupaten Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40911'],

            // Jakarta
            ['district' => 'Kebayoran Baru', 'city' => 'Kota Jakarta Selatan', 'province' => 'DKI Jakarta', 'postal_code' => '12110'],
            ['district' => 'Kebayoran Lama', 'city' => 'Kota Jakarta Selatan', 'province' => 'DKI Jakarta', 'postal_code' => '12240'],
            ['district' => 'Setiabudi', 'city' => 'Kota Jakarta Selatan', 'province' => 'DKI Jakarta', 'postal_code' => '12910'],
            ['district' => 'Tebet', 'city' => 'Kota Jakarta Selatan', 'province' => 'DKI Jakarta', 'postal_code' => '12810'],
            ['district' => 'Cilandak', 'city' => 'Kota Jakarta Selatan', 'province' => 'DKI Jakarta', 'postal_code' => '12430'],
            ['district' => 'Gambir', 'city' => 'Kota Jakarta Pusat', 'province' => 'DKI Jakarta', 'postal_code' => '10110'],
            ['district' => 'Menteng', 'city' => 'Kota Jakarta Pusat', 'province' => 'DKI Jakarta', 'postal_code' => '10310'],
            ['district' => 'Tanah Abang', 'city' => 'Kota Jakarta Pusat', 'province' => 'DKI Jakarta', 'postal_code' => '10210'],
            ['district' => 'Kelapa Gading', 'city' => 'Kota Jakarta Utara', 'province' => 'DKI Jakarta', 'postal_code' => '14240'],
            ['district' => 'Tanjung Priok', 'city' => 'Kota Jakarta Utara', 'province' => 'DKI Jakarta', 'postal_code' => '14310'],
            ['district' => 'Grogol Petamburan', 'city' => 'Kota Jakarta Barat', 'province' => 'DKI Jakarta', 'postal_code' => '11440'],
            ['district' => 'Kebon Jeruk', 'city' => 'Kota Jakarta Barat', 'province' => 'DKI Jakarta', 'postal_code' => '11530'],
            ['district' => 'Jatinegara', 'city' => 'Kota Jakarta Timur', 'province' => 'DKI Jakarta', 'postal_code' => '13310'],
            ['district' => 'Duren Sawit', 'city' => 'Kota Jakarta Timur', 'province' => 'DKI Jakarta', 'postal_code' => '13440'],

            // Bodetabek
            ['district' => 'Bekasi Barat', 'city' => 'Kota Bekasi', 'province' => 'Jawa Barat', 'postal_code' => '17145'],
            ['district' => 'Bekasi Timur', 'city' => 'Kota Bekasi', 'province' => 'Jawa Barat', 'postal_code' => '17111'],
            ['district' => 'Pancoran Mas', 'city' => 'Kota Depok', 'province' => 'Jawa Barat', 'postal_code' => '16436'],
            ['district' => 'Beji', 'city' => 'Kota Depok', 'province' => 'Jawa Barat', 'postal_code' => '16421'],
            ['district' => 'Bogor Tengah', 'city' => 'Kota Bogor', 'province' => 'Jawa Barat', 'postal_code' => '16121'],
            ['district' => 'Tangerang', 'city' => 'Kota Tangerang', 'province' => 'Banten', 'postal_code' => '15111'],
            ['district' => 'Serpong', 'city' => 'Kota Tangerang Selatan', 'province' => 'Banten', 'postal_code' => '15311'],
            ['district' => 'Ciputat', 'city' => 'Kota Tangerang Selatan', 'province' => 'Banten', 'postal_code' => '15411'],

            // Kota-kota Besar Lainnya
            ['district' => 'Gubeng', 'city' => 'Kota Surabaya', 'province' => 'Jawa Timur', 'postal_code' => '60281'],
            ['district' => 'Tegalsari', 'city' => 'Kota Surabaya', 'province' => 'Jawa Timur', 'postal_code' => '60262'],
            ['district' => 'Wonokromo', 'city' => 'Kota Surabaya', 'province' => 'Jawa Timur', 'postal_code' => '60241'],
            ['district' => 'Klojen', 'city' => 'Kota Malang', 'province' => 'Jawa Timur', 'postal_code' => '65111'],
            ['district' => 'Semarang Tengah', 'city' => 'Kota Semarang', 'province' => 'Jawa Tengah', 'postal_code' => '50132'],
            ['district' => 'Banjarsari', 'city' => 'Kota Surakarta', 'province' => 'Jawa Tengah', 'postal_code' => '57139'],
            ['district' => 'Gondokusuman', 'city' => 'Kota Yogyakarta', 'province' => 'DI Yogyakarta', 'postal_code' => '55221'],
            ['district' => 'Depok', 'city' => 'Kabupaten Sleman', 'province' => 'DI Yogyakarta', 'postal_code' => '55281'],
            ['district' => 'Medan Kota', 'city' => 'Kota Medan', 'province' => 'Sumatera Utara', 'postal_code' => '20212'],
            ['district' => 'Medan Petisah', 'city' => 'Kota Medan', 'province' => 'Sumatera Utara', 'postal_code' => '20111'],
            ['district' => 'Ilir Barat I', 'city' => 'Kota Palembang', 'province' => 'Sumatera Selatan', 'postal_code' => '30139'],
            ['district' => 'Padang Barat', 'city' => 'Kota Padang', 'province' => 'Sumatera Barat', 'postal_code' => '25112'],
            ['district' => 'Tampan', 'city' => 'Kota Pekanbaru', 'province' => 'Riau', 'postal_code' => '28292'],
            ['district' => 'Batam Kota', 'city' => 'Kota Batam', 'province' => 'Kepulauan Riau', 'postal_code' => '29461'],
            ['district' => 'Denpasar Selatan', 'city' => 'Kota Denpasar', 'province' => 'Bali', 'postal_code' => '80221'],
            ['district' => 'Kuta', 'city' => 'Kabupaten Badung', 'province' => 'Bali', 'postal_code' => '80361'],
            ['district' => 'Ujung Pandang', 'city' => 'Kota Makassar', 'province' => 'Sulawesi Selatan', 'postal_code' => '90111'],
            ['district' => 'Panakkukang', 'city' => 'Kota Makassar', 'province' => 'Sulawesi Selatan', 'postal_code' => '90231'],
            ['district' => 'Banjarmasin Tengah', 'city' => 'Kota Banjarmasin', 'province' => 'Kalimantan Selatan', 'postal_code' => '70111'],
            ['district' => 'Balikpapan Kota', 'city' => 'Kota Balikpapan', 'province' => 'Kalimantan Timur', 'postal_code' => '76111'],
            ['district' => 'Pontianak Kota', 'city' => 'Kota Pontianak', 'province' => 'Kalimantan Barat', 'postal_code' => '78111'],
        ];

        $matched = [];

        foreach ($dataset as $item) {
            $searchString = mb_strtolower("{$item['district']} {$item['city']} {$item['province']} {$item['postal_code']}");
            if (str_contains($searchString, $q)) {
                $label = "{$item['district']}, {$item['city']}, {$item['province']} ({$item['postal_code']})";
                $matched[] = [
                    'id' => null,
                    'label' => $label,
                    'province_name' => $item['province'],
                    'city_name' => $item['city'],
                    'district_name' => $item['district'],
                    'postal_code' => $item['postal_code'],
                    'biteship_area_id' => null,
                ];

                if (count($matched) >= 10) {
                    break;
                }
            }
        }

        return $matched;
    }
}
