<?php

namespace Modules\Farmacia\Http\Resources;

use App\Core\Table\Button;
use App\Core\Table\ButtonBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class NotaIngresoAlmacenCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return Collection
     */
    public function toArray(Request $request): Collection
    {
        return $this->collection->transform(function ($row, $key) {

            $builder = new ButtonBuilder();
            $builder->agregarBoton(Button::botonVisualizar())
                    ->agregarBoton(Button::botonEditar());

            return [
                'id' => $row->MovNumero,
                'MovNumero' => $row->MovNumero,
                'Observaciones' => $row->Observaciones,
                'Estado' => $row->Estado,
                'NroCuenta' => $row->NroCuenta,
                'Total' => $row->Total,
                'Concepto' => $row->Concepto,
                'FOrigen' => $row->fOrigen,
                'FDestino' => $row->FDestino,
                'MovTipo' => $row->MovTipo,
                'FechaCreacion' => $row->fechaCreacion,
                'DocumentoNumero' => $row->DocumentoNumero,
                'Abreviatura' => $row->Abreviatura,
                'actions' => $builder->getButtons()
            ];
        });
    }
}
