<?php

namespace Modules\Configuracion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoFinanciamientoRequest extends FormRequest
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
            'idCajaTiposComprobante' => [
                'required',
                'integer'
            ],
            'idTipoConcepto' => [
                'required',
                'integer'
            ],
            'GeneraPago' => [
                'required',
                'integer'
            ],
            'tipoVenta' => [
                'required',
                'string'
            ],
            'esOficina' => 'nullable|boolean',
            'esSalida'  => 'nullable|boolean',
            'SeIngresPrecios'  => 'nullable|boolean',
            'EsFarmacia'  => 'nullable|boolean',
            'esFuenteFinanciamiento'  => 'nullable|boolean',
            'SeImprimeComprobante'  => 'nullable|boolean',

        ];
    }
}