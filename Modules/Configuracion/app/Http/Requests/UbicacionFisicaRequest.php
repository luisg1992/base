<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UbicacionFisicaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'IdEspecialidadPrimaria' => [
                'required',
                'int',
            ],
            'Estado' => [
                'required',
                'int',
            ],
            'Nombre' => [
                'required',
                'string',
                'min:4',
                'max:100'
            ],
            'TipoUbicacionFisica' => [
                'required',
                'string',
                'min:4',
                'max:100'
            ],
        ];
    }
}
