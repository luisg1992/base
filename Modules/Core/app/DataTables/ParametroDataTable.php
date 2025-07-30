<?php

namespace Modules\Core\DataTables;

use App\Core\Table\Button;
use App\Core\Table\Column;
use App\Core\Table\Filter;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\TableBuilder;
use App\Core\Table\FilterBuilder;
use App\Core\Table\ButtonBuilder;
use App\DataTables\Traits\PaginationTrait;
use Modules\Core\Http\Resources\ParametroCollection;
use Modules\Core\Models\Parametro;

trait ParametroDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Parámetros';
        $this->titulo_tabla = 'Parámetros';
        $this->nombre_tabla = 'parametros';
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
        $builder->agregarColumna(Column::make('Tipo')->label('Tipo')->sortable(true));
        $builder->agregarColumna(Column::make('Codigo')->label('Código')->sortable(true));
        $builder->agregarColumna(Column::make('Descripcion')->label('Descripción')->sortable(true));
        $builder->agregarColumna(Column::make('ValorTexto')->label('Valor Texto')->sortable(true));
        $builder->agregarColumna(Column::make('ValorInt')->label('Valor int')->sortable(true));
        $builder->agregarColumna(Column::make('ValorFloat')->label('Valor float')->sortable(true));
        $builder->agregarColumna(Column::make('Grupo')->label('Grupo')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        $filter->agregarFiltro(
            Filter::makeInput('Descripcion')
                ->label('Descripción')
                ->cssClass('col-12')
                ->placeholder('Ingrese una descripción y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $descripcion = optional($filters->firstWhere('name', 'Descripcion'))['value'] ?? '';

        $query = Parametro::query()
            ->where('Descripcion', 'LIKE', "%{$descripcion}%");

        $query->orderBy('IdParametro', $this->direction);

        return (new ParametroCollection($query->paginate($this->limit)))
            ->additional($this->metaAdditional);
    }

}
