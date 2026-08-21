<?php

use App\Models\Address;
use App\Models\CustomerProfile;
use App\Models\User;
use App\Services\Shipping\BiteshipAreaSearchService;
use Illuminate\Support\Facades\Http;

it('searches area via fallback dataset when biteship api key is not configured', function () {
    config(['biteship.api_key' => '']);

    $service = app(BiteshipAreaSearchService::class);
    $results = $service->search('Coblong');

    expect($results)->not->toBeEmpty()
        ->and($results[0]['district_name'])->toBe('Coblong')
        ->and($results[0]['city_name'])->toBe('Kota Bandung')
        ->and($results[0]['province_name'])->toBe('Jawa Barat');
});

it('searches area via biteship maps api when api key is configured', function () {
    config([
        'biteship.api_key' => 'test-api-key',
        'biteship.base_url' => 'https://api.biteship.com',
    ]);

    Http::fake([
        'api.biteship.com/v1/maps/areas*' => Http::response([
            'success' => true,
            'areas' => [
                [
                    'id' => 'IDnp0432101',
                    'name' => 'Coblong, Kota Bandung, Jawa Barat. 40132',
                    'administrative_division_level_1_name' => 'Jawa Barat',
                    'administrative_division_level_2_name' => 'Kota Bandung',
                    'administrative_division_level_3_name' => 'Coblong',
                    'postal_code' => 40132,
                ],
            ],
        ], 200),
    ]);

    $service = app(BiteshipAreaSearchService::class);
    $results = $service->search('Coblong');

    expect($results)->not->toBeEmpty()
        ->and($results[0]['biteship_area_id'])->toBe('IDnp0432101')
        ->and($results[0]['district_name'])->toBe('Coblong')
        ->and($results[0]['city_name'])->toBe('Kota Bandung')
        ->and($results[0]['postal_code'])->toBe('40132');
});

it('exposes area search endpoint via api route', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->getJson(route('api.areas.search', ['q' => 'Bandung']))
        ->assertOk()
        ->assertJsonStructure([
            '*' => [
                'id',
                'label',
                'province_name',
                'city_name',
                'district_name',
                'postal_code',
                'biteship_area_id',
            ],
        ]);
});

it('exposes districts endpoint for a city via api route sorted A-Z', function () {
    $customer = User::factory()->create();

    $response = $this->actingAs($customer)
        ->getJson(route('api.areas.districts', ['city' => 'Kota Bandung']))
        ->assertOk();

    $data = $response->json();
    expect($data)->toBeArray()
        ->and($data)->toContain('Coblong')
        ->and($data)->toContain('Andir')
        ->and($data)->toContain('Sukajadi');

    $sorted = $data;
    sort($sorted, SORT_NATURAL | SORT_FLAG_CASE);
    expect($data)->toBe($sorted);
});

it('saves address with biteship_area_id successfully', function () {
    $customer = User::factory()->create();
    CustomerProfile::create([
        'user_id' => $customer->id,
        'verification_status' => CustomerProfile::ACTIVE,
    ]);

    $response = $this->actingAs($customer)
        ->post(route('account.addresses.store'), [
            'label' => 'Toko Dago',
            'recipient_name' => 'Ahmad Fauzi',
            'recipient_phone' => '081234567890',
            'address_line' => 'Jl. Ir. H. Juanda No. 123',
            'province_name' => 'Jawa Barat',
            'city_name' => 'Kota Bandung',
            'district_name' => 'Coblong',
            'postal_code' => '40132',
            'biteship_area_id' => 'IDnp0432101',
            'is_default' => '1',
        ]);

    $response->assertRedirect();

    $address = Address::where('user_id', $customer->id)->firstOrFail();
    expect($address->label)->toBe('Toko Dago')
        ->and($address->biteship_area_id)->toBe('IDnp0432101')
        ->and($address->district_name)->toBe('Coblong')
        ->and($address->city_name)->toBe('Kota Bandung')
        ->and($address->province_name)->toBe('Jawa Barat')
        ->and($address->is_default)->toBeTrue();
});
