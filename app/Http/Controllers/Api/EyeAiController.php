<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatRequest;
use App\Http\Requests\PredictImageRequest;
use App\Http\Requests\SegmentImageRequest;
use App\Services\EyeAiApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class EyeAiController extends Controller
{
    public function __construct(protected readonly EyeAiApiService $eyeAiApiService)
    {
    }

    public function predict(PredictImageRequest $request): JsonResponse
    {
        try {
            $result = $this->eyeAiApiService->predict($request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Prediction from FastAPI received successfully.',
                'data'    => $result,
            ], JsonResponse::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('EyeAiController: FastAPI /predict request failed.', [
                'error'     => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'The AI prediction service is unavailable. Please try again later.',
            ], JsonResponse::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    public function segment(SegmentImageRequest $request): JsonResponse
    {
        try {
            $result = $this->eyeAiApiService->segment($request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Segmentation result from FastAPI received successfully.',
                'data'    => $result,
            ], JsonResponse::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('EyeAiController: FastAPI /segment request failed.', [
                'error'     => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'The AI segmentation service is unavailable. Please try again later.',
            ], JsonResponse::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    public function chat(ChatRequest $request): JsonResponse
    {
        try {
            $result = $this->eyeAiApiService->chat($request->input('question'));

            return response()->json([
                'success' => true,
                'message' => 'Chat response from FastAPI received successfully.',
                'data'    => $result,
            ], JsonResponse::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('EyeAiController: FastAPI /chat request failed.', [
                'error'     => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'The AI chat service is unavailable. Please try again later.',
            ], JsonResponse::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
