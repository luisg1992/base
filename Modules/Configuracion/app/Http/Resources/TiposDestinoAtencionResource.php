<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TiposDestinoAtencionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdDestinoAtencion' => $this->IdDestinoAtencion,
            'Codigo' => $this->Codigo,
            'Descripcion' => $this->Descripcion,
            'IdTipoServicio' => $this->IdTipoServicio,
            'TipoServicioHosp' => $this->TipoServicioHosp,
            'DestinoSEM' => $this->DestinoSEM,
            'id_destinoAseguradoSIS' => $this->id_destinoAseguradoSIS,
        ];
    }
}
