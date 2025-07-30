<?php

namespace Modules\Persona\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoEtniaRequest extends FormRequest
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
            'id_etnia' => [
                'required',
                'int',
                'min:0',
                'max:1000'
            ],
            'desetni' => [
                'required',
                'string',
                'min:1',
                'max:100'
            ],
            'codgen' => [
                'required',
                'int',
                'min:0',
                'max:1000'
            ],
            'etnias' => [
                'required',
                'string',
                'min:1',
                'max:100'
            ]
        ];
    }
}
