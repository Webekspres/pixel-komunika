<?php

use App\Services\Shipping\IndonesiaRegionService;

it('returns all 38 indonesian provinces sorted A-Z', function () {
    $provinces = IndonesiaRegionService::getProvinces();

    expect($provinces)->toHaveCount(38)
        ->and($provinces[0])->toBe('Aceh')
        ->and($provinces)->toContain('Jawa Barat')
        ->and($provinces)->toContain('DKI Jakarta')
        ->and($provinces)->toContain('Bali')
        ->and($provinces)->toContain('Papua Barat Daya');

    $sorted = $provinces;
    sort($sorted, SORT_NATURAL | SORT_FLAG_CASE);
    expect($provinces)->toBe($sorted);
});

it('returns cities in jawa barat sorted A-Z', function () {
    $cities = IndonesiaRegionService::getCitiesByProvince('Jawa Barat');

    expect($cities)->toContain('Kota Bandung')
        ->and($cities)->toContain('Kabupaten Bandung')
        ->and($cities)->toContain('Kota Bekasi')
        ->and($cities)->toContain('Kabupaten Bogor');

    $sorted = $cities;
    sort($sorted, SORT_NATURAL | SORT_FLAG_CASE);
    expect($cities)->toBe($sorted);
});

it('returns districts for a city sorted A-Z', function () {
    $districts = IndonesiaRegionService::getDistrictsByCity('Kota Bandung');

    expect($districts)->toContain('Coblong')
        ->and($districts)->toContain('Andir')
        ->and($districts)->toContain('Sukajadi');

    $sorted = $districts;
    sort($sorted, SORT_NATURAL | SORT_FLAG_CASE);
    expect($districts)->toBe($sorted);
});

it('returns empty array for invalid province', function () {
    $cities = IndonesiaRegionService::getCitiesByProvince('NonExistentProvince');

    expect($cities)->toBeArray()->toBeEmpty();
});
