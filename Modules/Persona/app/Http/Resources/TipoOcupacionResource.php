<?php

namespace Modules\Persona\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoOcupacionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdTipoOcupacion,
            'descripcion' => $this->descripcion,
            'lolcli' => $this->lolcli,

            'Estado' => $this->Estado
        ];
    }
}
