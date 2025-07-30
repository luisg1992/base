<?php

namespace Modules\Facturacion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpedienteJudicialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdProgramaExpedienteJudicial' => $this->IdProgramaExpedienteJudicial,
            'IdFuenteFinanciamiento' => $this->IdFuenteFinanciamiento,
            'IdTipoFinanciamiento' => $this->IdTipoFinanciamiento,
            'IdPaciente' => $this->IdPaciente,
            'IdProgramaInstitucion' => $this->IdProgramaInstitucion,
            'IdTipoDocumento' => $this->IdTipoDocumento,
            'IdTipoServicio' => $this->IdTipoServicio,
            'IdEspecialidad' => $this->IdEspecialidad,
            'NumeroDocumento' => $this->NumeroDocumento,
            'FechaDocumento' => $this->FechaDocumento,
            'FechaVencimiento' => $this->FechaVencimiento,
            'NumeroExpedienteTramiteDocumentario' => $this->NumeroExpedienteTramiteDocumentario,
            'Estado' => $this->Estado,
            'FechaRegistro' => $this->FechaRegistro,
            'IdEmpleadoRegistra' => $this->IdEmpleadoRegistra,

            'Paciente' => $this->paciente ?? null,
        ];
    }
}
