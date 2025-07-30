<?php

namespace Modules\Farmacia\Http\Resources;

use App\Core\Table\Button;
use App\Core\Table\ButtonBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class NotaSalidaAlmacenCollection extends ResourceCollection
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
                'MovTipo' => $row->MovTipo,
                'FDestino' => $row->FDestino,
                'FOrigen' => $row->fOrigen,
                'Abreviatura' => $row->Abreviatura,
                'DocumentoNumero' => $row->DocumentoNumero,
                'FechaCreacion' => $row->fechaCreacion,
                'Estado' => $row->Estado,
                'Concepto' => $row->Concepto,
                'Total' => $row->Total,
                'IdUsuario' => $row->idUsuario,
                'Usuario' => $row->usuario,
                'actions' => $builder->getButtons()
            ];
        });
    }
}
