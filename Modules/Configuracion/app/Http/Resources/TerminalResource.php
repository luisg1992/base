<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TerminalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->TerminalesId,
            'Nombre' => $this->Nombre,
            'IdUbicacionesFisicas' => $this->IdUbicacionesFisicas,
            'IpAddress' => $this->IpAddress,
            'IpV6' => $this->IpV6,
            'MacAddress' => $this->MacAddress,
            'Estado' => $this->Estado,
        ];
    }
}
