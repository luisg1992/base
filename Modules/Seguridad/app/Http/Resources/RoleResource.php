<?php

namespace Modules\Seguridad\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permission_ids' => $this->permissions->pluck('id')->toArray(),
        ];
    }
}
