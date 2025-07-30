<?php

namespace Modules\Seguridad\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (int)$this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roles->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'name' => $row->name,
                ];
            }),
            'empleado_nombre' => $this->empleado->ApellidoPaterno . ' ' . $this->empleado->ApellidoMaterno . ', ' . $this->empleado->Nombres
        ];
    }
}
