<?php

namespace Modules\Emergencia\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TriajeEmergenciaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'Estado' => ['required', 'int'],
            'CodigoTriaje' => ['nullable', 'string'],
            'IdEmpleado' => ['nullable', 'integer'],
            'IdPaciente' => ['required', 'integer'],
            'IdFuenteFinanciamiento' => ['nullable', 'integer'],
            'IdTipoFinanciamiento' => ['nullable', 'integer'],
            'IdServicio' => ['nullable', 'integer'],
            'IdTipoGravedad' => ['nullable', 'integer'],
            'TriajeFecha' => ['nullable', 'string'],
            'TriajeHora' => ['nullable', 'string'],
            'TriajeAnios' => ['nullable', 'integer'],
            'TriajeMeses' => ['nullable', 'integer'],
            'TriajeDias' => ['nullable', 'integer'],
            'TriajeEscalaDolor' => ['nullable', 'string', 'max:2'],
            'TriajeSinRespiratorio' => ['nullable', 'string', 'max:2'],
            'TriajePresion' => ['nullable', 'string', 'max:13'],
            'TriajeFrecCardiaca' => ['nullable', 'integer'],
            'TriajeFrecRespiratoria' => ['nullable', 'integer'],
            'TriajeSaturacionOxigeno' => ['nullable', 'string', 'max:3'],
            'TriajeTemperatura' => ['nullable', 'string', 'max:6'],
            'TriajeTalla' => ['numeric'],
            'TriajePeso' => ['numeric'],
            'TriajeIMC' => ['numeric'],
            'TriajeObservacion' => ['nullable', 'string', 'max:200'],
            'Estacion' => ['nullable', 'string', 'max:50'],
            'IdMedicoTriaje' => ['nullable', 'integer'],
            'IdMedicoTopico' => ['nullable', 'integer'],
            'Acomp' => ['nullable', 'string', 'max:255'],
            'Diag_1' => ['nullable', 'integer'],
            'Diag_2' => ['nullable', 'integer'],
            'Diag_3' => ['nullable', 'integer'],
            'EstadoTriaje' => ['nullable', 'string', 'max:20'],
            'idCuentaAtencion' => ['nullable', 'integer'],
            'IdTipoDocTriaje' => ['nullable', 'integer'],
            'NroDocTriaje' => ['nullable', 'string', 'max:12'],
            'ApellidoPaternoTriaje' => ['nullable', 'string', 'max:40'],
            'ApellidoMaternoTriaje' => ['nullable', 'string', 'max:40'],
            'PrimerNombreTriaje' => ['nullable', 'string', 'max:40'],
            'FechaNacimientoTriaje' => ['nullable', 'date'],
            'IdMotivoIngreso' => ['nullable', 'integer'],
            'EstablecimientoSalud' => ['nullable', 'string', 'max:100'],
            'IdTipoTriajeDiferenciado' => ['nullable', 'integer'],
            'IdFormaIngreso' => ['nullable', 'integer'],
            'IdEstadoIngreso' => ['nullable', 'integer'],
            'Telefono' => ['nullable', 'string'],
            'Email' => ['nullable', 'string'],
            'HoraInicio' => ['required', 'string'],
            'HoraTermino' => ['required', 'string'],
        ];
    }
}
