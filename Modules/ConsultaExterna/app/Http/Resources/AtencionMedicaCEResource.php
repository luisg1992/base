<?php

namespace Modules\ConsultaExterna\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AtencionMedicaCEResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdCuentaAtencion,
            'EstadosColaCitas' => $this->EstadosColaCitas,
            'HoraAtencionInicio' => $this->HoraAtencionInicio,
            'HoraAtencionFin' => $this->HoraAtencionFin,
            'Telefono' => $this->Telefono,
            'Paciente' => $this->Paciente,
            'HistoriaClinicaPaciente' => $this->HistoriaClinicaPaciente,
            'FechaAtencion' => $this->FechaAtencion,
            'HoraInicio' => $this->HoraInicio,
            'Servicio' => $this->Servicio,
            'Medico' => $this->Medico,
            'OrigenCita' => $this->OrigenCita,
            'FuentesFinanciamiento' => $this->FuentesFinanciamiento,
            'PagoCita' => $this->PagoCita,
            'Firmado' => $this->Firmado,
        ];
    }
}
