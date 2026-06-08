<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdatePatientRequest — Validates incoming data for updating
 * an existing Patient record.
 *
 * Uses the route-model-bound patient ID to ignore the current
 * email when enforcing uniqueness.
 */
class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorised to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $patientId = $this->route('patient');

        return [
            'name'          => ['sometimes', 'string', 'max:255'],
            'email'         => ['sometimes', 'email', 'max:255', Rule::unique('patients')->ignore($patientId)],
            'phone'         => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['sometimes', 'date', 'before:today'],
            'gender'        => ['sometimes', Rule::in(['male', 'female', 'other'])],
            'status'        => ['sometimes', Rule::in(['active', 'inactive', 'archived'])],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Custom attribute names for cleaner validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'date_of_birth' => 'date of birth',
            'profile_image' => 'profile image',
        ];
    }
}
