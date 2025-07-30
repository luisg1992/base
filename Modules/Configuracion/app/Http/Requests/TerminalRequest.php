<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TerminalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'IdUbicacionesFisicas' => [
                'nullable',
                'int',
            ],
            'Nombre' => [
                'required',
                'string',
                'min:4',
                'max:100'
            ],
            'IpAddress' => [
                'required',
                'string',
                'min:4',
                'max:100'
            ],
            'IpV6' => [ 
                'nullable', 
                'string' 
            ],
            'MacAddress' => [ 
                'string',
                'min:4',
                'max:100'
            ],
            'Estado' => [
                'required',
                'int',
            ],
        ];
    }
}
