<?php

namespace App\Services;

use App\Models\AiPrediction;
use App\Models\RetinalScan;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class SecureFileService
{
    public function temporaryUrlForScan(RetinalScan $scan, int $minutes = 60): string
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('private');

        return $disk->temporaryUrl(
            $scan->original_scan_path,
            now()->addMinutes($minutes),
        );
    }

    public function temporaryUrlForSegmentation(AiPrediction $prediction, int $minutes = 60): ?string
    {
        if (! $prediction->segmentation_mask_path) {
            return null;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('private');

        return $disk->temporaryUrl(
            $prediction->segmentation_mask_path,
            now()->addMinutes($minutes),
        );
    }

    public function temporaryUrlForHeatmap(AiPrediction $prediction, int $minutes = 60): ?string
    {
        if (! $prediction->heatmap_path) {
            return null;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('private');

        return $disk->temporaryUrl(
            $prediction->heatmap_path,
            now()->addMinutes($minutes),
        );
    }
}
