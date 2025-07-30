<?php


namespace Modules\Configuracion\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FuenteFinanciamientoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->IdFuenteFinanciamiento,
            'Descripcion' => $this->Descripcion,
            'IdTipoFinanciamiento' => $this->IdTipoFinanciamiento,
            'idTipoConceptoFarmacia' => $this->idTipoConceptoFarmacia,
            'UtilizadoEn' => $this->UtilizadoEn,
            'CodigoFuenteFinanciamientoSEM' => $this->CodigoFuenteFinanciamientoSEM,
            'idAreaTramitaSeguros' => $this->idAreaTramitaSeguros,
            'EsUsadoEnCaja' => $this->EsUsadoEnCaja,
            'CodigoHIS' => $this->CodigoHIS,
            'idTipoFinanciador' => $this->idTipoFinanciador,
            'codigo' => $this->codigo,
            'Orden' => $this->Orden,
            'idEstado' => $this->idEstado,

            'tarifas' => $this->tarifas->map(function ($tarifa) {
                return [
                    'idTipoFinanciamiento' => $tarifa->idTipoFinanciamiento,
                ];
            }),
        ];
    }
}