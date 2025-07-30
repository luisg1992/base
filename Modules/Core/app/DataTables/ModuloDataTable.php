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
use Modules\Core\Http\Resources\ModuloCollection;
use Modules\Core\Models\Modulo;

trait ModuloDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Módulos';
        $this->titulo_tabla = 'MÓDULOS';
        $this->nombre_tabla = 'Modulos';
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
            'generar' => Button::make()->label('MÓDULOS')->icon('ti ti-hexagons')->action("modulos_grafico")
        ];

        $builder = new ButtonBuilder();
        $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones);
        $builder->agregarBoton(Button::botonRecargar());

        return $builder->getButtons();
    }

    private function obtenerColumnasTabla(): array
    {
        $builder = new TableBuilder();
        $builder->agregarColumna(Column::make('Etiqueta')->label('Etiqueta')->sortable(true));
        $builder->agregarColumna(Column::make('modulo_padre')->label('Padre')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::make('Descripcion')->label('Descripción')->sortable(true));
        $builder->agregarColumna(Column::make('Icono')->label('Icono')->sortable(true));
        $builder->agregarColumna(Column::make('Url')->label('URL')->sortable(true));
        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        $filter->agregarFiltro(
            Filter::makeInput('Etiqueta')
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
        $Etiqueta = optional($filters->firstWhere('name', 'Etiqueta'))['value'] ?? '';

        $query = Modulo::query()
            ->with('padre')
            ->when($Etiqueta, function (Builder $q) use ($Etiqueta) {
                $q->where(function ($subquery) use ($Etiqueta) {
                    $subquery->where('Etiqueta', 'LIKE', "%{$Etiqueta}%")
                        ->orWhere('Subtitulo', 'LIKE', "%{$Etiqueta}%")
                        ->orWhere('Descripcion', 'LIKE', "%{$Etiqueta}%");
                });
            });

        $query->orderBy('ModuloId', $this->direction);

        return (new ModuloCollection($query->paginate($this->limit)))
            ->additional($this->metaAdditional);
    }

}
