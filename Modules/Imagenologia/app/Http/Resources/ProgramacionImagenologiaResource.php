<?php

namespace Modules\Imagenologia\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramacionImagenologiaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdProgramacion,
            'idPuntoCarga' => $this->IdPuntoCarga,
            'fecha' => $this->Fecha,
            'Cupos' => $this->Cupos,
            'descripcion' => $this->Descripcion,
            'fechaReg' => $this->FechaReg
        ];
    }
}
