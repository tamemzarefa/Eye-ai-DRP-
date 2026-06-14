<?php

declare(strict_types=1);

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyzeEyeImageRequest;
use App\Services\FastApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * MedicalImagingController
 *
 * Handles medical image analysis requests by delegating
 * file forwarding to the FastApiService and returning
 * a clean, typed JSON response.
 *
 * Namespace : App\Http\Controllers\AI
 * Route     : POST /api/v1/medical-imaging/analyze
 */
class MedicalImagingController extends Controller
{
    // ─── Constructor Injection ────────────────────────────────────

    /**
     * @param FastApiService $fastApiService  Centralised HTTP wrapper
     *                                         for the FastAPI AI backend.
     */
    public function __construct(
        protected readonly FastApiService $fastApiService,
    ) {}

    // ─── Actions ─────────────────────────────────────────────────

    /**
     * Analyze an uploaded eye image for Diabetic Retinopathy.
     *
     * Flow:
     *   1. Validate the uploaded `eye_image` via AnalyzeEyeImageRequest.
     *   2. Forward the image to the Python FastAPI `/predict` endpoint
     *      using multipart/form-data (field name: `file`).
     *   3. Parse the `prediction` and `confidence` fields from the
     *      FastAPI JSON response and relay them to the caller.
     *   4. On any network or server failure, log the error and return
     *      a user-friendly 503 response.
     *
     * @param  AnalyzeEyeImageRequest  $request  Auto-validated form request.
     * @return JsonResponse
     */
    public function analyzeEyeImage(AnalyzeEyeImageRequest $request): JsonResponse
    {
        // ── 1. Retrieve the validated uploaded file ───────────────
        /** @var \Illuminate\Http\UploadedFile $image */
        $image = $request->file('eye_image');

        try {
            // ── 2. Delegate to FastApiService ─────────────────────
            //
            // FastApiService::analyzeEyeImage() forwards the file to
            // POST http://127.0.0.1:8000/predict using Http::attach()
            // with the field name `file` as required by FastAPI.
            //
            /** @var array{prediction: string, confidence: float} $result */
            $result = $this->fastApiService->analyzeEyeImage($image);

            // ── 3. Return successful JSON response ────────────────
            return response()->json([
                'success'    => true,
                'message'    => 'Eye image analysis completed successfully.',
                'data'       => [
                    'prediction' => $result['prediction'],
                    'confidence' => $result['confidence'],
                ],
            ], JsonResponse::HTTP_OK);  // 200

        } catch (Throwable $e) {
            // ── 4a. Log full details internally (never expose to client)
            Log::error('MedicalImagingController: FastAPI analysis failed.', [
                'error'         => $e->getMessage(),
                'exception'     => get_class($e),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
                'original_name' => $image->getClientOriginalName(),
                'mime_type'     => $image->getMimeType(),
            ]);

            // ── 4b. Return a safe, user-friendly error response ───
            return response()->json([
                'success' => false,
                'message' => 'The medical imaging service is currently unavailable. '
                           . 'Please try again later.',
                'error'   => $e->getMessage(),
            ], JsonResponse::HTTP_SERVICE_UNAVAILABLE);  // 503
        }
    }
}
