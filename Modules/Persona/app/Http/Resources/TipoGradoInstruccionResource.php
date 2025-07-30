<?php

namespace Modules\Persona\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoGradoInstruccionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdTipoEmpleado,
            'Descripcion' => $this->Descripcion,
            'sip2000' => $this->sip2000,
            'codigoReniec' => $this->codigoReniec,
            'IdTipoDIRIS' => $this->IdTipoDIRIS,
            'Estado' => $this->Estado
        ];
    }
}
