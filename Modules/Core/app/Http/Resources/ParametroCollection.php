<?php

namespace Modules\Core\Http\Resources;

use App\Core\Table\Button;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ParametroCollection extends ResourceCollection
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
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones, $row);

            return [
                'id' => $row->IdParametro,
                'IdParametro' => $row->IdParametro,
                'Tipo' => $row->Tipo,
                'Codigo' => $row->Codigo,
                'ValorTexto' => $row->ValorTexto,
                'ValorInt' => $row->ValorInt,
                'ValorFloat' => $row->ValorFloat,
                'Descripcion' => $row->Descripcion,
                'Grupo' => $row->Grupo,
                'actions' => $builder->getButtons(),
            ];
        });
    }
}
