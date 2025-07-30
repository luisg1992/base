<?php

namespace Modules\Persona\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoDocumentoIdentidadRequest extends FormRequest
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
            'Abreviatura' => [
                'required',
                'string',
                'min:1',
                'max:10'
            ],
            'CodigoSUNASA' => [
                'required',
                'int',
                'min:0',
                'max:100'
            ],
            'CodigoHIS' => [
                'required',
                'int',
                'min:0',
                'max:100'
            ],
            'CodigoSIS' => [
                'required',
                'int',
                'min:0',
                'max:100'
            ]
        ];
    }
}
