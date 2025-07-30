<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ParametroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Tipo' => 'required|string|max:20',
            'Codigo' => 'required|string|max:20',
            'Descripcion' => 'required|string',
            'ValorInt' => 'nullable|integer',
            'ValorFloat' => 'nullable|numeric',
        ];
    }
}
