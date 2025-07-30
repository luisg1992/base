<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecetaDosisUnidadMedidaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdRecetaDosisUnidadMedida' => $this->IdRecetaDosisUnidadMedida,
            'DosisUnidadMedida' => $this->DosisUnidadMedida,
        ];
    }
}
