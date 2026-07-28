<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

/**
 * RetinalScan — An uploaded fundus photograph for one eye.
 *
 * Belongs to an EyeExamination. Stored on the private disk.
 * All public URL generation is handled through SecureFileService
 * using temporary signed URLs.
 *
 * @property int         $id
 * @property int         $examination_id
 * @property string      $eye          'left' | 'right'
 * @property string      $original_scan_path
 * @property string      $status       'pending' | 'processing' | 'completed' | 'failed'
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class RetinalScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'examination_id',
        'eye',
        'original_scan_path',
        'status',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function examination(): BelongsTo
    {
        return $this->belongsTo(EyeExamination::class, 'examination_id');
    }

    /** The AI prediction generated for this scan. */
    public function prediction(): HasOne
    {
        return $this->hasOne(AiPrediction::class, 'retinal_scan_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Generate a temporary signed URL for the original scan.
     * Defaults to 60 minutes of validity.
     */
    public function temporaryUrl(int $minutes = 60): string
    {
        return Storage::disk('private')->temporaryUrl(
            $this->original_scan_path,
            now()->addMinutes($minutes),
        );
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
