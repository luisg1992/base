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
use Modules\Configuracion\Http\Resources\RecetaDosisCollection;
use Modules\Configuracion\Models\RecetaDosis;

trait RecetaDosisDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Receta Dosis';
        $this->titulo_tabla = 'RECETA DOSIS';
        $this->nombre_tabla = 'RecetaDosis';
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
        $builder->agregarColumna(Column::make('NumeroDosis')->label('Numero de Dosis')->sortable(true));
        //$builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter->agregarFiltro(
            Filter::makeInput('NumeroDosis')
                ->label('Numero de Dosis')
                ->cssClass('col-12')
                ->placeholder('Ingrese el numero de dosis y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $NumeroDosis = optional($filters->firstWhere('name', 'NumeroDosis'))['value'] ?? '';

        /* $query = CitaAnuladaMotivo::query()
            ->when($Descripcion, fn(Builder $q) => $q->where('Descripcion', 'like', "%$Descripcion%")); */
        $query = RecetaDosis::query()
            ->when($NumeroDosis, function (Builder $q) use ($NumeroDosis) {

                $q->where(function ($subquery) use ($NumeroDosis) {
                    $subquery->where('NumeroDosis', 'LIKE', "%{$NumeroDosis}%");
                });
            });

        $query->orderBy('idDosis');
        return (new RecetaDosisCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
