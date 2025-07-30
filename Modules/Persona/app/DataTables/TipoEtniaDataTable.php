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
use Modules\Persona\Http\Resources\TipoEtniaCollection;
use Modules\Persona\Models\TipoEtnia;

trait TipoEtniaDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Tipos de Etnias';
        $this->titulo_tabla = 'TIPOS ETNIAS ';
        $this->nombre_tabla = 'Etnias';
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
        $builder->agregarColumna(Column::make('desetni')->label('Tipo Etnia')->sortable(true));
        $builder->agregarColumna(Column::make('codgen')->label('Codigo gen')->sortable(true));

        $builder->agregarColumna(Column::make('etnias')->label('Grupo Etnias')->sortable(true));

        $builder->agregarColumna(Column::make('id_etnia')->label('Codigo Etnia')->sortable(true));

        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter->agregarFiltro(
            Filter::makeInput('desetni')
                ->label('desetni')
                ->cssClass('col-12')
                ->placeholder('Ingrese una descripción y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $desetni = optional($filters->firstWhere('name', 'desetni'))['value'] ?? '';

        $query = TipoEtnia::query()
            ->when($desetni, function (Builder $q) use ($desetni) {
                $q->where(function ($subquery) use ($desetni) {
                    $subquery->where('desetni', 'LIKE', "%{$desetni}%");
                });
            });

        $query->orderBy('codetni', 'asc');
        return (new TipoEtniaCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
