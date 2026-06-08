<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Patient — Core domain model for the Healthcare module.
 *
 * Implements Spatie HasMedia to manage patient profile images via
 * a single-file media collection named `profile_image`.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string|null $phone
 * @property \Carbon\Carbon $date_of_birth
 * @property string      $gender
 * @property string      $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Patient extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

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

    // ─── Spatie Media Collections ─────────────────────────────────

    /**
     * Register a single-file media collection for the patient's
     * profile image. Only one image is kept; uploading a new one
     * automatically replaces the previous file.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image')
             ->singleFile()                  // Keep only the latest upload
             ->acceptsMimeTypes([            // Whitelist image types
                 'image/jpeg',
                 'image/png',
                 'image/webp',
             ]);
    }

    /**
     * Register media conversions for responsive thumbnails.
     *
     * This creates a `thumb` conversion (150×150) so the frontend
     * can display lightweight avatars without loading the full image.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(150)
             ->height(150)
             ->sharpen(10)
             ->nonQueued();                   // Generate synchronously
    }
}
