<?php

namespace Modules\Seguridad\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'modulo_id' => (int) $this->modulo_id,
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
        ];
    }
}
