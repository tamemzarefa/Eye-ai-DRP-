<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'يجب إدخال سؤال للنقاش مع خدمة AI.',
            'question.string'   => 'يجب أن يكون السؤال نصاً صالحاً.',
            'question.max'      => 'طول السؤال يجب ألا يتجاوز 1000 حرف.',
        ];
    }
}
