<?php

namespace Modules\Persona\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoEmpleadoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'Estado' => [
                'required',
                'int',
            ],
            'Descripcion' => [
                'required',
                'string',
                'min:1',
                'max:100'
            ],
            'TipoEmpleadoHIS' => [
                'required',
                'int',
                'min:1',
                'max:100'
            ],
            'EsProgramado' => [
                'required',
                'int',
                'min:0',
                'max:100'
            ],
            'TipoEmpleadoSIS' => [
                'required',
                'int',
                'min:0',
                'max:100'
            ],
            'EsColegiatura' => [
                'required',
                'int',
                'min:0',
                'max:100'
            ],
            'TipoEspecialidadSIS' => [
                'required',
                'int',
                'min:0',
                'max:100'
            ]
        ];
    }
}
