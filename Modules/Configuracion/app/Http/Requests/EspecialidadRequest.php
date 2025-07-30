<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EspecialidadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'Nombre' => [
                'required',
                'string',
                'min:4',
                'max:100'
            ],
            'IdEspecialidadPrimaria' => [
                'required',
                'int'
            ],
            'IdDepartamento' => [
                'required',
                'int'
            ],
            'IdEstado' => [
                'nullable', 
                'int'
            ],
            'TiempoPromedioAtencion' => [
                'required',
                'int'
            ],
            'IdProductoConsulta' => [
                'required'
            ]
        ];
    }
}
