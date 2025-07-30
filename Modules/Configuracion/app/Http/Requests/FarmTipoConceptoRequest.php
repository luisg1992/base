<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FarmTipoConceptoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'Concepto' => [
                'required',
                'string',
                'min:4',
                'max:50',
            ],
            'codigoMINSA' => [
                'required',
                'string',
                'min:1',
                'max:2',
            ],
            'Estado' => [
                'required',
                'integer'
            ],
        ];
    }
}