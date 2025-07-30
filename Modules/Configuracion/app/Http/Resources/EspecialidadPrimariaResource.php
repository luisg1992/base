<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EspecialidadPrimariaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdEspecialidadPrimaria,
            'Nombre' => $this->Nombre,
            'Estado' => $this->Estado,
            'IdDepartamento' => $this->IdDepartamento, 
        ];
    }
}
