<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramaInstitucionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdInstitucion' => $this->IdInstitucion,
            'Descripcion' => $this->Descripcion,
            'Estado' => $this->Estado,
        ];
    }
}
