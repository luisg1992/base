<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoMotivoTriajeEmergenciaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'IdEstado' => [
                'required',
                'int',
            ],

            'Descripcion' => [
                'required',
                'string',
                'min:5',
                'max:50'
            ],
            'IdPrioridad' => [
                'required',
                'int'
            ],
            'IdServicio' => [
                'required',
                'int'
            ],

        ];
    }
}