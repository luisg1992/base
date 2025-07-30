<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TiposDestinoAtencionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'Codigo' => [
                'required',
                'string',
                'min:1',
                'max:1'
            ],
            'Descripcion' => [
                'required',
                'string',
                'min:4',
                'max:100'
            ],
            'IdTipoServicio' => [
                'required',
                'int',
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
