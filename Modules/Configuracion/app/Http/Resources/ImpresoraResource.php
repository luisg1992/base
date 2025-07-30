<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImpresoraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ImpresorasId,
            'Nombre' => $this->Nombre,
            'IdTerminales' => $this->IdTerminales,
            'Estado' => $this->Estado,
            'PorDefecto' => $this->PorDefecto , 
            'Formato' => $this->Formato
        ];
    }
}
