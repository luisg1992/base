<?php

namespace Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicidadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdPublicidad,
            'Titulo' => $this->Titulo,
            'Descripcion' => $this->Descripcion,
            'TamanoLetra' => $this->TamanoLetra,
            'FondoColor' => $this->FondoColor,
            'PosicionVertical' => $this->PosicionVertical,
            'IdPublicidadTipo' => $this->IdPublicidadTipo,
            'Estado' => $this->Estado,
        ];
    }
}
