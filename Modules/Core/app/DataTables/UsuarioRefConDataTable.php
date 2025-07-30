<?php

namespace Modules\Core\DataTables;

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
use Modules\Core\Http\Resources\UsuarioRefConCollection;
use Modules\Core\Models\UsuarioRefCon;

trait UsuarioRefConDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Usuarios RefCon';
        $this->titulo_tabla = 'USUARIOS REFCON';
        $this->nombre_tabla = 'UsuarioRefCon';
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
        $builder->agregarColumna(Column::make('Nombres')->label('Nombres')->sortable(true));
        $builder->agregarColumna(Column::make('Usuario')->label('Usuario')->sortable(true));
        $builder->agregarColumna(Column::make('Clave')->label('Clave')->sortable(true));
        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter->agregarFiltro(
            Filter::makeInput('Nombres')
                ->label('Nombres')
                ->cssClass('col-12')
                ->placeholder('Ingrese un Nombre y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $Nombres = optional($filters->firstWhere('name', 'Nombres'))['value'] ?? '';

        /* $query = CitaAnuladaMotivo::query()
            ->when($Descripcion, fn(Builder $q) => $q->where('Descripcion', 'like', "%$Descripcion%")); */
        $query = UsuarioRefCon::query()
            ->when($Nombres, function (Builder $q) use ($Nombres) {

                $q->where(function ($subquery) use ($Nombres) {
                    $subquery->where('Nombres', 'LIKE', "%{$Nombres}%");
                });
            });

        $query->orderBy('IdUsuarioRefCon');
        return (new UsuarioRefConCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
