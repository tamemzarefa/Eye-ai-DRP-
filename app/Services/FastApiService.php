<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * FastApiService — HTTP client wrapper for the external FastAPI
 * AI / Machine Learning service.
 *
 * All communication with the FastAPI inference endpoints is
 * centralised here so the rest of the application remains
 * agnostic of transport details.
 *
 * Configuration is pulled from `config/services.php` → `fastapi`
 * which reads from `.env`:
 *   FASTAPI_BASE_URL=http://localhost:8000
 *   FASTAPI_API_KEY=your-secret-key
 *   FASTAPI_TIMEOUT=30
 */
class FastApiService
{
    /** Base URL of the FastAPI service. */
    protected readonly string $baseUrl;

    /** Bearer / API key for authenticating requests. */
    protected readonly string $apiKey;

    /** HTTP timeout in seconds. */
    protected readonly int $timeout;

    public function __construct()
    {
        /** @var string $baseUrl */
        $baseUrl = config('services.fastapi.base_url', 'http://localhost:8000');

        /** @var string $apiKey */
        $apiKey = config('services.fastapi.api_key', '');

        /** @var int $timeout */
        $timeout = (int) config('services.fastapi.timeout', 30);

        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey  = $apiKey;
        $this->timeout = $timeout;
    }

    // ─── Public API ───────────────────────────────────────────────

    /**
     * Send a medical image to the FastAPI model for diagnostic
     * inference / prediction.
     *
     * Sends the image as multipart form-data together with an
     * optional metadata payload (e.g. patient ID, context).
     *
     * @param  string               $imagePath  Absolute path to the image file.
     * @param  array<string, mixed> $metadata   Additional context for the model.
     * @return array<string, mixed>             Parsed JSON prediction payload.
     *
     * @throws \Illuminate\Http\Client\RequestException  On 4xx / 5xx responses.
     */
    public function analyzeImage(string $imagePath, array $metadata = []): array
    {
        Log::info('FastApiService: Sending image for analysis.', [
            'image_path' => $imagePath,
            'metadata'   => $metadata,
        ]);

        $response = $this->client()
            ->attach('image', fopen($imagePath, 'r'), basename($imagePath))
            ->post('/api/v1/predict', $metadata);

        // Throw on server / client errors (4xx / 5xx)
        $response->throw();

        Log::info('FastApiService: Prediction received.', [
            'status' => $response->status(),
        ]);

        /** @var array<string, mixed> */
        return $response->json();
    }

    /**
     * Health-check ping to verify FastAPI connectivity.
     *
     * @return bool  True when the service responds with 200 OK.
     */
    public function healthCheck(): bool
    {
        try {
            $response = $this->client()->get('/health');

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('FastApiService: Health check failed.', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    // ─── Internal Helpers ─────────────────────────────────────────

    /**
     * Build a pre-configured HTTP client with base URL, auth
     * headers, timeout, and retry policy.
     */
    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->timeout($this->timeout)
            ->retry(
                times: 3,
                sleepMilliseconds: 500,
                throw: false,
            );
    }
}
