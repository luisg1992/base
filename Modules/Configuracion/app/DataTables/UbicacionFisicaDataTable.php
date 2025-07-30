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
use Modules\Configuracion\Models\UbicacionFisica;
use Modules\Configuracion\Models\EspecialidadPrimaria;
use Modules\Configuracion\Http\Resources\UbicacionFisicaCollection;

trait UbicacionFisicaDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Consultorio';
        $this->titulo_tabla = 'CONSULTORIO';
        $this->nombre_tabla = 'UbicacionFisica';
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
        $builder->agregarColumna(Column::make('Departamento')->label('Departamento')->sortable(true));
        $builder->agregarColumna(Column::make('EspecialidadPrimaria')->label('Especialidad Primaria')->sortable(true));
        $builder->agregarColumna(Column::make('Nombre')->label('Ubicación Física')->sortable(true));
        $builder->agregarColumna(Column::make('TipoUbicacionFisica')->label('Tipo')->sortable(true));
        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        // Filtro de Especialidades Primarias
        $especialidadesPrimarias = cache_configuracion_especialidades_primarias();
        $filter->agregarFiltro(
            Filter::makeSelect('IdEspecialidadPrimaria')
                ->label('Especialidad Primaria')
                ->cssClass('col-12 col-sm-6 col-md-4 col-lg-4')
                ->options($especialidadesPrimarias)
                ->includeAllOption(false)
                ->placeholder('Seleccione una Especialidad Primaria...')
        );

        $filter->agregarFiltro(
            Filter::makeInput('Nombre')
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
        $Nombre = optional($filters->firstWhere('name', 'Nombre'))['value'] ?? '';
        $IdEspecialidadPrimaria = optional($filters->firstWhere('name', 'IdEspecialidadPrimaria'))['value'] ?? '';

        $query = UbicacionFisica::query()
            ->with('especialidadPrimaria.departamento')
            ->when($Nombre, function (Builder $q) use ($Nombre) {
                $q->where(function ($subquery) use ($Nombre) {
                    $subquery->where('Nombre', 'LIKE', "%{$Nombre}%");
                });
            });

        if ($IdEspecialidadPrimaria) {
            $query->when($IdEspecialidadPrimaria, function (Builder $q) use ($IdEspecialidadPrimaria) {
                $q->where('IdEspecialidadPrimaria', $IdEspecialidadPrimaria);
            });
        }

        $query->orderBy('IdUbicacionFisica');
        return (new UbicacionFisicaCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
