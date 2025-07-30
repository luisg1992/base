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
use App\DataTables\Traits\PaginationTrait;
use Modules\Persona\Http\Resources\EmpleadoCollection;
use Modules\Persona\Models\Empleado;

trait EmpleadoDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Empleados';
        $this->titulo_tabla = 'EMPLEADOS';
        $this->nombre_tabla = 'empleados';
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
        $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission, $acciones);
        $builder->agregarBoton(Button::botonRecargar());

        return $builder->getButtons();
    }

    private function obtenerColumnasTabla(): array
    {
        $builder = new TableBuilder();
        $builder->agregarColumna(Column::make('ImagenFoto')->label('Foto'));
        $builder->agregarColumna(Column::make('NombreCompleto')->label('Nombre'));
        $builder->agregarColumna(Column::make('DNI')->label('DNI'));
        $builder->agregarColumna(Column::make('TipoEmpleado')->label('Tipo de empleado'));
        $builder->agregarColumna(Column::make('TipoCondicionTrabajo')->label('Condición de trabajo'));
        $builder->agregarColumna(Column::make('Usuario')->label('¿Tiene usuario?')->sortable(false)->type('indicador'));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter
            ->agregarFiltro(
                Filter::makeInput('nombre')
                    ->label('Apellidos y nombres')
                    ->cssClass('col-8')
                    ->placeholder('Ingrese el apellido, nombre o DNI y presione Enter')
            )->agregarFiltro(
                Filter::makeSelect('has_user')
                    ->label('¿Tiene usuario?')
                    ->cssClass('col-4')
                    ->options([
                        ['id' => '1', 'label' => 'Si'],
                        ['id' => '0', 'label' => 'No'],
                    ])
            );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $nombre = optional($filters->firstWhere('name', 'nombre'))['value'] ?? '';

        $hasUser = optional($filters->firstWhere('name', 'has_user'))['value'] ?? '';

        $query = Empleado::query()
            ->with('tipoCondicionTrabajo', 'tipoEmpleado', 'usuarioRelacion');
        if ($nombre !== '') {
            $words = explode(' ', $nombre);
            $query->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->where(function ($subq) use ($word) {
                        $subq->where('ApellidoPaterno', 'LIKE', "{$word}%")
                            ->orWhere('ApellidoMaterno', 'LIKE', "{$word}%")
                            ->orWhere('Nombres', 'LIKE', "{$word}%")
                            ->orWhere('DNI', 'LIKE', "{$word}%");
                    });
                }
            });
        }

        // Filtro por relación usuario
        if ($hasUser !== '' && !is_null($hasUser)) {
            if ($hasUser === '1') {
                $query->whereHas('usuarioRelacion');
            } elseif ($hasUser === '0') {
                $query->whereDoesntHave('usuarioRelacion');
            }
        }

        $query->orderByDesc('IdEmpleado');

        return (new EmpleadoCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
