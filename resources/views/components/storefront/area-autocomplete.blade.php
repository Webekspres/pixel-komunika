@props([
    'province' => '',
    'city' => '',
    'district' => '',
    'postalCode' => '',
    'areaId' => '',
    'required' => true,
])

@php
    $hasInitial = ! empty($province) || ! empty($city) || ! empty($district);
    $initialLabel = $hasInitial
        ? implode(', ', array_filter([$district, $city, $province])) . ($postalCode ? " ({$postalCode})" : '')
        : '';
@endphp

<div
    x-data="{
        searchQuery: '',
        results: [],
        isLoading: false,
        isOpen: false,
        isAreaSelected: {{ $hasInitial ? 'true' : 'false' }},
        selectedLabel: '{{ addslashes($initialLabel) }}',
        provinceName: '{{ addslashes($province) }}',
        cityName: '{{ addslashes($city) }}',
        districtName: '{{ addslashes($district) }}',
        postalCode: '{{ addslashes($postalCode) }}',
        biteshipAreaId: '{{ addslashes($areaId) }}',

        async searchAreas() {
            if (this.searchQuery.trim().length < 2) {
                this.results = [];
                this.isOpen = false;
                return;
            }

            this.isLoading = true;
            this.isOpen = true;

            try {
                const response = await fetch(`/api/areas/search?q=${encodeURIComponent(this.searchQuery.trim())}`);
                if (response.ok) {
                    this.results = await response.json();
                } else {
                    this.results = [];
                }
            } catch (err) {
                console.error('Area search error:', err);
                this.results = [];
            } finally {
                this.isLoading = false;
            }
        },

        selectArea(item) {
            this.provinceName = item.province_name || '';
            this.cityName = item.city_name || '';
            this.districtName = item.district_name || '';
            this.postalCode = item.postal_code || '';
            this.biteshipAreaId = item.biteship_area_id || '';
            this.selectedLabel = item.label || `${this.districtName}, ${this.cityName}, ${this.provinceName}`;
            this.isAreaSelected = true;
            this.isOpen = false;
            this.searchQuery = '';
            this.results = [];
        },

        resetSelection() {
            this.isAreaSelected = false;
            this.searchQuery = '';
            this.isOpen = false;
            this.$nextTick(() => {
                this.$refs.areaInput?.focus();
            });
        }
    }"
    class="space-y-3 relative"
    @click.away="isOpen = false"
>
    <!-- Hidden Form Fields Submitted with Form -->
    <input type="hidden" name="province_name" x-model="provinceName" {{ $required ? 'required' : '' }}>
    <input type="hidden" name="city_name" x-model="cityName" {{ $required ? 'required' : '' }}>
    <input type="hidden" name="district_name" x-model="districtName" {{ $required ? 'required' : '' }}>
    <input type="hidden" name="postal_code" x-model="postalCode">
    <input type="hidden" name="biteship_area_id" x-model="biteshipAreaId">

    <!-- State 1: Selected Area Preview -->
    <template x-if="isAreaSelected">
        <div>
            <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
                Wilayah Pengiriman <span class="text-red-500">*</span>
            </label>
            <div class="rounded-xl border border-zinc-200/80 bg-zinc-50/80 p-3.5 flex items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-brand-yellow text-zinc-900 shadow-2xs">
                        <x-icon name="map-pin" class="size-4.5" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs sm:text-sm font-bold text-zinc-900 truncate" x-text="selectedLabel"></p>
                        <p class="text-[11px] sm:text-xs text-zinc-500 font-medium mt-0.5 truncate">
                            <span x-text="'Kec. ' + districtName"></span> •
                            <span x-text="cityName"></span> •
                            <span x-text="provinceName"></span>
                            <span x-show="postalCode" x-text="' (' + postalCode + ')'"></span>
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="resetSelection()"
                    class="shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950 transition shadow-2xs"
                >
                    <x-icon name="refresh-cw" class="size-3.5" />
                    <span>Ganti Wilayah</span>
                </button>
            </div>
        </div>
    </template>

    <!-- State 2: Search Input & Dropdown -->
    <div x-show="!isAreaSelected">
        <label class="block text-xs sm:text-sm font-semibold text-zinc-700 mb-1.5">
            Wilayah Pengiriman (Kecamatan / Kota / Kode Pos) <span class="text-red-500">*</span>
        </label>
        
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400">
                <x-icon name="search" class="size-4" />
            </div>

            <input
                x-ref="areaInput"
                type="text"
                x-model="searchQuery"
                @input.debounce.300ms="searchAreas()"
                @focus="if(searchQuery.length >= 2) isOpen = true"
                placeholder="Ketik nama Kecamatan, Kota, atau Kode Pos (misal: Coblong, Bandung)..."
                class="w-full rounded-xl border border-zinc-200/80 bg-white pl-10 pr-10 py-2.5 text-xs sm:text-sm font-medium text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 outline-hidden transition shadow-2xs"
            />

            <div x-show="isLoading" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-zinc-400">
                <svg class="size-4 animate-spin text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>
        </div>

        <!-- Dropdown Results Popup -->
        <div
            x-show="isOpen && searchQuery.length >= 2"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute z-50 mt-1 max-h-60 w-full overflow-y-auto rounded-xl border border-zinc-200 bg-white py-1 shadow-lg"
        >
            <template x-if="isLoading">
                <div class="px-4 py-3 text-center text-xs text-zinc-500">
                    <span>Mencari wilayah di Indonesia...</span>
                </div>
            </template>

            <template x-if="!isLoading && results.length === 0">
                <div class="px-4 py-3 text-center text-xs text-zinc-500">
                    <span>Tidak ditemukan wilayah yang sesuai dengan "<strong x-text="searchQuery"></strong>"</span>
                </div>
            </template>

            <template x-if="!isLoading && results.length > 0">
                <div>
                    <template x-for="item in results" :key="item.label + (item.biteship_area_id || '')">
                        <button
                            type="button"
                            @click="selectArea(item)"
                            class="w-full text-left px-3.5 py-2.5 text-xs hover:bg-amber-50/80 transition flex items-center justify-between gap-2 border-b border-zinc-50 last:border-b-0"
                        >
                            <div>
                                <p class="font-bold text-zinc-900 text-xs sm:text-sm" x-text="item.label"></p>
                                <p class="text-[11px] text-zinc-500 mt-0.5">
                                    <span x-text="item.district_name"></span> • <span x-text="item.city_name"></span> • <span x-text="item.province_name"></span>
                                </p>
                            </div>
                            <span class="text-zinc-400 shrink-0">
                                <x-icon name="arrow-right" class="size-3.5" />
                            </span>
                        </button>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>
