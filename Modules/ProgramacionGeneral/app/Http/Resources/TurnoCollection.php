<?php

namespace Modules\ProgramacionGeneral\Http\Resources;

use App\Core\Table\Badge;
use App\Core\Table\Button;
use App\Core\Table\Cell;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TurnoCollection extends ResourceCollection
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
                'id' => $row->IdTurno,
                'TipoServicio' => optional($row->tipoServicio)->Descripcion,
                'TipoTurno' => optional($row->tipoTurno)->Turno,
                'Codigo' => $row->Codigo,
                'Descripcion' => $row->Descripcion,
                'HoraInicio' => $row->HoraInicio,
                'HoraFin' => $row->HoraFin,
                'IdTipoServicio' => $row->IdTipoServicio,
                'IdEspecialidad' => $row->IdEspecialidad,
                'IdTipoTurnoRef' => $row->IdTipoTurnoRef,
                'Estado' => Cell::badgeEstado($row),
                'actions' => $builder->getButtons()
            ];
        });
    }
}
