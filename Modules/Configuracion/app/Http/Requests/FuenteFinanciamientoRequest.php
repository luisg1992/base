<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FuenteFinanciamientoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'idEstado' => [
                'required',
                'int',
            ],
            'Descripcion' => [
                'required',
                'string',
                'min:5',
                'max:50'
            ],
            'idTipoConceptoFarmacia' => [
                'required',
                'int'
            ],
            'UtilizadoEn' => [
                'required',
                'int'
            ],
            'CodigoFuenteFinanciamientoSEM' => [
                'required',
                'int'
            ],
            'idAreaTramitaSeguros' => [
                'required',
                'int'
            ],

            'CodigoHIS' => [
                'required',
                'int'
            ],

            'idTipoFinanciador' => [
                'required',
                'int'
            ],
            'codigo' => [
                'required',
                'int'
            ],
            'EsUsadoEnCaja' => 'boolean',
            'fuentefinanciamiento' => 'sometimes|array',
            'fuentefinanciamiento.*.IdTipoFinanciamiento' => 'required|integer',
            'IdTipoFinanciamiento' => 'nullable|int',
            'Orden' => 'nullable|int',
        ];
    }
}