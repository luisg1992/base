<?php

namespace Modules\Persona\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoEmpleadoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdTipoEmpleado,
            'Descripcion' => $this->Descripcion,
            'TipoEmpleadoHIS' => $this->TipoEmpleadoHIS,
            'EsProgramado' => $this->EsProgramado,
            'TipoEmpleadoSIS' => $this->TipoEmpleadoSIS,
            'EsColegiatura' => $this->EsColegiatura,
            'TipoEspecialidadSIS' => $this->TipoEspecialidadSIS,
            'Estado' => $this->Estado
        ];
    }
}
