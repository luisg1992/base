<?php

namespace Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmsCelularResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdSmsCelular,
            'Url' => $this->Url,
            'Token' => $this->Token,
            'Usuario' => $this->Usuario,
            'Celular' => $this->Celular,
            'Estado' => $this->Estado,
        ];
    }
}
