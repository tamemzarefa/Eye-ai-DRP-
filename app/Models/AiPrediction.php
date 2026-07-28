<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * AiPrediction — The complete output of the FastAPI AI server
 * for a single retinal scan.
 *
 * Captures all three model outputs:
 *   1. Classification  → predicted_class + confidence_score
 *   2. Segmentation    → segmentation_mask_path (private disk)
 *   3. Heatmap         → heatmap_path (private disk)
 *
 * The raw FastAPI JSON response is preserved in `raw_response_log`
 * for complete medical auditing.
 *
 * @property int         $id
 * @property int         $retinal_scan_id
 * @property string      $predicted_class
 * @property float       $confidence_score
 * @property string|null $segmentation_mask_path
 * @property string|null $heatmap_path
 * @property array|null  $raw_response_log
 * @property \Carbon\Carbon|null $processed_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class AiPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'retinal_scan_id',
        'predicted_class',
        'confidence_score',
        'segmentation_mask_path',
        'heatmap_path',
        'raw_response_log',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'confidence_score'   => 'float',
            'raw_response_log'   => 'array',
            'processed_at'       => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────

    public function retinalScan(): BelongsTo
    {
        return $this->belongsTo(RetinalScan::class, 'retinal_scan_id');
    }

    // ─── Signed URL Helpers ───────────────────────────────────────

    /**
     * Generate a temporary signed URL for the segmentation mask image.
     */
    public function segmentationUrl(int $minutes = 60): ?string
    {
        if (! $this->segmentation_mask_path) {
            return null;
        }

        return Storage::disk('private')->temporaryUrl(
            $this->segmentation_mask_path,
            now()->addMinutes($minutes),
        );
    }

    /**
     * Generate a temporary signed URL for the heatmap image.
     */
    public function heatmapUrl(int $minutes = 60): ?string
    {
        if (! $this->heatmap_path) {
            return null;
        }

        return Storage::disk('private')->temporaryUrl(
            $this->heatmap_path,
            now()->addMinutes($minutes),
        );
    }
}
