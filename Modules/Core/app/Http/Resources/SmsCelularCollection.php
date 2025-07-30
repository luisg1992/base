<?php

namespace Modules\Core\Http\Resources;

use App\Core\Table\Button;
use App\Core\Table\Cell;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class SmsCelularCollection extends ResourceCollection
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
            'enviar.sms' => Button::make()->label('Enviar SMS')->icon('ti ti-send')->action("enviar_sms")
        ];

        return $this->collection->transform(function ($row, $key) use ($valorPermission, $acciones) {
            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones, $row);

            return [
                'id' => $row->IdSmsCelular,
                'Url' => $row->Url,
                'Token' => $row->Token,
                'Usuario' => $row->Usuario,
                'Celular' => $row->Celular,
                'Estado' => Cell::badgeEstado($row),
                'actions' => $builder->getButtons(),
            ];
        });
    }
}
