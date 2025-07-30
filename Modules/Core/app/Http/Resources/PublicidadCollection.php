<?php

namespace Modules\Core\Http\Resources;

use App\Core\Table\Button;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class PublicidadCollection extends ResourceCollection
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
                'id' => $row->IdPublicidad,
                'Titulo' => $row->Titulo,
                'Descripcion' => $row->Descripcion,
                'TamanoLetra' => $row->TamanoLetra,
                'ColorLetra' => $row->tipo->ColorLetra,
                'ColorFondo' => $row->tipo->ColorFondo,
                'Tipo' => $row->tipo->Nombre,
                'Posicion' => $row->PosicionVertical ? 'Vertical' : 'Horizontal',
                'actions' => $builder->getButtons(),
            ];
        });
    }
}
