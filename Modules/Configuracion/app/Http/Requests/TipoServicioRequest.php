<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoServicioRequest extends FormRequest
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
                'min:4',
                'max:100'
            ]
        ];
    }
}
