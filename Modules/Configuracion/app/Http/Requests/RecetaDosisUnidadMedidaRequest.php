<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecetaDosisUnidadMedidaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'DosisUnidadMedida' => [
                'required',
                'string',
                'min:1',
                'max:50'
            ],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
