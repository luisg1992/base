<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CajaTipoComprobanteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'Descripcion' => [
                'required',
                'string',
                'min:4',
                'max:50',
            ],

            'Estado' => [
                'required',
                'integer'
            ],
        ];
    }
}