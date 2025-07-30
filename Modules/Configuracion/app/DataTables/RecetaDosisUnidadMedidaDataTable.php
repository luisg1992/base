<?php

namespace Modules\Configuracion\DataTables;

use App\Core\Table\Button;
use App\Core\Table\Column;
use App\Core\Table\Filter;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\TableBuilder;
use App\Core\Table\FilterBuilder;
use App\Core\Table\ButtonBuilder;
use Illuminate\Database\Eloquent\Builder;
use App\DataTables\Traits\PaginationTrait;
use Modules\Configuracion\Http\Resources\RecetaDosisUnidadMedidaCollection;
use Modules\Configuracion\Models\RecetaDosisUnidadMedida;

trait RecetaDosisUnidadMedidaDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Receta Dosis Unidades de Medida';
        $this->titulo_tabla = 'RECETA DOSIS UNIDADES DE MEDIDA';
        $this->nombre_tabla = 'RecetaDosisUnidadMedida';
        $this->columns = $this->obtenerColumnasTabla();
        $this->getConfiguracionDataTable();
        $this->filters = $this->obtenerCamposFiltro();

        return [
            'titulo_pagina' => $this->titulo_pagina,
            'titulo_tabla' => $this->titulo_tabla,
            'nombre_tabla' => $this->nombre_tabla,
            'columns' => $this->columns,
            'visible_columns' => $this->visibleColumns,
            'pagination' => $this->initPagination(),
            'filters' => $this->filters,
            'header_buttons' => $this->obtenerBotonesCabecera(),
        ];
    }

    private function obtenerBotonesCabecera(): array
    {
        $valorPermission = ModuloHelper::obtenerPermisoBaseDesdeRuta();
        $acciones = [
            'crear' => Button::botonCrear()->label('Nuevo'),
        ];

        $builder = new ButtonBuilder();
        $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones);
        $builder->agregarBoton(Button::botonRecargar());

        return $builder->getButtons();
    }

    private function obtenerColumnasTabla(): array
    {
        $builder = new TableBuilder();
        $builder->agregarColumna(Column::make('DosisUnidadMedida')->label('Dosis Unidad de Medida')->sortable(true));
        //$builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter->agregarFiltro(
            Filter::makeInput('DosisUnidadMedida')
                ->label('Dosis Unidad de Medida')
                ->cssClass('col-12')
                ->placeholder('Ingrese el numero de dosis y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $DosisUnidadMedida = optional($filters->firstWhere('name', 'DosisUnidadMedida'))['value'] ?? '';

        /* $query = CitaAnuladaMotivo::query()
            ->when($Descripcion, fn(Builder $q) => $q->where('Descripcion', 'like', "%$Descripcion%")); */
        $query = RecetaDosisUnidadMedida::query()
            ->when($DosisUnidadMedida, function (Builder $q) use ($DosisUnidadMedida) {

                $q->where(function ($subquery) use ($DosisUnidadMedida) {
                    $subquery->where('DosisUnidadMedida', 'LIKE', "%{$DosisUnidadMedida}%");
                });
            });

        $query->orderBy('IdRecetaDosisUnidadMedida');
        return (new RecetaDosisUnidadMedidaCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
