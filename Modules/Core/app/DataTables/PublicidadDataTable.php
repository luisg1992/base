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
use App\DataTables\Traits\PaginationTrait;
use Modules\Core\Http\Resources\PublicidadCollection;
use Modules\Core\Models\Publicidad;

trait PublicidadDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Publicidad';
        $this->titulo_tabla = 'Publicidad';
        $this->nombre_tabla = 'publicidad';
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
        $builder->agregarBoton(Button::botonCrear()->label('Nuevo'));
        $builder->agregarBoton(Button::botonRecargar());
//        $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission,$acciones);

        return $builder->getButtons();
    }

    private function obtenerColumnasTabla(): array
    {
        $builder = new TableBuilder();
//        $builder->agregarColumna(Column::make('Titulo')->label('Título'));
        $builder->agregarColumna(Column::make('Descripcion')->label('Descripción'));
        $builder->agregarColumna(Column::make('TamanoLetra')->label('Tamaño de letra'));
        $builder->agregarColumna(Column::make('Posicion')->label('Posición'));
        $builder->agregarColumna(Column::make('Tipo')->label('Tipo'));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        $filter->agregarFiltro(
            Filter::makeInput('Descripcion')
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
        $descripcion = optional($filters->firstWhere('name', 'Descripcion'))['value'] ?? '';

        $query = Publicidad::query()
            ->with('tipo')
            ->where('Descripcion', 'LIKE', "%{$descripcion}%");

        $query->orderBy('IdPublicidad', $this->direction);

        return (new PublicidadCollection($query->paginate($this->limit)))
            ->additional($this->metaAdditional);
    }

}
