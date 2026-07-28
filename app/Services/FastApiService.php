<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
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

    /**
     * Forward an eye image to the FastAPI Diabetic Retinopathy
     * prediction endpoint: POST /predict
     *
     * The FastAPI server expects:
     *   - Content-Type : multipart/form-data
     *   - Field name   : `file`   ← MUST match the Python parameter name
     *
     * The endpoint returns JSON:
     *   { "prediction": "No DR", "confidence": 0.9724 }
     *
     * @param  UploadedFile                $image   The validated uploaded file.
     * @return array{prediction: string, confidence: float}
     *
     * @throws \Illuminate\Http\Client\RequestException  On 4xx / 5xx responses.
     * @throws \Throwable                                On connection failure.
     */
    public function analyzeEyeImage(UploadedFile $image): array
    {
        // Read the raw binary content of the file from its temporary path.
        // getRealPath() is safe here because the file has already been
        // validated by AnalyzeEyeImageRequest before reaching this method.
        $fileContents = file_get_contents($image->getRealPath());
        $filename     = $image->getClientOriginalName();
        $mimeType     = $image->getMimeType() ?? 'image/jpeg';

        Log::info('FastApiService: Forwarding eye image to /predict.', [
            'filename'  => $filename,
            'mime_type' => $mimeType,
            'size_kb'   => round(strlen($fileContents) / 1024, 2),
        ]);

        // Http::attach() builds the multipart/form-data body.
        // The first argument MUST be 'file' — the FastAPI parameter name.
        $response = $this->client()
            ->attach(
                name    : 'file',          // ← FastAPI field name
                contents: $fileContents,
                filename: $filename,
                headers : ['Content-Type' => $mimeType],
            )
            ->post('/predict');

        // Throw a RequestException on any 4xx / 5xx status code.
        // The controller's try-catch will handle it and return a 503.
        $response->throw();

        Log::info('FastApiService: /predict response received.', [
            'http_status' => $response->status(),
        ]);

        /** @var array<string, mixed>|null $payload */
        $payload = $response->json();

        if (! is_array($payload)
            || ! array_key_exists('prediction', $payload)
            || ! array_key_exists('confidence', $payload)
        ) {
            Log::error('FastApiService: Invalid /predict response payload.', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'json'   => $payload,
            ]);

            throw new \RuntimeException('FastAPI /predict returned an invalid payload.');
        }

        return [
            'prediction' => (string) $payload['prediction'],
            'confidence' => (float) $payload['confidence'],
        ];
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
