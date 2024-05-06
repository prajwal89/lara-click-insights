<?php

namespace Prajwal89\LaraClickInsights\Requests;

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
            'clickables' => 'required|array',
            'clickables.*' => 'required|string',
            'clicked_on' => 'sometimes|nullable|string',
        ];
    }
}
