<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * EyeAiApiService — Centralised FastAPI HTTP client wrapper.
 *
 * Handles /predict, /segment and /chat requests from Laravel using
 * multipart uploads and JSON payloads, and normalises the returned data.
 */
class EyeAiApiService
{
    protected readonly string $baseUrl;
    protected readonly string $apiKey;
    protected readonly int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.fastapi.base_url', 'http://localhost:8000'), '/');
        $this->apiKey = config('services.fastapi.api_key', '');
        $this->timeout = (int) config('services.fastapi.timeout', 30);
    }

public function predict(UploadedFile $file): array
{
    // يمكنك إلغاء شرط الفحص الإجباري، أو وضع المفاتيح القادمة من البايثون فعلياً
    return $this->sendFile('/predict', $file, []); // مصفوفة فارغة لعدم إجبار مفاتيح معينة
}

    public function segment(UploadedFile $file): array
    {
        return $this->sendFile('/segment', $file);
    }

    public function chat(string $question): array
    {
        Log::info('EyeAiApiService: Sending chat request to FastAPI.', [
            'endpoint' => '/chat',
            'question' => mb_substr($question, 0, 256),
        ]);

        try {
            $response = $this->client()
                ->post('/chat', ['question' => $question]);

            $response->throw();

            return $this->parseJsonResponse($response);
        } catch (ConnectionException $e) {
            Log::error('EyeAiApiService: FastAPI /chat connection failed.', [
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Unable to reach the FastAPI chat service.', previous: $e);
        } catch (RequestException $e) {
            Log::error('EyeAiApiService: FastAPI /chat returned an error.', [
                'status'  => $e->response?->status(),
                'body'    => $e->response?->body(),
                'error'   => $e->getMessage(),
            ]);

            throw new \RuntimeException('FastAPI chat request failed.', previous: $e);
        } catch (Throwable $e) {
            Log::error('EyeAiApiService: Unexpected error while calling /chat.', [
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('FastAPI chat service returned an invalid response.', previous: $e);
        }
    }

   protected function sendFile(string $uri, UploadedFile $file, array $requiredKeys = []): array
{
    // 1. محاولة قراءة محتوى الملف بطريقة آمنة
    $fileContents = null;

    if ($file->getRealPath() && file_exists($file->getRealPath())) {
        $fileContents = file_get_contents($file->getRealPath());
    } elseif (method_exists($file, 'getContent')) {
        $fileContents = $file->getContent();
    }

    if (empty($fileContents)) {
        throw new \RuntimeException('The uploaded file could not be read for FastAPI request.');
    }

    $filename = $file->getClientOriginalName();
    // استخدام getClientMimeType بدلاً من getMimeType لتجنب محاولة فتح الملف من القرص مرة أخرى
    $mimeType = $file->getClientMimeType() ?: 'application/octet-stream';

    Log::info('EyeAiApiService: Sending image file to FastAPI.', [
        'endpoint'  => $uri,
        'filename'  => $filename,
        'mime_type' => $mimeType,
        'size_kb'   => round(strlen($fileContents) / 1024, 2),
    ]);

    try {
        $response = $this->client()
            ->attach(
                name: 'file',
                contents: $fileContents,
                filename: $filename,
                headers: ['Content-Type' => $mimeType],
            )
            ->post($uri);

        $response->throw();

        $payload = $this->parseJsonResponse($response);
        $this->validatePayload($payload, $requiredKeys, $uri);

        return $payload;

    } catch (ConnectionException $e) {
        Log::error('EyeAiApiService: FastAPI connection failed.', [
            'endpoint' => $uri,
            'error'    => $e->getMessage(),
        ]);

        throw new \RuntimeException('Unable to reach the FastAPI service.', previous: $e);

    } catch (RequestException $e) {
        Log::error('EyeAiApiService: FastAPI request failed.', [
            'endpoint' => $uri,
            'status'   => $e->response?->status(),
            'body'     => $e->response?->body(),
            'error'    => $e->getMessage(),
        ]);

        throw new \RuntimeException('FastAPI request failed.', previous: $e);

    } catch (Throwable $e) {
        Log::error('EyeAiApiService: FastAPI response parsing failed.', [
            'endpoint' => $uri,
            'error'    => $e->getMessage(),
        ]);

        throw new \RuntimeException('FastAPI returned an invalid response.', previous: $e);
    }
}

    protected function parseJsonResponse(Response $response): array
    {
        $payload = $response->json();

        if (is_string($payload)) {
            $payload = json_decode($payload, true);
        }

        if (is_object($payload)) {
            $payload = json_decode(json_encode($payload), true);
        }

        if (! is_array($payload)) {
            Log::error('EyeAiApiService: Unexpected FastAPI response format.', [
                'status'  => $response->status(),
                'body'    => $response->body(),
                'parsed'  => $payload,
            ]);

            throw new \RuntimeException('FastAPI response is not a valid JSON object.');
        }

        return $payload;
    }

    protected function validatePayload(array $payload, array $requiredKeys, string $uri): void
    {
        foreach ($requiredKeys as $key) {
            if (! array_key_exists($key, $payload)) {
                Log::error('EyeAiApiService: FastAPI payload missing required key.', [
                    'endpoint' => $uri,
                    'missing'  => $key,
                    'payload'  => $payload,
                ]);

                throw new \RuntimeException(sprintf('FastAPI %s response is missing required data: %s', $uri, $key));
            }
        }
    }

    protected function client(): PendingRequest
    {
        $headers = ['Accept' => 'application/json'];

        if ($this->apiKey !== '') {
            $headers['X-API-Key'] = $this->apiKey;
        }

        return Http::baseUrl($this->baseUrl)
            ->withHeaders($headers)
            ->timeout($this->timeout)
            ->retry(
                times: 2,
                sleepMilliseconds: 500,
                throw: false,
            );
    }
}
