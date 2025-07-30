<?php

namespace Modules\Configuracion\Http\Resources;


use App\Core\Table\Badge;
use App\Core\Table\Button;
use App\Core\Table\Cell;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FuenteFinanciamientoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return Collection
     */
    public function toArray(Request $request): Collection
    {
        $valorPermission = ModuloHelper::obtenerPermisoBaseDesdeRuta();
        $acciones = [
            'visualizar' => Button::botonVisualizar(),
            'editar' => Button::botonEditar(),
            'eliminar' => Button::botonEliminar(),
            'cambiar.estado' => fn($row) => Button::botonEstado($row->Estado)
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {
            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones, $row);

            return [
                'id' => $row->IdFuenteFinanciamiento,
                'Descripcion' => $row->Descripcion,
                'IdTipoFinanciamiento' => $row->IdTipoFinanciamiento,
                'idTipoConceptoFarmacia' => $row->idTipoConceptoFarmacia,
                'ConceptoFarmacia' => optional($row->tipoconceptofarmacia)->Concepto,
                'UtilizadoEn' => $row->UtilizadoEn,
                'CodigoFuenteFinanciamientoSEM' => $row->CodigoFuenteFinanciamientoSEM,
                'idAreaTramitaSeguros' => $row->idAreaTramitaSeguros,
                'AreaTramitaSeguros' => optional($row->areatramitaseguros)->Descripcion,
                'EsUsadoEnCaja' => $row->EsUsadoEnCaja,
                'CodigoHIS' => $row->CodigoHIS,
                'idTipoFinanciador' => $row->idTipoFinanciador,
                'FuenteFinanciador' => optional($row->fuentefinanciador)->nombre,
                'codigo' => $row->codigo,
                'Orden' => $row->Orden,
                'idEstado' => Cell::badgeEstado($row),
                'actions' => $builder->getButtons()
            ];
        });
    }
}
