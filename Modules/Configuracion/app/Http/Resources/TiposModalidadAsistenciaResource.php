<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TiposModalidadAsistenciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdModalidad' => $this->IdModalidad,
            'Descripcion' => $this->Descripcion,
            'IdEstado' => $this->IdEstado,
        ];
    }
}
