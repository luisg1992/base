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
use Modules\Seguridad\Http\Resources\PermissionCollection;
use Spatie\Permission\Models\Permission;

trait PermissionDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Permisos';
        $this->titulo_tabla = 'PERMISOS';
        $this->nombre_tabla = 'permissions';
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
        $builder->agregarColumna(Column::make('name')->label('Nombre')->sortable(true));
        $builder->agregarColumna(Column::make('descripcion')->label('Descripción')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        $filter->agregarFiltro(
            Filter::makeInput('name')
                ->label('Descripción')
                ->cssClass('col-12')
                ->placeholder('Ingrese una descripción y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();

        $filters = collect($request->input('filters'));
        $Etiqueta = optional($filters->firstWhere('name', 'descripcion'))['value'] ?? '';

        $query = Permission::query()
            ->when($Etiqueta, function (Builder $q) use ($Etiqueta) {
                $q->where(function ($subquery) use ($Etiqueta) {
                    $subquery->where('name', 'LIKE', "%{$Etiqueta}%")
                        ->orWhere('descripcion', 'LIKE', "%{$Etiqueta}%");
                });
            });

        $query->orderBy($this->sortBy, $this->direction);

        return (new PermissionCollection($query->paginate($this->limit)))
            ->additional($this->metaAdditional);
    }
}
