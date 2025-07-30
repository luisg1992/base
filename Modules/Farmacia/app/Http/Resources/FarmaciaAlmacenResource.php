<?php

namespace Modules\Farmacia\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmaciaAlmacenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'idAlmacen' => $this->idAlmacen,
            'descripcion' => $this->descripcion,
            'idTipoLocales' => $this->idTipoLocales,
            'idTipoSuministro' => $this->idTipoSuministro,
            'codigoSISMED' => $this->codigoSISMED,
            'idEstado' => $this->idEstado,
        ];
    }
}
