<?php

namespace Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SetisisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdSetisis,
            'IdSetisis' => $this->IdSetisis,
            'Fecha' => $this->Fecha->format('d/m/Y'),
            'PaqueteNumero' => $this->PaqueteNumero,
        ];
    }
}
