<?php

namespace Modules\Facturacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartaGarantiaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'IdFuenteFinanciamiento' => [
                'required',
                'int',
            ],
            'IdTipoFinanciamiento' => [
                'required',
                'int',
            ],
            'IdPaciente' => [
                'required',
                'int',
            ],
            'FechaInicio' => [
                'required',
                'date',
            ],
            'FechaFinal' => [
                'required',
                'date',
            ],
            'NumeroPlaca' => [
                'string',
            ],
            'NumeroPoliza' => [
                'string',
            ],
            'NumeroSiniestro' => [
                'string',
            ],
            'Estado' => [
                'required',
                'int',
            ]
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
