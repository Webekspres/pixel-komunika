<?php

use App\Models\Shipment;
use App\Services\Shipping\StoreCourierFreeShipping;

it('zeros store courier when subtotal plus pph meets threshold', function () {
    $rates = [
        ['provider' => Shipment::PROVIDER_STORE, 'code' => 'store', 'service' => 'Kurir Toko', 'cost' => 15000.0],
        ['provider' => Shipment::PROVIDER_BITESHIP, 'code' => 'jne', 'service' => 'REG', 'cost' => 18000.0],
    ];

    $applied = StoreCourierFreeShipping::applyToRates($rates, 900_000, 100_000);

    expect($applied[0]['cost'])->toBe(0.0)
        ->and($applied[1]['cost'])->toBe(18000.0);
});

it('keeps store courier cost below threshold', function () {
    $option = ['provider' => Shipment::PROVIDER_STORE, 'cost' => 12000.0];

    $applied = StoreCourierFreeShipping::applyToOption($option, 500_000, 50_000);

    expect($applied['cost'])->toBe(12000.0);
});

it('does not waive biteship even above threshold', function () {
    $option = ['provider' => Shipment::PROVIDER_BITESHIP, 'cost' => 25000.0];

    $applied = StoreCourierFreeShipping::applyToOption($option, 2_000_000, 0);

    expect($applied['cost'])->toBe(25000.0);
});
