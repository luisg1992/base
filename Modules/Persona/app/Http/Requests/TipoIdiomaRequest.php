<?php

namespace Modules\Persona\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoIdiomaRequest extends FormRequest
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
                'int',
                'min:1',
                'max:10000'
            ],
            'Lengua' => [
                'required',
                'string',
                'min:1',
                'max:100'
            ]
        ];
    }
}
