<?php

namespace Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioRefConResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdUsuarioRefCon' => $this->IdUsuarioRefCon,
            'Nombres' => $this->Nombres,
            'Usuario' => $this->Usuario,
            'Clave' => $this->Clave,
            'Estado' => $this->Estado,
        ];
    }
}
