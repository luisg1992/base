<?php

namespace Modules\Core\Http\Resources;

use App\Core\Table\Button;
use App\Core\Table\Badge;
use App\Core\Table\Cell;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ModuloCollection extends ResourceCollection
{
    public function toArray(Request $request): Collection
    {
        $valorPermission = ModuloHelper::obtenerPermisoBaseDesdeRuta();
        $acciones = [
            'visualizar' => Button::botonVisualizar(),
            'editar' => Button::botonEditar(),
            'eliminar' => Button::botonEliminar(),
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {
            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission, $acciones, $row);

            return [
                'id' => $row->ModuloId,
                'ModuloPadreId' => $row->ModuloPadreId,
                'modulo_padre' => $row,
                'Etiqueta' => $row->Etiqueta,
                'Subtitulo' => $row->Subtitulo,
                'Descripcion' => $row->Descripcion,
                'Icono' => $row->Icono,
                'Url' => $row->Url,
                'EsAccesoDirecto' => $row->EsAccesoDirecto,
                'EstaBloqueado' => $row->EstaBloqueado,
                'Estado' => Cell::badgeEstado($row),
                'Orden' => $row->Orden,
                'actions' => $builder->getButtons(),
            ];
        });
    }
}
