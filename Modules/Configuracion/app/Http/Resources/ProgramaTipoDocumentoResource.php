<?php

namespace Modules\Configuracion\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramaTipoDocumentoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'IdTipoDocumento' => $this->IdTipoDocumento,
            'Descripcion' => $this->Descripcion,
            'Estado' => $this->Estado,
        ];
    }
}
