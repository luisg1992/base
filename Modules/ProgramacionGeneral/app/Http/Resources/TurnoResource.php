<?php

namespace Modules\ProgramacionGeneral\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TurnoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdTurno,
            'Codigo' => $this->Codigo,
            'Descripcion' => $this->Descripcion,
            'HoraInicio' => $this->HoraInicio,
            'HoraFin' => $this->HoraFin,
            'IdTipoServicio' => $this->IdTipoServicio,
            'IdEspecialidad' => $this->IdEspecialidad,
            'IdTipoTurnoRef' => $this->IdTipoTurnoRef,
            'Estado' => $this->Estado
        ];
    }
}
