<?php

namespace Modules\Seguridad\DataTables;

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
use App\Models\User;
use Modules\Seguridad\Http\Resources\UserCollection;

trait UserDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Usuarios';
        $this->titulo_tabla = 'USUARIOS';
        $this->nombre_tabla = 'users';
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
        $acciones = [];

        $builder = new ButtonBuilder();
        $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones);
        $builder->agregarBoton(Button::botonRecargar());

        return $builder->getButtons();
    }

    private function obtenerColumnasTabla(): array
    {
        $builder = new TableBuilder();
        $builder->agregarColumna(Column::make('name')->label('Nombre'));
        $builder->agregarColumna(Column::make('email')->label('Usuario'));
        $builder->agregarColumna(Column::make('contacto')->label('Datos de contacto'));
        $builder->agregarColumna(Column::make('empleado_nombre')->label('Nombre del empleado'));
        $builder->agregarColumna(Column::make('estado')->label('Estado')->type('indicador'));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        $filter->agregarFiltro(
            Filter::makeInput('name')
                ->label('Buscar por nombre del usuario')
                ->cssClass('col-12')
                ->placeholder('Ingrese el nombre y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();

        $filters = collect($request->input('filters'));
        $name = optional($filters->firstWhere('name', 'name'))['value'] ?? '';

        $query = User::query()
            ->with('empleado')
            ->when($name, function (Builder $q) use ($name) {
                $q->where(function ($subquery) use ($name) {
                    $subquery->where('name', 'LIKE', "%{$name}%")
                        ->orWhere('email', 'LIKE', "%{$name}%");
                });
            });

        $query->orderBy($this->sortBy, $this->direction);

        return (new UserCollection($query->paginate($this->limit)))
            ->additional($this->metaAdditional);
    }
}
