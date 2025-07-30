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
use Modules\Core\Http\Resources\SetisisCollection;
use Modules\Core\Models\Setisis;

trait SetisisDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Setisis';
        $this->titulo_tabla = 'Setisis';
        $this->nombre_tabla = 'setisis';
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
        $builder->agregarBoton(Button::botonCrear()->label('Nuevo'));
        $builder->agregarBoton(Button::botonRecargar());
//        $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones);

        return $builder->getButtons();
    }

    private function obtenerColumnasTabla(): array
    {
        $builder = new TableBuilder();
        $builder->agregarColumna(Column::make('Fecha')->label('Fecha'));
        $builder->agregarColumna(Column::make('Numero')->label('Numero'));
        $builder->agregarColumna(Column::make('Paquete')->label('Paquete'));
        $builder->agregarColumna(Column::make('Estado')->label('Estado'));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        $filter->agregarFiltro(
            Filter::makeInput('search')
                ->label('Buscar')
                ->cssClass('col-12')
                ->placeholder('Ingrese usuario o celular y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $search = optional($filters->firstWhere('name', 'search'))['value'] ?? '';

        $query = Setisis::query();

        $query->orderByDesc('Fecha')
            ->orderByDesc('Numero');

        return (new SetisisCollection($query->paginate($this->limit)))
            ->additional($this->metaAdditional);
    }

}
