<?php

namespace Modules\ProgramacionGeneral\DataTables;

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
use Modules\ProgramacionGeneral\Models\Turno;
use Modules\ProgramacionGeneral\Http\Resources\TurnoCollection;

trait TurnoDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Turnos';
        $this->titulo_tabla = 'TURNOS';
        $this->nombre_tabla = 'Turnos';
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
        $builder->agregarColumna(Column::make('TipoServicio')->label('Tipo Servicio')->sortable(true));
        $builder->agregarColumna(Column::make('TipoTurno')->label('Tipo Turno')->sortable(true));
        $builder->agregarColumna(Column::make('Codigo')->label('Código')->sortable(true));
        $builder->agregarColumna(Column::make('Descripcion')->label('Turno')->sortable(true));
        $builder->agregarColumna(Column::make('HoraInicio')->label('Hora Inicio')->sortable(true));
        $builder->agregarColumna(Column::make('HoraFin')->label('Hora Fin')->sortable(true));
        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        // Filtro de Tipo de Servicios
        $tipoServicios = cache_configuracion_tipo_servicios();
        $filter->agregarFiltro(
            Filter::makeSelect('IdTipoServicio')
                ->label('Tipo de Servicio')
                ->cssClass('col-12 col-sm-6 col-md-4 col-lg-4')
                ->options($tipoServicios)
                ->includeAllOption(false)
                ->placeholder('Seleccione un Tipo de Servicio...')
        );

        // Tipos de Turnos
        $tipoTurnos = cache_programacion_tipo_turnos();
        $filter->agregarFiltro(
            Filter::makeSelect('IdTurno')
                ->label('Tipo Turno')
                ->cssClass('col-12 col-sm-6 col-md-3 col-lg-2')
                ->options($tipoTurnos)
                ->includeAllOption(false)
                ->placeholder('Seleccione un Tipo de Turno...')
        );

        $filter->agregarFiltro(
            Filter::makeInput('Descripcion')
                ->label('Descripcion')
                ->cssClass('col-12 col-sm-12 col-md-5 col-lg-6')
                ->placeholder('Ingrese una descripción y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $Descripcion = optional($filters->firstWhere('name', 'Descripcion'))['value'] ?? '';
        $IdTipoServicio = optional($filters->firstWhere('name', 'IdTipoServicio'))['value'] ?? '';
        $IdTurno = optional($filters->firstWhere('name', 'IdTurno'))['value'] ?? '';

        $query = Turno::query()
            ->with('tipoTurno', 'tipoServicio')
            ->when($Descripcion, function (Builder $q) use ($Descripcion) {
                $q->where(function ($subquery) use ($Descripcion) {
                    $subquery->where('Descripcion', 'LIKE', "%{$Descripcion}%");
                });
            });

        if ($IdTipoServicio) {
            $query->when($IdTipoServicio, function (Builder $q) use ($IdTipoServicio) {
                $q->where(function ($subquery) use ($IdTipoServicio) {
                    $subquery->where('IdTipoServicio', $IdTipoServicio);
                });
            });
        }

        if ($IdTurno) {
            $query->when($IdTurno, function (Builder $q) use ($IdTurno) {
                $q->where(function ($subquery) use ($IdTurno) {
                    $subquery->where('IdTipoTurnoRef', $IdTurno);
                });
            });
        }

        $query->orderBy('IdTurno');
        return (new TurnoCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
