<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'patient_name'   => $this->patient_name,
            'patient_age'    => $this->patient_age,
            'patient_id'     => $this->patient_id,
            'classification' => $this->classification,
            'confidence'     => $this->confidence,
            'doctor_notes'   => $this->doctor_notes,
            'feedback'       => $this->feedback,
            'image_preview'  => $this->image_preview,
            'date'           => $this->date,
            'created_at'     => $this->created_at?->toDateTimeString(),
            'updated_at'     => $this->updated_at?->toDateTimeString(),
        ];
    }
}
