<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmsCelularRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Url' => 'required|string',
            'Token' => 'required|string',
            'Usuario' => 'required|string',
            'Celular' => 'required|string',
        ];
    }
}
