<?php

namespace Modules\Emergencia\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class TriajeEmergenciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $diagnosticos = DB::select('EXEC WebS_TriajeEmergenciaHospitalizacion_Consultar_Diagnostico @IdTriajeEmergencia = ?',
            [$this->IdTriajeEmergencia]
        );

        return [
            'id' => $this->IdTriajeEmergencia,
            'CodigoTriaje' => $this->CodigoTriaje,
            'IdEmpleado' => $this->IdEmpleado,
            'IdPaciente' => $this->IdPaciente,
            'IdFuenteFinanciamiento' => $this->IdFuenteFinanciamiento,
            'IdTipoFinanciamiento' => $this->IdTipoFinanciamiento,
            'IdServicio' => $this->IdServicio,
            'IdTipoGravedad' => $this->IdTipoGravedad,
            'TriajeFecha' => $this->TriajeFecha,
            'TriajeHora' => $this->TriajeHora,
            'TriajeAnios' => $this->TriajeAnios,
            'TriajeMeses' => $this->TriajeMeses,
            'TriajeDias' => $this->TriajeDias,
            'TriajeEscalaDolor' => $this->TriajeEscalaDolor,
            'TriajeSinRespiratorio' => $this->TriajeSinRespiratorio,
            'TriajePresion' => $this->TriajePresion,
            'TriajeFrecCardiaca' => $this->TriajeFrecCardiaca,
            'TriajeFrecRespiratoria' => $this->TriajeFrecRespiratoria,
            'TriajeSaturacionOxigeno' => $this->TriajeSaturacionOxigeno,
            'TriajeTemperatura' => $this->TriajeTemperatura,
            'TriajeTalla' => round(floatval($this->TriajeTalla), 2),
            'TriajePeso' => round(floatval($this->TriajePeso), 2),
            'TriajeIMC' => round(floatval($this->TriajeIMC), 2),
            'TriajeObservacion' => $this->TriajeObservacion,
            'Estacion' => $this->Estacion,
            'IdMedicoTriaje' => $this->IdMedicoTriaje,
            'IdMedicoTopico' => $this->IdMedicoTopico,
            'Acomp' => $this->Acomp,
            'Diag_1' => $this->Diag_1,
            'Diag_2' => $this->Diag_2,
            'Diag_3' => $this->Diag_3,
            'EstadoTriaje' => $this->EstadoTriaje,
            'idCuentaAtencion' => $this->idCuentaAtencion,
            'IdTipoDocTriaje' => $this->IdTipoDocTriaje,
            'NroDocTriaje' => $this->NroDocTriaje,
            'ApellidoPaternoTriaje' => $this->ApellidoPaternoTriaje,
            'ApellidoMaternoTriaje' => $this->ApellidoMaternoTriaje,
            'PrimerNombreTriaje' => $this->PrimerNombreTriaje,
            'FechaNacimientoTriaje' => $this->FechaNacimientoTriaje,
            'IdMotivoIngreso' => $this->IdMotivoIngreso,
            'EstablecimientoSalud' => $this->EstablecimientoSalud,
            'IdTipoTriajeDiferenciado' => $this->IdTipoTriajeDiferenciado,
            'Estado' => $this->Estado,
            'IdFormaIngreso' => $this->IdFormaIngreso,
            'IdEstadoIngreso' => $this->IdEstadoIngreso,
            'HoraInicio' => $this->HoraInicio,
            'HoraTermino' => $this->HoraTermino,
            'diagnosticos' => collect($diagnosticos)->transform(function ($row) {
                return [
                    'IdDiagnostico' => (int)$row->IdDiagnostico,
                    'Descripcion' => $row->Diagnostico,
                ];
            }),
        ];
    }
}
