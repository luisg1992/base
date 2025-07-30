<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoGravedadAtencionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->IdTipoGravedad,
            'Descripcion'     => $this->Descripcion,
            'OrdenPrioridad'  => $this->OrdenPrioridad,
            'Estado'          => $this->Estado,
        ];
    }
}
