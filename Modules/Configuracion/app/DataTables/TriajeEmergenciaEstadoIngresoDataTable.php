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
use Modules\Configuracion\Models\TriajeEmergenciaEstadoIngreso;
use Modules\Configuracion\Http\Resources\TriajeEmergenciaEstadoIngresoCollection;

trait TriajeEmergenciaEstadoIngresoDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Estados de Ingreso';
        $this->titulo_tabla = 'ESTADOS DE INGRESO';
        $this->nombre_tabla = 'TriajeEmergenciaEstadosIngresos';
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
        $builder->agregarColumna(Column::make('FormaIngreso')->label('Forma de Ingreso')->sortable(true));
        $builder->agregarColumna(Column::make('Descripcion')->label('Estado de Ingreso')->sortable(true));
        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        // Filtro de Especialdiades
        $formas_ingreso = cache_triaje_emergencia_formas_ingreso();
        $filter->agregarFiltro(
            Filter::makeSelect('IdFormaIngreso')
                ->label('Forma de Ingreso')
                ->cssClass('col-12 col-sm-6 col-md-4 col-lg-4')
                ->options($formas_ingreso)
                ->includeAllOption(false)
                ->placeholder('Seleccione una Forma de Ingreso...')
        );

        $filter->agregarFiltro(
            Filter::makeInput('Descripcion')
                ->label('Descripción')
                ->cssClass('col-12 col-sm-6 col-md-8 col-lg-8')
                ->placeholder('Ingrese una descripción y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $Descripcion = optional($filters->firstWhere('name', 'Descripcion'))['value'] ?? '';
        $IdFormaIngreso = optional($filters->firstWhere('name', 'IdFormaIngreso'))['value'] ?? '';

        $query = TriajeEmergenciaEstadoIngreso::query()
            ->with('formaIngreso')
            ->when($Descripcion, function (Builder $q) use ($Descripcion) {
                $q->where(function ($subquery) use ($Descripcion) {
                    $subquery->where('Descripcion', 'LIKE', "%{$Descripcion}%");
                });
            });

        if ($IdFormaIngreso) {
            $query->whereHas('formaIngreso', function ($query) use ($IdFormaIngreso) {
                $query->where('IdFormaIngreso', $IdFormaIngreso);
            });
        }

        $query->orderBy('IdEstadoIngreso');
        return (new TriajeEmergenciaEstadoIngresoCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
