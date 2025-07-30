<?php

namespace Modules\Core\Http\Resources;

use App\Core\Table\Button;
use App\Core\Table\Cell;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class SetisisCollection extends ResourceCollection
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


        return $this->collection->transform(function ($row, $key) use ($valorPermission) {

            $acciones = [];
            if($row->Estado == 1) {
                $acciones['enviar.paquete'] = Button::make()->label('Enviar')->icon('ti ti-send')->action("enviar_paquete");
            }
            if($row->Estado == 2) {
                $acciones['consultar.paquete'] = Button::make()->label('Consultar')->icon('ti ti-send')->action("consultar_paquete");
            }

            $builder = new ButtonBuilder();
            $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones, $row);

            return [
                'id' => $row->IdSetisis,
                'Fecha' => $row->Fecha->format('d/m/Y'),
                'Numero' => $row->Numero,
                'Paquete' => $row->PaqueteNumero,
                'Estado' => $row->Estado,
                'actions' => $builder->getButtons(),
            ];
        });
    }
}
