<?php

namespace Modules\Seguridad\Http\Resources;

use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use App\Core\Table\ButtonBuilder;
use App\Core\Table\Button;

class PermissionCollection extends ResourceCollection
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
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {

            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones, $row);

            return [
                'id' => $row->id,
                'name' => $row->name,
                'modulo_id' => $row->modulo_id,
                'descripcion' => $row->descripcion,
                'actions' => $builder->getButtons()
            ];
        });
    }
}
