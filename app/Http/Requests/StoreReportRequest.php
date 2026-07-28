<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_name'  => ['required', 'string', 'max:255'],
            'patient_age'   => ['nullable', 'string', 'max:100'],
            'patient_id'    => ['required', 'string', 'max:100'],
            'classification'=> ['required', 'string', 'max:255'],
            'confidence'    => ['required', 'numeric', 'min:0', 'max:100'],
            'doctor_notes'  => ['nullable', 'string'],
            'feedback'      => ['nullable', 'string', 'in:agree,disagree,unsure'],
            'image_preview' => ['nullable', 'string', 'max:2048'],
            'date'          => ['required', 'string', 'max:255'],
        ];
    }
}
