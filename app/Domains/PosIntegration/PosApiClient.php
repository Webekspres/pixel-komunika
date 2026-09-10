<?php

namespace App\Domains\PosIntegration;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Throwable;

class PosApiClient
{
    public function __construct(
        protected ?string $baseUrl = null,
        protected ?string $staticApiKey = null,
        protected int $timeout = 10,
    ) {
        $this->baseUrl = $baseUrl ?? config('pos.base_url');
        $this->staticApiKey = $staticApiKey ?? config('pos.static_api_key');
        $this->timeout = $timeout ?: (int) config('pos.timeout', 10);
    }

    public function generateApiKey(?DateTimeInterface $now = null): string
    {
        if (empty($this->staticApiKey)) {
            throw new PosApiException('POS static API key is not configured.');
        }

        $time = $now ?? Carbon::now('Asia/Jakarta');

        if ($time instanceof CarbonInterface || $time instanceof Carbon) {
            $time = $time->copy()->setTimezone('Asia/Jakarta');
        } elseif ($time instanceof DateTimeImmutable) {
            $time = $time->setTimezone(new DateTimeZone('Asia/Jakarta'));
        } elseif ($time instanceof DateTime) {
            $time = (clone $time)->setTimezone(new DateTimeZone('Asia/Jakarta'));
        }

        $day = (int) $time->format('j');
        $month = (int) $time->format('n');
        $multiplier = $day * $month;

        return md5($this->staticApiKey.$multiplier);
    }

    public function categories(): array
    {
        return $this->request('GET', '/master/category');
    }

    public function products(): array
    {
        return $this->request('GET', '/master/product');
    }

    public function pricelist(): array
    {
        return $this->request('GET', '/master/pricelist');
    }

    public function productDetail(string $itemId): array
    {
        return $this->request('POST', '/master/product_detail', ['item_id' => $itemId]);
    }

    protected function request(string $method, string $path, array $data = []): array
    {
        if (empty($this->baseUrl)) {
            throw new PosApiException('POS base URL is not configured.');
        }

        $url = rtrim($this->baseUrl, '/').'/'.ltrim($path, '/');
        $apiKey = $this->generateApiKey();

        try {
            $pendingRequest = Http::withHeaders([
                'X-Api-Key' => $apiKey,
                'Accept' => 'application/json',
            ])
                ->timeout($this->timeout)
                ->retry(
                    times: 2,
                    sleepMilliseconds: 100,
                    when: fn (Throwable $e) => $e instanceof ConnectionException
                        || ($e instanceof RequestException && $e->response?->serverError()),
                    throw: false
                );

            $response = $method === 'GET'
                ? $pendingRequest->get($url, $data)
                : $pendingRequest->post($url, $data);
        } catch (Throwable $e) {
            throw new PosApiException($this->redact("POS API connection failure for [{$path}]: ".$e->getMessage()), 0, $e);
        }

        if ($response->failed()) {
            $status = $response->status();
            $bodyExcerpt = substr($response->body(), 0, 200);
            throw new PosApiException($this->redact("POS API request to [{$path}] failed with HTTP {$status}: {$bodyExcerpt}"));
        }

        $json = $response->json();
        if (! is_array($json) || ! array_key_exists('status', $json)) {
            throw new PosApiException($this->redact("POS API request to [{$path}] returned invalid envelope: ".substr($response->body(), 0, 200)));
        }

        if ($json['status'] !== 'success') {
            $message = $json['message'] ?? 'Unknown error';
            throw new PosApiException($this->redact("POS API error response for [{$path}]: {$message}"));
        }

        return $json['data'] ?? [];
    }

    public function redact(string $text): string
    {
        if (! empty($this->staticApiKey)) {
            $text = str_replace($this->staticApiKey, '[REDACTED_KEY]', $text);
        }

        return $text;
    }
}
