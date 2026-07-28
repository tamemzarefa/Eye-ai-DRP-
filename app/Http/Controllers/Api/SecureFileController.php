<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiPrediction;
use App\Models\RetinalScan;
use App\Services\SecureFileService;
use Illuminate\Http\JsonResponse;

class SecureFileController extends Controller
{
    public function __construct(protected readonly SecureFileService $secureFileService) {}

    public function previewScan(RetinalScan $scan): JsonResponse
    {
        $this->authorize('view', $scan->examination);

        return response()->json([
            'eye'           => $scan->eye,
            'status'        => $scan->status,
            'temporary_url' => $this->secureFileService->temporaryUrlForScan($scan),
        ]);
    }

    public function asset(AiPrediction $prediction, string $asset): JsonResponse
    {
        $this->authorize('view', $prediction->retinalScan->examination);

        return response()->json([
            'asset' => $asset,
            'url'   => match ($asset) {
                'segmentation' => $this->secureFileService->temporaryUrlForSegmentation($prediction),
                'heatmap'      => $this->secureFileService->temporaryUrlForHeatmap($prediction),
                default        => null,
            },
        ]);
    }
}
