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
use App\DataTables\Traits\PaginationTrait;
use Modules\Configuracion\Models\Servicio;
use Modules\Configuracion\Models\TipoServicio;
use Modules\Configuracion\Http\Resources\ServicioCollection;

trait ServicioDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Servicios';
        $this->titulo_tabla = 'SERVICIOS';
        $this->nombre_tabla = 'Servicios';
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
        $builder->agregarColumna(Column::make('TipoServicio')->label('Tipo de servicio')->sortable());
        $builder->agregarColumna(Column::make('Departamento')->label('Departamento')->sortable());
        $builder->agregarColumna(Column::make('EspecialidadPrimaria')->label('Especialidad primaria')->sortable());
        $builder->agregarColumna(Column::make('Especialidad')->label('Especialidad')->sortable());
        $builder->agregarColumna(Column::make('Nombre')->label('Servicio')->sortable());
        $builder->agregarColumna(Column::make('Codigo')->label('Código')->sortable());
        $builder->agregarColumna(Column::make('codigoServicioHIS')->label('codigoServicioHIS')->sortable());
        $builder->agregarColumna(Column::make('idEstado')->label('idEstado')->type('indicador')->sortable());
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
                ->cssClass('col-12 col-sm-6 col-md-3 col-lg-3')
                ->options($tipoServicios)
                ->includeAllOption(false)
                ->placeholder('Seleccione un Tipo de Servicio...')
        );

        // Filtro de departamentos hospital
        $departamentos_hospital = cache_configuracion_departamentos_hospital();
        $filter->agregarFiltro(
            Filter::makeSelect('IdDepartamento')
                ->label('Departamento')
                ->cssClass('col-12 col-sm-6 col-md-3 col-lg-3')
                ->options($departamentos_hospital)
                ->includeAllOption(false)
                ->placeholder('Seleccione un departamento...')
        );

        // Filtro de especialidades primarias
        $especialidades_primarias = cache_configuracion_especialidades_primarias();
        $filter->agregarFiltro(
            Filter::makeSelect('IdEspecialidadPrimaria')
                ->label('Especialidad primaria')
                ->cssClass('col-12 col-sm-6 col-md-3 col-lg-3')
                ->options($especialidades_primarias)
                ->includeAllOption(false)
                ->placeholder('Seleccione un especialidad primaria...')
        );

        // Filtro de especialidades
        $especialidades = cache_configuracion_especialidades();
        $filter->agregarFiltro(
            Filter::makeSelect('IdEspecialidad')
                ->label('Especialidad')
                ->cssClass('col-12 col-sm-6 col-md-3 col-lg-3')
                ->options($especialidades)
                ->includeAllOption(false)
                ->value(null)
                ->placeholder('Seleccione un especialidad...')
        );

        $filter->agregarFiltro(
            Filter::makeInput('Nombre')
                ->label('Nombre')
                ->cssClass('col-12 col-sm-12')
                ->placeholder('Ingrese el nombre del servicio y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();

        $filters = collect($request->input('filters'));
        $Nombre = optional($filters->firstWhere('name', 'Nombre'))['value'] ?? null;
        $IdTipoServicio = optional($filters->firstWhere('name', 'IdTipoServicio'))['value'] ?? null;
        $IdDepartamento = optional($filters->firstWhere('name', 'IdDepartamento'))['value'] ?? null;
        $IdEspecialidadPrimaria = optional($filters->firstWhere('name', 'IdEspecialidadPrimaria'))['value'] ?? null;
        $IdEspecialidad = optional($filters->firstWhere('name', 'IdEspecialidad'))['value'] ?? null;

        $query = Servicio::query()->with(['especialidad', 'especialidad.departamento', 'especialidad.especialidadPrimaria', 'tipoServicio']);

        if ($IdDepartamento) {
            $query->whereHas('especialidad', function ($query) use ($IdDepartamento) {
                $query->where('IdDepartamento', $IdDepartamento);
            });
        }

        if ($IdEspecialidadPrimaria) {
            $query->whereHas('especialidad', function ($query) use ($IdEspecialidadPrimaria) {
                $query->where('IdEspecialidadPrimaria', $IdEspecialidadPrimaria);
            });
        }

        if ($IdEspecialidad) {
            $query->where('IdEspecialidad',  $IdEspecialidad);
        }

        $query->when($IdTipoServicio, function ($q) use ($IdTipoServicio) {
            $q->where('IdTipoServicio', $IdTipoServicio);
        })
            ->when($Nombre, function ($q) use ($Nombre) {
                $q->where('Nombre', 'LIKE', "%{$Nombre}%");
            });

        $query->orderByDesc('IdServicio', 'desc');

        return (new ServicioCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }

}
