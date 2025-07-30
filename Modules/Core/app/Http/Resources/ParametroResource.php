<?php

namespace Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParametroResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdParametro,
            'Tipo' => $this->Tipo,
            'Codigo' => $this->Codigo,
            'ValorTexto' => $this->ValorTexto,
            'ValorInt' => $this->ValorInt,
            'ValorFloat' => $this->ValorFloat,
            'Descripcion' => $this->Descripcion,
            'Grupo' => $this->Grupo,
        ];
    }
}
