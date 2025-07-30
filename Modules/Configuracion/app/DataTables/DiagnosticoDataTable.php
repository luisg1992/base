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
use Modules\Configuracion\Http\Resources\DiagnosticoCollection;
use Modules\Configuracion\Models\Diagnostico;


trait DiagnosticoDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Diagnosticos';
        $this->titulo_tabla = 'DIAGNOSTICOS ';
        $this->nombre_tabla = 'Diagnosticos';
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
            'header_buttons' => $this->obtenerBotnesCabecera(),
        ];
    }

    private function obtenerBotnesCabecera(): array
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
        $builder->agregarColumna(Column::make('CodigoCIE10')->label('CodigoCIE10')->sortable(true));
        $builder->agregarColumna(Column::make('Descripcion')->label('Descripcion')->sortable(true));
        $builder->agregarColumna(Column::make('CodigoCIE9')->label('CodigoCIE9')->sortable(true));
        $builder->agregarColumna(Column::make('EsActivo')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter->agregarFiltro(
            Filter::makeInput('Descripcion')
                ->label('Descripcion')
                ->cssClass('col-6')
                ->placeholder('Ingrese una descripción y presione Enter')
        );
        $filter->agregarFiltro(
            Filter::makeInput('CodigoCIE10')
                ->label('CodigoCIE10')
                ->cssClass('col-6')
                ->placeholder('Ingrese una descripción y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $Descripcion = optional($filters->firstWhere('name', 'Descripcion'))['value'] ?? '';
        $CodigoCIE10 = optional($filters->firstWhere('name', 'CodigoCIE10'))['value'] ?? '';

        $query = Diagnostico::query()
            ->when($Descripcion, function (Builder $q) use ($Descripcion) {
                $q->where(function ($subquery) use ($Descripcion) {
                    $subquery->where('Descripcion', 'LIKE', "%{$Descripcion}%");
                });
            })
            ->when($CodigoCIE10, function (Builder $q) use ($CodigoCIE10) {
                $q->where(function ($subquery) use ($CodigoCIE10) {
                    $subquery->where('CodigoCIE10', 'LIKE', "%{$CodigoCIE10}%");
                });
            });

        $query->orderBy('Descripcion', 'asc');
        return (new DiagnosticoCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
