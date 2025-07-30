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
use Modules\Core\Http\Resources\SmsCelularCollection;
use Modules\Core\Models\SmsCelular;

trait SmsCelularDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Número de Celulares';
        $this->titulo_tabla = 'Número de Celulares';
        $this->nombre_tabla = 'sms_celulares';
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
        $builder->agregarColumna(Column::make('Url')->label('Url'));
        $builder->agregarColumna(Column::make('Token')->label('Token'));
        $builder->agregarColumna(Column::make('Usuario')->label('Usuario'));
        $builder->agregarColumna(Column::make('Celular')->label('Celular'));
        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        $filter->agregarFiltro(
            Filter::makeInput('search')
                ->label('Buscar')
                ->cssClass('col-12')
                ->placeholder('Ingrese usuario o celular y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $search = optional($filters->firstWhere('name', 'search'))['value'] ?? '';

        $query = SmsCelular::query()
            ->where('Usuario', 'LIKE', "%{$search}%")
            ->orWhere('Celular', 'LIKE', "%{$search}%");

        $query->orderBy('IdSmsCelular', $this->direction);

        return (new SmsCelularCollection($query->paginate($this->limit)))
            ->additional($this->metaAdditional);
    }

}
