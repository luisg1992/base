<?php

namespace Modules\Configuracion\Http\Resources;


use App\Core\Table\Badge;
use App\Core\Table\Button;
use App\Core\Table\Cell;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\ButtonBuilder;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DiagnosticoCollection extends ResourceCollection
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
                'id' => $row->IdDiagnostico,
                'CodigoCIE10' => $row->CodigoCIE10,
                'Descripcion' => $row->Descripcion,
                'IdCapitulo' => $row->IdCapitulo,
                'DiagnosticoCapitulo' => optional($row->capitulo)->Descripcion,
                'IdGrupo' => $row->IdGrupo,
                'DiagnosticoGrupo' => optional($row->grupo)->Descripcion,
                'IdCategoria' => $row->IdCategoria,
                'DiagnosticoCategoria' => optional($row->categoria)->Descripcion,
                'CodigoExportacion' => $row->CodigoExportacion,
                'CodigoCIE9' => $row->CodigoCIE9,
                'CodigoCIE10' => $row->CodigoCIE10,
                'Gestacion' => $row->Gestacion,
                'Morbilidad' => $row->Morbilidad,
                'Intrahospitalario' => $row->Intrahospitalario,
                'Restriccion' => $row->Restriccion,
                'EdadMaxDias' => $row->EdadMaxDias,
                'EdadMinDias' => $row->EdadMinDias,
                'IdTipoSexo' => $row->IdTipoSexo,
                'ClaseDxHIS' => $row->ClaseDxHIS,
                'DescripcionMINSA' => $row->DescripcionMINSA,
                'codigoCIEsinPto' => $row->codigoCIEsinPto,
                'FechaInicioVigencia' => $row->FechaInicioVigencia,
                'EsActivo' => Cell::badgeEstado($row),
                'actions' => $builder->getButtons()
            ];
        });
    }
}
