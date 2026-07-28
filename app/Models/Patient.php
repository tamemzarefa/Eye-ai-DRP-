<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Patient — Clinical registry entry.
 *
 * Patients are managed by doctors. They do NOT log in to the system.
 * Each patient can have multiple eye examinations over time.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string|null $phone
 * @property \Carbon\Carbon $date_of_birth
 * @property string      $gender   'male' | 'female' | 'other'
 * @property string      $status   'active' | 'inactive' | 'archived'
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Patient extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    // ─── Mass Assignment ──────────────────────────────────────────
    protected $fillable = [
        'name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'status',
    ];

    // ─── Attribute Casting ────────────────────────────────────────
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────

    /**
     * All eye examinations conducted for this patient.
     */
    public function examinations(): HasMany
    {
        return $this->hasMany(EyeExamination::class);
    }

    // ─── Spatie Media Collections ─────────────────────────────────

    /**
     * Profile image — optional avatar for quick identification in the UI.
     * Not used for retinal scans (those belong to RetinalScan records).
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image')
             ->singleFile()
             ->acceptsMimeTypes([
                 'image/jpeg',
                 'image/png',
                 'image/webp',
             ]);
    }

    /**
     * Lightweight thumbnail conversion for the patient listing view.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(150)
             ->height(150)
             ->sharpen(10)
             ->nonQueued();
    }
}
