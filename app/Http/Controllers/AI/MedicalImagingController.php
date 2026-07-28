<?php

declare(strict_types=1);

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyzeEyeImageRequest;
use App\Services\EyeAiApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * MedicalImagingController
 *
 * Handles medical image analysis requests by delegating
 * file forwarding to the EyeAiApiService and returning
 * a clean, typed JSON response.
 *
 * Namespace : App\Http\Controllers\AI
 * Route     : POST /api/v1/medical-imaging/analyze
 */
class MedicalImagingController extends Controller
{
    // ─── Constructor Injection ────────────────────────────────────

    /**
     * @param EyeAiApiService $eyeAiApiService Centralised HTTP wrapper for FastAPI AI backend.
     */
    public function __construct(
        protected readonly EyeAiApiService $eyeAiApiService,
    ) {}

    // ─── Actions ─────────────────────────────────────────────────

    /**
     * Analyze an uploaded eye image for Diabetic Retinopathy.
     *
     * @param  AnalyzeEyeImageRequest  $request  Auto-validated form request.
     * @return JsonResponse
     */
    public function analyzeEyeImage(AnalyzeEyeImageRequest $request): JsonResponse
    {
        // 1. رفع مهلة تنفيذ السكربت إلى 120 ثانية لتجنب مشكلة الـ Timeout أثناء المعالجة
        set_time_limit(320);

        /** @var \Illuminate\Http\UploadedFile $image */
        $image = $request->file('eye_image');

        try {
            // 2. إرسال الصورة مباشرة إلى FastAPI عبر EyeAiApiService
            $result = $this->eyeAiApiService->predict($image);

            // 3. إرجاع نتيجة التشخيص والـ Confidence
           return response()->json([
            'success' => true,
            'message' => 'Eye image analysis completed successfully.',
            'data'    => $result, // إرجاع مصفوفة الاستجابة كاملة (Epicrisis, Procedere, الخ)
        ], JsonResponse::HTTP_OK);

        } catch (Throwable $e) {
            // 4. تسجيل الخطأ في اللوغ وإرجاع استجابة آمنة للمستخدم
            Log::error('MedicalImagingController: FastAPI analysis failed.', [
                'error'         => $e->getMessage(),
                'exception'     => get_class($e),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
                'original_name' => $image->getClientOriginalName(),
                'mime_type'     => $image->getClientMimeType(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'The medical imaging service is currently unavailable. Please try again later.',
                'error'   => $e->getMessage(),
            ], JsonResponse::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}