<?php


namespace Modules\Configuracion\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmTipoConceptoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->idTipoConcepto,
            'codigoMINSA' => $this->codigoMINSA,
            'Concepto' => $this->Concepto,
            'Estado' => $this->Estado,
        ];
    }
}