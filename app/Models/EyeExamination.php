<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * EyeExamination — A single clinical visit/session.
 *
 * One examination belongs to one patient and is performed by one doctor.
 * It contains one or more retinal scans (left/right eye) and resolves
 * into one doctor diagnosis.
 *
 * @property int         $id
 * @property int         $patient_id
 * @property int         $doctor_id
 * @property \Carbon\Carbon $examination_date
 * @property string|null $clinical_notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class EyeExamination extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'examination_date',
        'clinical_notes',
    ];

    protected function casts(): array
    {
        return [
            'examination_date' => 'date',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /** All retinal scans captured during this visit. */
    public function retinalScans(): HasMany
    {
        return $this->hasMany(RetinalScan::class, 'examination_id');
    }

    /** The doctor's final diagnosis for this examination. */
    public function diagnosis(): HasOne
    {
        return $this->hasOne(DoctorDiagnosis::class, 'examination_id');
    }
}
