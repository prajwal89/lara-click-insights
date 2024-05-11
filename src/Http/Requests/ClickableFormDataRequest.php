<?php

namespace Prajwal89\LaraClickInsights\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClickableFormDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clickables' => 'array',
            'clickables.*' => 'string',
            'clicked_on' => 'sometimes|nullable|string',
        ];
    }
}
