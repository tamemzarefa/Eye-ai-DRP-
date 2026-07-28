<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AiPrediction;
use App\Models\RetinalScan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * AiInferenceOrchestrator — Coordinates AI analysis and persistence
 * for retinal scans.
 */
class AiInferenceOrchestrator
{
    public function __construct(protected readonly FastApiService $fastApiService) {}

    /**
     * Process a retinal scan through the FastAPI backend and persist
     * the resulting AI prediction history.
     */
    public function processScan(RetinalScan $scan): ?AiPrediction
    {
        $scan->update(['status' => 'processing']);

        try {
            $absolutePath = Storage::disk('private')->path($scan->original_scan_path);

            $result = $this->fastApiService->analyzeImage($absolutePath, [
                'scan_id'    => $scan->id,
                'eye'        => $scan->eye,
                'patient_id' => $scan->examination->patient_id,
            ]);

            $segmentationPath = $this->downloadAsset(
                $result['segmentation_mask_url'] ?? $result['segmentation_url'] ?? null,
                'ai/segmentation',
            );

            $heatmapPath = $this->downloadAsset(
                $result['heatmap_path'] ?? $result['heatmap_url'] ?? null,
                'ai/heatmap',
            );

            $prediction = AiPrediction::create([
                'retinal_scan_id'      => $scan->id,
                'predicted_class'      => $result['prediction'] ?? 'Negative',
                'confidence_score'     => isset($result['confidence']) ? (float) $result['confidence'] : 0.0,
                'segmentation_mask_path' => $segmentationPath,
                'heatmap_path'         => $heatmapPath,
                'raw_response_log'     => $result,
                'processed_at'         => now(),
            ]);

            $scan->update(['status' => 'completed']);

            return $prediction;
        } catch (Throwable $e) {
            Log::error('AiInferenceOrchestrator: Scan inference failed.', [
                'scan_id' => $scan->id,
                'error'   => $e->getMessage(),
            ]);

            $scan->update(['status' => 'failed']);

            return null;
        }
    }

    /**
     * Download a remote asset to private storage and return the saved path.
     */
    protected function downloadAsset(?string $url, string $folder): ?string
    {
        if (! $url) {
            return null;
        }

        try {
            $response = Http::timeout(30)->get($url);
            $response->throw();

            $filename = $this->filenameFromUrl($url);
            $path = trim($folder, '/') . '/' . $filename;

            Storage::disk('private')->put($path, $response->body());

            return $path;
        } catch (Throwable $e) {
            Log::warning('AiInferenceOrchestrator: Failed to download remote asset.', [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function filenameFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $filename = basename($path);

        if (! $filename) {
            return (string) uniqid('asset_', true);
        }

        return preg_replace('/[^A-Za-z0-9_\.-]+/', '_', $filename);
    }
}
