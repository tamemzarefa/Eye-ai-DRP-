<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * User — Represents an authenticated Doctor or Admin in the EyeAI system.
 *
 * Patients do NOT have user accounts. This model is exclusively for
 * clinical staff who log into the application.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $role          'admin' | 'doctor'
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Role Helpers ─────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    // ─── Relationships ────────────────────────────────────────────

    /**
     * Examinations performed by this doctor.
     */
    public function examinations(): HasMany
    {
        return $this->hasMany(EyeExamination::class, 'doctor_id');
    }

    /**
     * Diagnoses issued by this doctor.
     */
    public function diagnoses(): HasMany
    {
        return $this->hasMany(DoctorDiagnosis::class, 'doctor_id');
    }

    /**
     * AI copilot chat conversations started by this doctor.
     */
    public function chatConversations(): HasMany
    {
        return $this->hasMany(ChatConversation::class);
    }
}
