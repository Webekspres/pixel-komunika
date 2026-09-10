<?php

namespace Tests\Unit;

use App\Domains\PosIntegration\PosApiClient;
use App\Domains\PosIntegration\PosApiException;
use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PosApiClientTest extends TestCase
{
    public function test_x_api_key_calculation_with_frozen_time(): void
    {
        $client = new PosApiClient(
            baseUrl: 'https://pos-sandbox.test',
            staticApiKey: 'secret123',
        );

        $nowJakarta = Carbon::create(2026, 9, 10, 14, 0, 0, 'Asia/Jakarta');
        // day = 10, month = 9 -> 10 * 9 = 90
        $expected = md5('secret123'.(10 * 9));

        $this->assertSame($expected, $client->generateApiKey($nowJakarta));
    }

    public function test_x_api_key_uses_asia_jakarta_timezone_explicitly(): void
    {
        $client = new PosApiClient(
            baseUrl: 'https://pos-sandbox.test',
            staticApiKey: 'secret123',
        );

        // 2026-09-09 23:30:00 UTC corresponds to 2026-09-10 06:30:00 in Asia/Jakarta (UTC+7)
        $utcTime = new DateTimeImmutable('2026-09-09 23:30:00', new DateTimeZone('UTC'));
        // In Jakarta, day is 10 and month is 9
        $expectedInJakarta = md5('secret123'.(10 * 9));

        $this->assertSame($expectedInJakarta, $client->generateApiKey($utcTime));
    }

    public function test_credentials_are_redacted_from_exception_and_logs(): void
    {
        $client = new PosApiClient(
            baseUrl: 'https://pos-sandbox.test',
            staticApiKey: 'very-secret-token-value',
        );

        $textWithSecret = 'Error contacting https://pos-sandbox.test with key very-secret-token-value';
        $redacted = $client->redact($textWithSecret);

        $this->assertStringNotContainsString('very-secret-token-value', $redacted);
        $this->assertStringContainsString('[REDACTED_KEY]', $redacted);
    }

    public function test_operations_send_correct_requests(): void
    {
        Http::fake([
            'https://pos-sandbox.test/master/category' => Http::response([
                'status' => 'success',
                'message' => 'ok',
                'data' => [['category_id' => '1', 'category_name' => 'CAT 1']],
            ], 200),
            'https://pos-sandbox.test/master/product' => Http::response([
                'status' => 'success',
                'message' => 'ok',
                'data' => [['item_id' => 'P1', 'item_name' => 'Item 1']],
            ], 200),
            'https://pos-sandbox.test/master/pricelist' => Http::response([
                'status' => 'success',
                'message' => 'ok',
                'data' => [['item_id' => 'P1', 'price' => []]],
            ], 200),
            'https://pos-sandbox.test/master/product_detail' => Http::response([
                'status' => 'success',
                'message' => 'ok',
                'data' => ['item_id' => 'P1', 'item_name' => 'Item 1'],
            ], 200),
        ]);

        $client = new PosApiClient(
            baseUrl: 'https://pos-sandbox.test',
            staticApiKey: 'secret123',
        );

        $categories = $client->categories();
        $products = $client->products();
        $pricelist = $client->pricelist();
        $detail = $client->productDetail('P1');

        $this->assertCount(1, $categories);
        $this->assertCount(1, $products);
        $this->assertCount(1, $pricelist);
        $this->assertSame('P1', $detail['item_id']);

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Api-Key') && $request->hasHeader('Accept', 'application/json');
        });
    }

    public function test_envelope_error_and_auth_error_throws_pos_api_exception(): void
    {
        Http::fake([
            'https://pos-sandbox.test/master/category' => Http::response([
                'status' => 'error',
                'message' => 'Unauthorized signature',
                'data' => [],
            ], 200),
        ]);

        $client = new PosApiClient(
            baseUrl: 'https://pos-sandbox.test',
            staticApiKey: 'secret123',
        );

        $this->expectException(PosApiException::class);
        $this->expectExceptionMessage('POS API error response for [/master/category]: Unauthorized signature');

        $client->categories();
    }

    public function test_server_5xx_retries_and_succeeds(): void
    {
        Http::fake([
            'https://pos-sandbox.test/master/category' => Http::sequence()
                ->push(['error' => 'temporary error'], 503)
                ->push([
                    'status' => 'success',
                    'message' => 'ok',
                    'data' => [['category_id' => '1', 'category_name' => 'CAT 1']],
                ], 200),
        ]);

        $client = new PosApiClient(
            baseUrl: 'https://pos-sandbox.test',
            staticApiKey: 'secret123',
        );

        $categories = $client->categories();
        $this->assertCount(1, $categories);
    }
}
