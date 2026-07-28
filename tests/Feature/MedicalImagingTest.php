<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * MedicalImagingTest
 *
 * Feature tests for the Medical Imaging Diabetic Retinopathy API endpoint.
 * Verifies validation rules, HTTP client faking, and response formats.
 */
class MedicalImagingTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    // ─── Test Cases ───────────────────────────────────────────────

    /**
     * Test a successful DR prediction integration.
     *
     * Fakes the external FastAPI endpoint to respond with a 200 success code
     * containing prediction and confidence score metrics.
     */
    public function test_can_analyze_eye_image_successfully(): void
    {
        // 1. Fake the external FastAPI `/predict` server endpoint
        Http::fake([
            '*/predict' => Http::response([
                'prediction' => 'No Diabetic Retinopathy',
                'confidence' => 0.985,
            ], Response::HTTP_OK),
        ]);

        // 2. Create a fake JPEG image file (100 KB)
        $fakeImage = UploadedFile::fake()->image('eye_scan.jpg', 600, 600);

        // 3. Make POST request to our Laravel API route
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson(route('medical-imaging.analyze'), [
                'eye_image' => $fakeImage,
            ]);

        // 4. Assert HTTP status code is 200 OK
        $response->assertStatus(Response::HTTP_OK);

        // 5. Verify the structured JSON response matches expectations
        $response->assertJson([
            'success' => true,
            'message' => 'Eye image analysis completed successfully.',
            'data' => [
                'prediction' => 'No Diabetic Retinopathy',
                'confidence' => 0.985,
            ],
        ]);

        // 6. Verify that the FastAPI endpoint was indeed called once
        Http::assertSent(function ($request) {
            return $request->url() === config('services.fastapi.base_url') . '/predict'
                && $request->isMultipart()
                && $request->data()[0]['name'] === 'file'; // Uploaded field mapping
        });
    }

    /**
     * Test FastAPI returning a JSON string response instead of an object.
     */
    public function test_can_handle_fastapi_json_string_response(): void
    {
        Http::fake([
            '*/predict' => Http::response('{
                "prediction": "No Diabetic Retinopathy",
                "confidence": 0.985
            }', Response::HTTP_OK),
        ]);

        $fakeImage = UploadedFile::fake()->image('eye_scan.jpg', 600, 600);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson(route('medical-imaging.analyze'), [
                'eye_image' => $fakeImage,
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'success' => true,
            'data' => [
                'prediction' => 'No Diabetic Retinopathy',
                'confidence' => 0.985,
            ],
        ]);
    }

    /**
     * Test request validation when the image is missing or invalid.
     */
    public function test_fails_validation_when_image_is_missing(): void
    {
        // Make POST request with an empty payload
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson(route('medical-imaging.analyze'), []);

        // Assert 422 Unprocessable Entity
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        // Verify key validations are listed
        $response->assertJsonValidationErrors(['eye_image']);
    }

    /**
     * Test request validation with an unsupported mime type.
     */
    public function test_fails_validation_with_invalid_mime_type(): void
    {
        // Create a fake text file instead of an image
        $fakeTxtFile = UploadedFile::fake()->create('document.txt', 100);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson(route('medical-imaging.analyze'), [
                'eye_image' => $fakeTxtFile,
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['eye_image']);
    }

    /**
     * Test request validation when the file size exceeds the 10 MB limit.
     */
    public function test_fails_validation_when_file_exceeds_size_limit(): void
    {
        // Create a fake image that exceeds 10 MB (e.g. 11 MB = 11,264 KB)
        $largeImage = UploadedFile::fake()->create('large_scan.jpg', 11264, 'image/jpeg');

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson(route('medical-imaging.analyze'), [
                'eye_image' => $largeImage,
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['eye_image']);
    }

    /**
     * Test service failure handling when FastAPI is down or returns error codes.
     */
    public function test_returns_service_unavailable_status_on_fastapi_server_error(): void
    {
        // 1. Fake the external server to return an internal server error status
        Http::fake([
            '*/predict' => Http::response('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR),
        ]);

        $fakeImage = UploadedFile::fake()->image('eye_scan.jpg');

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson(route('medical-imaging.analyze'), [
                'eye_image' => $fakeImage,
            ]);

        // 2. Assert HTTP status code is 503 Service Unavailable
        $response->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE);

        // 3. Verify user-friendly message payload
        $response->assertJson([
            'success' => false,
            'message' => 'The medical imaging service is currently unavailable. Please try again later.',
        ]);
    }

    /**
     * Test service failure handling when FastAPI returns an invalid payload.
     */
    public function test_returns_service_unavailable_status_on_invalid_fastapi_payload(): void
    {
        Http::fake([
            '*/predict' => Http::response([
                'prediction' => 'No Diabetic Retinopathy',
                // confidence intentionally omitted
            ], Response::HTTP_OK),
        ]);

        $fakeImage = UploadedFile::fake()->image('eye_scan.jpg');

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson(route('medical-imaging.analyze'), [
                'eye_image' => $fakeImage,
            ]);

        $response->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE);
        $response->assertJson([
            'success' => false,
            'message' => 'The medical imaging service is currently unavailable. Please try again later.',
        ]);
    }
}
