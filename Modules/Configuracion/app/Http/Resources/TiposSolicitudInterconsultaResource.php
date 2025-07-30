<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TiposSolicitudInterconsultaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdSolicitudInter' => $this->IdSolicitudInter,
            'Descripcion' => $this->Descripcion,
            'IdEstado' => $this->IdEstado,
        ];
    }
}
