<?php

namespace Modules\Persona\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PacienteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = request()->input('id');
        return [
            'NroDocumento' => [
                'required',
                'string',
                'size:8',
                Rule::unique('Pacientes', 'NroDocumento')->ignore($id, 'IdPaciente'),
            ],
            'ApellidoPaterno' => [
                'required',
            ],
            'ApellidoMaterno' => [
                'required',
            ],
            'PrimerNombre' => [
                'required',
            ],
            'SegundoNombre' => [
                'required',
            ],
            'FechaNacimiento' => [
                'required',
            ],
            'Telefono' => [
                'required',
            ],
            'IdGradoInstruccion' => [
                'required',
            ],
            'IdReligion' => [
                'required',
            ],
            'IdIdioma' => [
                'required',
            ],
            'IdEtnia' => [
                'required',
            ],
            'DireccionDomicilio' => [
                'required',
            ],
            'IdDistritoProcedencia' => [
                'required',
            ]
        ];
    }
}
