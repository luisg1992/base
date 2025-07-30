<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecetaClasificacionViaAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdCategoria' => $this->IdCategoria,
            'Categoria' => $this->Categoria,
            //'IdCategoria' => $this->IdCategoria,
        ];
    }
}
