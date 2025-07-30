<?php

namespace Modules\Persona\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoOcupacionRequest extends FormRequest
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
            'descripcion' => [
                'required',
                'string',
                'min:1',
                'max:100'
            ],
            'lolcli' => [
                'required',
                'string',
                'min:1',
                'max:100'
            ]

        ];
    }
}
