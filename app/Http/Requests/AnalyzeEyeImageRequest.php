<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * AnalyzeEyeImageRequest
 *
 * Validates the incoming multipart/form-data payload for the
 * eye image analysis endpoint.
 *
 * Rules:
 *   - `eye_image` is required.
 *   - Must be a valid image file (jpeg, png, jpg).
 *   - Maximum file size is 10 MB (10 240 KB).
 */
class AnalyzeEyeImageRequest extends FormRequest
{
    // ─── Authorization ────────────────────────────────────────────

    /**
     * Determine if the authenticated user is allowed to make
     * this request.
     *
     * Set to `true` for now; swap in a Gate/Policy check when
     * role-based access control is introduced.
     */
    public function authorize(): bool
    {
        return true;
    }

    // ─── Validation Rules ─────────────────────────────────────────

    /**
     * Validation rules applied to the incoming request data.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'eye_image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:10240', // 10 MB → Laravel measures max in kilobytes
            ],
        ];
    }

    // ─── Custom Messages ──────────────────────────────────────────

    /**
     * Human-readable error messages returned to the API consumer.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'eye_image.required' => 'An eye image file is required for analysis.',
            'eye_image.image'    => 'The uploaded file must be a valid image.',
            'eye_image.mimes'    => 'Only JPEG and PNG images are accepted.',
            'eye_image.max'      => 'The image file must not exceed 10 MB.',
        ];
    }
}
