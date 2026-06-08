<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StorePatientRequest — Validates incoming data for creating
 * a new Patient record.
 *
 * Centralises validation so the controller remains thin and
 * the rules are reusable / testable in isolation.
 */
class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorised to make this request.
     *
     * For a public API this returns true. Wire up policies
     * or gates here when auth is enforced.
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
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', 'unique:patients,email'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender'        => ['required', Rule::in(['male', 'female', 'other'])],
            'status'        => ['sometimes', Rule::in(['active', 'inactive', 'archived'])],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'], // 5 MB
            'run_diagnostic'=> ['sometimes', 'boolean'],
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
            'date_of_birth'  => 'date of birth',
            'profile_image'  => 'profile image',
            'run_diagnostic' => 'AI diagnostic flag',
        ];
    }
}
