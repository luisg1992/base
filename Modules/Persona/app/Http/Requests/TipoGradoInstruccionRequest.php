<?php

namespace Modules\Persona\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoGradoInstruccionRequest extends FormRequest
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
            'sip2000' => [
                'required',
                'int',
                'min:1',
                'max:100'
            ],
            'codigoReniec' => [
                'required',
                'int',
                'min:1',
                'max:100'
            ],
            'IdTipoDIRIS' => [
                'required',
                'int',
                'min:1',
                'max:100'
            ]
        ];
    }
}
