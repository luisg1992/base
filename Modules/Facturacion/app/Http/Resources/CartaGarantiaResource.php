<?php

namespace Modules\Facturacion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartaGarantiaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdCartaGarantia' => $this->IdCartaGarantia,
            'IdFuenteFinanciamiento' => $this->IdFuenteFinanciamiento,
            'IdTipoFinanciamiento' => $this->IdTipoFinanciamiento,
            'IdPaciente' => $this->IdPaciente,
            'FechaInicio' => $this->FechaInicio,
            'FechaFinal' => $this->FechaFinal,
            'NumeroPlaca' => $this->NumeroPlaca,
            'NumeroPoliza' => $this->NumeroPoliza,
            'NumeroSiniestro' => $this->NumeroSiniestro,
            'Estado' => $this->Estado,
            'FechaRegistro' => $this->FechaRegistro,
            'IdEmpleadoRegistra' => $this->IdEmpleadoRegistra,

            'Paciente' => $this->paciente ?? null,
        ];
    }
}
