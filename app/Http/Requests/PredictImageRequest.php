<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PredictImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'An image file is required for prediction.',
            'file.image'    => 'The uploaded file must be a valid image.',
            'file.mimes'    => 'Only JPEG and PNG images are accepted.',
            'file.max'      => 'The image file must not exceed 10 MB.',
        ];
    }
}
