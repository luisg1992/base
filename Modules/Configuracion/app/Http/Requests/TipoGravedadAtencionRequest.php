<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoGravedadAtencionRequest extends FormRequest
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
            'OrdenPrioridad' => [
                'required',
                'integer',
                'min:1'
            ],
            'Estado' => [
                'required',
                'integer'
            ],
        ];
    }
}
