<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecetaFrecuenciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdRecetaFrecuencia' => $this->IdRecetaFrecuencia,
            'Descripcion' => $this->Descripcion,
            'Orden' => $this->Orden,
        ];
    }
}
