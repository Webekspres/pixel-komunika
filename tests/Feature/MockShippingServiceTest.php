<?php

use App\Services\Shipping\MockBiteshipShippingService;

it('calculates mock shipping rates correctly based on weight and destination', function () {
    $service = new MockBiteshipShippingService;

    $ratesJakarta = $service->calculateRates('Jakarta Selatan', 1500);
    expect($ratesJakarta)->not->toBeEmpty();

    $jne = collect($ratesJakarta)->firstWhere('code', 'jne');
    expect($jne['cost'])->toBe(18000); // 9000 * 2kg
    expect(collect($ratesJakarta)->pluck('code'))->toContain('grab', 'gojek');

    $ratesSurabaya = $service->calculateRates('Surabaya', 500);
    $jneSurabaya = collect($ratesSurabaya)->firstWhere('code', 'jne');
    expect($jneSurabaya['cost'])->toBe(22000); // 22000 * 1kg

    $ratesBandung = $service->calculateRates('Bandung', 1000);
    expect(collect($ratesBandung)->pluck('code'))->toContain('store');
});
