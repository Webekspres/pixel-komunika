@props([
    'province' => '',
    'city' => '',
    'district' => '',
    'postalCode' => '',
])

@php
    $regions = \App\Services\Shipping\IndonesiaRegionService::all();
    $provinces = \App\Services\Shipping\IndonesiaRegionService::getProvinces();
    $initialDistricts = ! empty($city) ? \App\Services\Shipping\IndonesiaRegionService::getDistrictsByCity($city) : [];
    if (! empty($district) && ! in_array($district, $initialDistricts, true)) {
        $initialDistricts[] = $district;
        sort($initialDistricts, SORT_NATURAL | SORT_FLAG_CASE);
    }
@endphp

<div
    x-data="{
        regionsData: {{ json_encode($regions) }},
        selectedProvince: '{{ addslashes($province) }}',
        selectedCity: '{{ addslashes($city) }}',
        selectedDistrict: '{{ addslashes($district) }}',
        availableDistricts: {{ json_encode($initialDistricts) }},
        isLoadingDistricts: false,
        
        get availableCities() {
            if (!this.selectedProvince || !this.regionsData[this.selectedProvince]) {
                return [];
            }
            return this.regionsData[this.selectedProvince];
        },

        async onProvinceChange() {
            if (!this.availableCities.includes(this.selectedCity)) {
                this.selectedCity = '';
                this.selectedDistrict = '';
                this.availableDistricts = [];
            }
        },

        async onCityChange() {
            this.selectedDistrict = '';
            this.availableDistricts = [];

            if (!this.selectedCity) {
                return;
            }

            this.isLoadingDistricts = true;

            try {
                const response = await fetch(`/api/areas/districts?city=${encodeURIComponent(this.selectedCity)}`);
                if (response.ok) {
                    const data = await response.json();
                    this.availableDistricts = Array.isArray(data) ? data : [];
                }
            } catch (e) {
                console.error('Gagal memuat daftar kecamatan:', e);
            } finally {
                this.isLoadingDistricts = false;
            }
        },

        async init() {
            if (this.selectedCity && (!this.availableDistricts || this.availableDistricts.length === 0)) {
                await this.onCityChange();
                if ('{{ addslashes($district) }}') {
                    this.selectedDistrict = '{{ addslashes($district) }}';
                }
            }
        }
    }"
    class="space-y-4"
>
    <!-- Baris 1: Provinsi & Kota/Kabupaten (Dropdown Bertingkat A-Z) -->
    <div class="grid gap-4 sm:grid-cols-2">
        <!-- Field 1: Provinsi -->
        <div>
            <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                Provinsi <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select
                    name="province_name"
                    x-model="selectedProvince"
                    @change="onProvinceChange()"
                    required
                    class="w-full appearance-none rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs pr-8"
                >
                    <option value="" disabled selected>Pilih Provinsi</option>
                    @foreach ($provinces as $prov)
                        <option value="{{ $prov }}" {{ $province === $prov ? 'selected' : '' }}>
                            {{ $prov }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                    <x-icon name="chevron-down" class="size-4" />
                </div>
            </div>
        </div>

        <!-- Field 2: Kota / Kabupaten (Dropdown A-Z) -->
        <div>
            <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                Kota / Kabupaten <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select
                    name="city_name"
                    x-model="selectedCity"
                    @change="onCityChange()"
                    :disabled="!selectedProvince"
                    required
                    class="w-full appearance-none rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs pr-8 disabled:bg-zinc-100 disabled:text-zinc-400 disabled:cursor-not-allowed"
                >
                    <option value="" disabled selected x-text="selectedProvince ? 'Pilih Kota / Kabupaten' : 'Pilih Provinsi terlebih dahulu'"></option>
                    <template x-for="cityItem in availableCities" :key="cityItem">
                        <option :value="cityItem" x-text="cityItem" :selected="cityItem === selectedCity"></option>
                    </template>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                    <x-icon name="chevron-down" class="size-4" />
                </div>
            </div>
        </div>
    </div>

    <!-- Baris 2: Kecamatan (Dropdown A-Z) & Kode Pos (Teks Opsional) -->
    <div class="grid gap-4 sm:grid-cols-2">
        <!-- Field 3: Kecamatan (Dropdown Bertingkat A-Z - Always Visible) -->
        <div>
            <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                Kecamatan <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select
                    name="district_name"
                    x-model="selectedDistrict"
                    :disabled="!selectedCity || isLoadingDistricts"
                    required
                    class="w-full appearance-none rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs pr-8 disabled:bg-zinc-100 disabled:text-zinc-400 disabled:cursor-not-allowed"
                >
                    <option value="" disabled selected x-text="!selectedCity ? 'Pilih Kota terlebih dahulu' : (isLoadingDistricts ? 'Memuat Kecamatan...' : 'Pilih Kecamatan')"></option>
                    <template x-for="districtItem in availableDistricts" :key="districtItem">
                        <option :value="districtItem" x-text="districtItem" :selected="districtItem === selectedDistrict"></option>
                    </template>
                    <template x-if="selectedDistrict && !availableDistricts.includes(selectedDistrict)">
                        <option :value="selectedDistrict" x-text="selectedDistrict" selected></option>
                    </template>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                    <template x-if="isLoadingDistricts">
                        <svg class="size-4 animate-spin text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </template>
                    <template x-if="!isLoadingDistricts">
                        <x-icon name="chevron-down" class="size-4" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Field 4: Kode Pos (Opsional) -->
        <div>
            <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                Kode Pos
            </label>
            <input
                type="text"
                name="postal_code"
                value="{{ $postalCode }}"
                placeholder="Contoh: 40132"
                class="w-full rounded-xl border border-zinc-200/80 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
            />
        </div>
    </div>
</div>
