<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImpresoraRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'IdTerminales' => [
                'required',
                'int',
            ],
            'Nombre' => [
                'required',
                'string',
                'min:4',
                'max:100'
            ], 
            'PorDefecto' => [
                'required' 
            ],
            'Formato' => [
                'required' 
            ],
            'Estado' => [
                'required',
                'int',
            ],
        ];
    }
}
