<?php

namespace Modules\ProgramacionGeneral\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TurnoRequest extends FormRequest
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
            'Codigo' => [
                'required',
                'string',
                'min:3',
                'max:3'
            ],
            'Descripcion' => [
                'required',
                'string',
                'min:5',
                'max:50'
            ],
            'HoraInicio' => [
                'required',
                'string',
                'min:5',
                'max:5'
            ],
            'HoraFin' => [
                'required',
                'string',
                'min:5',
                'max:5'
            ],
            'IdTipoServicio' => [
                'required',
                'int'
            ], 
            'IdTipoTurnoRef' => [
                'required',
                'int'
            ]
        ];
    }
}
