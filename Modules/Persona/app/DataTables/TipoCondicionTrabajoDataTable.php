<?php

namespace Modules\Persona\DataTables;

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
use Modules\Persona\Models\TipoCondicionTrabajo;
use Modules\Persona\Http\Resources\TipoCondicionTrabajoCollection;

trait TipoCondicionTrabajoDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Tipos de Condicion de Trabajo';
        $this->titulo_tabla = 'TIPOS CONDICION TRABAJO';
        $this->nombre_tabla = 'TipoCondicionTrabajo';
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
        $builder->agregarColumna(Column::make('Descripcion')->label('Tipo Condición Trabajo')->sortable(true));
        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
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
        $Descripcion = optional($filters->firstWhere('name', 'Descripcion'))['value'] ?? '';

        $query = TipoCondicionTrabajo::query()
            ->when($Descripcion, function (Builder $q) use ($Descripcion) {
                $q->where(function ($subquery) use ($Descripcion) {
                    $subquery->where('Descripcion', 'LIKE', "%{$Descripcion}%");
                });
            });

        $query->orderBy('IdCondicionTrabajo', 'asc');
        return (new TipoCondicionTrabajoCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
