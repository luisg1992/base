<?php

namespace Modules\Imagenologia\Http\Resources;

use App\Core\Table\Button;
//use App\Core\Table\Badge;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;
use App\Core\Table\Cell;

class ListaEsperaCollection extends ResourceCollection
{
    public function toArray(Request $request): Collection
    {
        return $this->collection->transform(function ($recetaCabecera, $key) {
            //Botones
            $builder = new ButtonBuilder();
            $builder->agregarBoton(Button::botonVisualizar());
            $builder->agregarBoton(Button::make()->label('Programar')->action('programar')->icon('ti ti-calendar')->url($recetaCabecera->IdPuntoCarga));

            $fechaReceta = Carbon::parse($recetaCabecera->FechaReceta);
            $fechaHoy = Carbon::now();

            $diferenciaDias = $fechaReceta->diffInDays($fechaHoy, false);
            
             $FechaDif = Cell::badgeText(
                $recetaCabecera->FechaReceta,
                $diferenciaDias < 3 ? 'success' : 'danger'
            );

            return [
                    'id' => $recetaCabecera->idReceta,
                    'IdPuntoCarga' => $recetaCabecera->IdPuntoCarga,
                    'Descripcion' => optional($recetaCabecera->factPuntoCarga)->Descripcion,
                    'FechaReceta' => $FechaDif,
                    'idItem' => optional($recetaCabecera->recetaDetalle)->idItem,
                    'Paciente' => optional(optional($recetaCabecera->facturacionCuentasAtencion)->paciente)->ApellidoPaterno . ' ' . optional(optional($recetaCabecera->facturacionCuentasAtencion)->paciente)->ApellidoMaterno,
                    'actions' => $builder->getButtons()
                ];
        });
    }
}
