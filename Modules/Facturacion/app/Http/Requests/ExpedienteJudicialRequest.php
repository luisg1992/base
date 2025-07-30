<?php

namespace Modules\Facturacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpedienteJudicialRequest extends FormRequest
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
            'IdProgramaInstitucion' => [
                'required',
                'int',
            ],
            'IdTipoDocumento' => [
                'required',
                'int',
            ],
            'IdTipoServicio' => [
                'required',
                'int',
            ],
            'IdEspecialidad' => [
                'required',
                'int',
            ],
            'NumeroDocumento' => [
                'nullable',
                'string',
            ],
            'FechaDocumento' => [
                'nullable',
                'date',
            ],
            'FechaVencimiento' => [
                'nullable',
                'date',
            ],
            'NumeroExpedienteTramiteDocumentario' => [
                'nullable',
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
