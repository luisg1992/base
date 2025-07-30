<?php


namespace Modules\Configuracion\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoFinanciamientoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdTipoFinanciamiento,
            'Descripcion' => $this->Descripcion,
            'esOficina' => $this->esOficina,
            'esSalida' => $this->esSalida,
            'SeIngresPrecios' => $this->SeIngresPrecios,
            'EsFarmacia' => $this->EsFarmacia,
            'idCajaTiposComprobante' => $this->idCajaTiposComprobante,
            'SeImprimeComprobante' => $this->SeImprimeComprobante,
            'esFuenteFinanciamiento' => $this->esFuenteFinanciamiento,
            'tipoVenta' => $this->tipoVenta,
            'GeneraPago' => $this->GeneraPago,
            'idTipoConcepto' => $this->idTipoConcepto,
            'Estado' => $this->Estado,
        ];
    }
}