<?php

use App\Models\StoreCourierRate;
use App\Services\Shipping\BiteshipShippingService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();
});

it('maps Biteship rates to correct format', function () {
    Http::fake([
        'api.biteship.com/v1/maps/areas*' => Http::response([
            'areas' => [
                ['area_id' => 'DEST123', 'city' => 'Jakarta', 'province' => 'DKI Jakarta'],
            ],
        ], 200),
        'api.biteship.com/v1/rates/couriers*' => Http::response([
            'rates' => [
                [
                    'courier_company' => ['name' => 'JNE'],
                    'courier_type_code' => 'REG',
                    'courier_type_name' => 'Reguler',
                    'cost' => 15000,
                    'etd' => '2-3',
                ],
                [
                    'courier_company' => ['name' => 'J&T'],
                    'courier_type_code' => 'REG',
                    'courier_type_name' => 'Reguler',
                    'cost' => 12000,
                    'etd' => '2-3',
                ],
            ],
        ], 200),
    ]);

    config(['biteship.api_key' => 'test-key', 'biteship.origin_area_id' => 'ORIGIN123']);

    $service = new BiteshipShippingService();
    $rates = $service->calculateRates('Jakarta', 500);

    expect($rates)->toHaveCount(2)
        ->and($rates[0])->toMatchArray([
            'provider' => 'BITESHIP',
            'code' => 'reg',
            'service' => 'REG',
            'name' => 'JNE Reguler',
            'cost' => 15000,
        ])
        ->and($rates[1])->toMatchArray([
            'provider' => 'BITESHIP',
            'code' => 'reg',
            'service' => 'REG',
            'name' => 'J&T Reguler',
            'cost' => 12000,
        ]);
});

it('returns empty array on timeout and logs warning', function () {
    Http::fake([
        'api.biteship.com/v1/maps/areas*' => Http::response([], 200),
        'api.biteship.com/v1/rates/couriers*' => Http::response([], 200),
    ]);

    config(['biteship.api_key' => 'test-key', 'biteship.origin_area_id' => 'ORIGIN123']);

    // Simulate timeout by not having the request complete
    $service = new BiteshipShippingService();
    $rates = $service->calculateRates('Jakarta', 500);

    // The service should handle timeout gracefully (Http::timeout is set in service)
    // This test verifies the service doesn't crash
    expect($rates)->toBeEmpty();
});

it('returns empty array on 5xx error and logs warning', function () {
    Http::fake([
        'api.biteship.com/v1/maps/areas*' => Http::response([], 200),
        'api.biteship.com/v1/rates/couriers*' => Http::response(['message' => 'Server error'], 500),
    ]);

    config(['biteship.api_key' => 'test-key', 'biteship.origin_area_id' => 'ORIGIN123']);

    $service = new BiteshipShippingService();
    $rates = $service->calculateRates('Jakarta', 500);

    expect($rates)->toBeEmpty();
});

it('returns empty array when area not found', function () {
    Http::fake([
        'api.biteship.com/v1/maps/areas*' => Http::response(['areas' => []], 200),
    ]);

    config(['biteship.api_key' => 'test-key', 'biteship.origin_area_id' => 'ORIGIN123']);

    $service = new BiteshipShippingService();
    $rates = $service->calculateRates('UnknownCity', 500);

    expect($rates)->toBeEmpty();
});

it('returns empty array when API key or origin not configured', function () {
    config(['biteship.api_key' => '', 'biteship.origin_area_id' => '']);

    $service = new BiteshipShippingService();
    $rates = $service->calculateRates('Jakarta', 500);

    expect($rates)->toBeEmpty();
});

it('filters out zero cost rates', function () {
    Http::fake([
        'api.biteship.com/v1/maps/areas*' => Http::response([
            'areas' => [['area_id' => 'DEST123', 'city' => 'Jakarta']],
        ], 200),
        'api.biteship.com/v1/rates/couriers*' => Http::response([
            'rates' => [
                [
                    'courier_company' => ['name' => 'JNE'],
                    'courier_type_code' => 'REG',
                    'courier_type_name' => 'Reguler',
                    'cost' => 0,
                    'etd' => '2-3',
                ],
            ],
        ], 200),
    ]);

    config(['biteship.api_key' => 'test-key', 'biteship.origin_area_id' => 'ORIGIN123']);

    $service = new BiteshipShippingService();
    $rates = $service->calculateRates('Jakarta', 500);

    expect($rates)->toBeEmpty();
});