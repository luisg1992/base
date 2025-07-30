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
use Modules\Configuracion\Http\Resources\FuenteFinanciamientoCollection;
use Modules\Configuracion\Models\FuenteFinanciamiento;

trait FuenteFinanciamientoDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Fuente Financiamiento';
        $this->titulo_tabla = 'FUENTE FINANCIAMIENTO';
        $this->nombre_tabla = 'Fuente Financiamiento';
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
        $builder->agregarColumna(Column::make('Descripcion')->label('Descripcion')->sortable(true));
        $builder->agregarColumna(Column::make('codigo')->label('Codigo')->sortable(true));
        $builder->agregarColumna(Column::make('idEstado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());
        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter->agregarFiltro(
            Filter::makeInput('Descripcion')
                ->label('Descripcion')
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

        $query = FuenteFinanciamiento::query()

            ->when($Descripcion, function (Builder $q) use ($Descripcion) {
                $q->where(function ($subquery) use ($Descripcion) {
                    $subquery->where('Descripcion', 'LIKE', "%{$Descripcion}%");
                });
            });

        $query->orderBy('Descripcion', 'asc');
        return (new FuenteFinanciamientoCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
