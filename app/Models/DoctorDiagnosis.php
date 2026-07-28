<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DoctorDiagnosis — The authoritative human clinical decision.
 *
 * The doctor reviews the AI prediction(s) for an examination and
 * records their final decision. They may agree, disagree, or remain
 * unsure about the AI output.
 *
 * One examination → one diagnosis record.
 *
 * @property int         $id
 * @property int         $examination_id
 * @property int         $doctor_id
 * @property string      $final_diagnosis
 * @property string|null $doctor_notes
 * @property string|null $ai_feedback    'agree' | 'disagree' | 'unsure'
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class DoctorDiagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'examination_id',
        'doctor_id',
        'final_diagnosis',
        'doctor_notes',
        'ai_feedback',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function examination(): BelongsTo
    {
        return $this->belongsTo(EyeExamination::class, 'examination_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────

    public function agreedWithAi(): bool
    {
        return $this->ai_feedback === 'agree';
    }

    public function disagreedWithAi(): bool
    {
        return $this->ai_feedback === 'disagree';
    }
}
