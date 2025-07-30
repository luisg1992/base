<?php

namespace Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuloResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ModuloId,
            'Etiqueta' => $this->Etiqueta,
            'Subtitulo' => $this->Subtitulo,
            'Descripcion' => $this->Descripcion,
            'Icono' => $this->Icono,
            'Url' => $this->Url,
            'EsAccesoDirecto' => $this->EsAccesoDirecto,
            'EstaBloqueado' => $this->EstaBloqueado,
            'Estado' => $this->Estado,
            'Orden' => $this->Orden,
            'ModuloPadreId' => $this->ModuloPadreId,
            'padre' => $this->whenLoaded('padre', function () {
                return [
                    'ModuloId' => $this->padre->ModuloId,
                    'Etiqueta' => $this->padre->Etiqueta,
                    'Icono' => $this->padre->Icono,
                ];
            }),
        ];
    }
}
