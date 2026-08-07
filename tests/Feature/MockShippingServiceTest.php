<?php

use App\Services\Shipping\MockBiteshipShippingService;

it('calculates mock shipping rates correctly based on weight and destination', function () {
    $service = new MockBiteshipShippingService();

    $ratesJakarta = $service->calculateRates('Jakarta Selatan', 1500);
    expect($ratesJakarta)->not->toBeEmpty();
    expect($ratesJakarta[0]['code'])->toBe('jne');
    expect($ratesJakarta[0]['cost'])->toBe(18000); // 9000 * 2kg

    $ratesSurabaya = $service->calculateRates('Surabaya', 500);
    expect($ratesSurabaya[0]['cost'])->toBe(22000); // 22000 * 1kg
});
