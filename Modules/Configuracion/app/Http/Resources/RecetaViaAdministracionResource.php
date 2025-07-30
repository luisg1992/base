<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecetaViaAdministracionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdViaAdministracion' => $this->IdViaAdministracion,
            'Descripcion' => $this->Descripcion,
            'IdCategoria' => $this->IdCategoria,
        ];
    }
}
