<?php

namespace Modules\Farmacia\Http\Resources;

use App\Builders\Table\Button;
use App\Builders\Table\ButtonBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class FarmaciaAlmacenCollection extends ResourceCollection
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
            $builder->addButton(Button::viewButton())
                ->addButton(Button::editButton())
                ->addButton(Button::deleteButton());

            return [
                'id' => $row->idAlmacen,
                'descripcion' => $row->descripcion,
                'codigo_sismed' => $row->codigoSISMED,
                'actions' => $builder->getButtons()
            ];
        });
    }
}
