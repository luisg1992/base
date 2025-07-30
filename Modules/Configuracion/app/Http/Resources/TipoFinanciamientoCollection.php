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

class TipoFinanciamientoCollection extends ResourceCollection
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
            'cambiar.estado' => fn($row) => Button::botonEstado($row->Estado),
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {
            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones, $row);

            return [
                'id' => $row->IdTipoFinanciamiento,
                'Descripcion' => $row->Descripcion,
                'esOficina' => $row->esOficina,
                'esSalida' => $row->esSalida,
                'SeIngresPrecios' => $row->SeIngresPrecios,
                'EsFarmacia' => $row->EsFarmacia,
                'idCajaTiposComprobante' => $row->idCajaTiposComprobante,
                'TipoComprobante'  => optional($row->tipocomprobante)->Descripcion,
                'SeImprimeComprobante' => $row->SeImprimeComprobante,
                'esFuenteFinanciamiento' => $row->esFuenteFinanciamiento,
                'tipoVenta' => $row->tipoVenta,
                'GeneraPago' => $row->GeneraPago,
                'idTipoConcepto' => $row->idTipoConcepto,
                'TipoConcepto'  => optional($row->tipoconcepto)->Concepto,
                'Estado' => Cell::badgeEstado($row),
                'actions' => $builder->getButtons()
            ];
        });
    }
}
