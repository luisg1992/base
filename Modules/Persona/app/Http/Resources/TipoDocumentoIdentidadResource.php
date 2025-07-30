<?php

namespace Modules\Persona\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoDocumentoIdentidadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdDocIdentidad,
            'Descripcion' => $this->Descripcion,
            'Abreviatura' => $this->Abreviatura,
            'CodigoSUNASA' => $this->CodigoSUNASA,
            'CodigoHIS' => $this->CodigoHIS,
            'CodigoSIS' => $this->CodigoSIS,
            'Estado' => $this->Estado
        ];
    }
}
