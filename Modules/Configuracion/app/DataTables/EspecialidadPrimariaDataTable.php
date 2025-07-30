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
use Modules\Configuracion\Models\EspecialidadPrimaria;
use Modules\Configuracion\Http\Resources\EspecialidadPrimariaCollection;

trait EspecialidadPrimariaDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Especialidades Primarias';
        $this->titulo_tabla = 'ESPECIALIDADES PRIMARIAS';
        $this->nombre_tabla = 'EspecialidadesPrimarias';
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
        $builder->agregarColumna(Column::make('Nombre')->label('Especialidad Primaria')->sortable(true));
        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        // Cargar los departamentos
        $departamentos = cache_configuracion_departamentos_hospital();
        $filter->agregarFiltro(
            Filter::makeSelect('IdDepartamento')
                ->label('Departamento')
                ->cssClass('col-12 col-sm-6 col-md-4 col-lg-4')
                ->options($departamentos)
                ->includeAllOption(false)
                ->placeholder('Seleccione un Departamento...')
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
        $IdDepartamento = optional($filters->firstWhere('name', 'IdDepartamento'))['value'] ?? '';

        // Construcción de la consulta
        $query = EspecialidadPrimaria::query()
            ->with('departamento')
            ->when($Nombre, function (Builder $q) use ($Nombre) {
                $q->where(function ($subquery) use ($Nombre) {
                    $subquery->where('Nombre', 'LIKE', "%{$Nombre}%");
                });
            });

        if ($IdDepartamento) {
            $query->when($IdDepartamento, function (Builder $q) use ($IdDepartamento) {
                $q->where('IdDepartamento', $IdDepartamento);
            });
        }

        $query->orderBy('IdEspecialidadPrimaria');
        return (new EspecialidadPrimariaCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
