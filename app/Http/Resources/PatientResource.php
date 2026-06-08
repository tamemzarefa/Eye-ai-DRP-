<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * PatientResource — Standardises JSON output for the Patient model.
 *
 * Guarantees a consistent response structure for the React
 * frontend regardless of internal model changes.
 *
 * @mixin \App\Models\Patient
 */
class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'date_of_birth'  => $this->date_of_birth->toDateString(),
            'gender'         => $this->gender,
            'status'         => $this->status,

            // ── Spatie Media Library URLs ──────────────────────────
            'profile_image'  => $this->getFirstMediaUrl('profile_image'),
            'profile_thumb'  => $this->getFirstMediaUrl('profile_image', 'thumb'),

            // ── Timestamps ────────────────────────────────────────
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
