<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EspecialidadPrimariaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'Nombre' => [
                'required',
                'string',
                'min:4',
                'max:100'
            ],
            'IdDepartamento' => [
                'required',
                'int'
            ],
            'Estado' => [  
                'int'
            ], 
        ];
    }
}
