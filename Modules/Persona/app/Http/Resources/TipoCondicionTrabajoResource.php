<?php

namespace Modules\Persona\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoCondicionTrabajoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdCondicionTrabajo,
            'Descripcion' => $this->Descripcion,
            'Estado' => $this->Estado
        ];
    }
}
