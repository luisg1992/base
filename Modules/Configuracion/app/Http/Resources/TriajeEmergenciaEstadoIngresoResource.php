<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TriajeEmergenciaEstadoIngresoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdEstadoIngreso,
            'IdFormaIngreso' => $this->IdFormaIngreso,
            'Descripcion' => $this->Descripcion, 
            'Estado' => $this->Estado
        ];
    }
}
