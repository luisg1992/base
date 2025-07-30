<?php

namespace Modules\Farmacia\DataTables;

use App\Core\Table\Button;
use App\Core\Table\Column;
use App\Core\Table\Filter;
use App\Core\Table\TableBuilder;
use App\Core\Table\FilterBuilder;
use App\Core\Table\ButtonBuilder;
use App\DataTables\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Modules\Farmacia\Http\Resources\FarmaciaAlmacenCollection;
use Modules\Farmacia\Models\FarmaciaAlmacen;

trait FarmaciaAlmacenDataTable
{
    use PaginationTrait;

    public function initTable(): array
    {
        $this->titulo_pagina = config('app.name') . ' | Farmacias';
        $this->nombre_tabla = 'farm_almacen';
        $this->titulo_tabla = 'Listado de farmacias';
        $this->columns = $this->obtenerColumnasTabla();
        $this->filters = $this->obtenerCamposFiltro();
        $this->getConfiguracionDataTable();

        return [
            'titulo_pagina' => $this->titulo_pagina,
            'titulo_tabla' => $this->titulo_tabla,
            'nombre_tabla' => $this->nombre_tabla,
            'columns' => $this->columns,
            'filters' => $this->filters,
            'visible_columns' => $this->visibleColumns,
            'pagination' => $this->initPagination(),
            'header_buttons' => $this->obtenerBotonesCabecera(),
        ];
    }

    private function obtenerBotonesCabecera(): array
    {
        $builder = new ButtonBuilder();
        $builder->agregarBoton(Button::botonCrear());

        return $builder->getButtons();
    }

    private function obtenerColumnasTabla(): array
    {
        $builder = new TableBuilder();

        $builder->agregarColumna(Column::make('id')->label('Código'));
        $builder->agregarColumna(Column::make('descripcion')->label('Description'));
        $builder->agregarColumna(Column::make('codigo_sismed')->label('Codigo SISMED'));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        $filter->agregarFiltro(Filter::makeInput('descripcion')
            ->label('Description')
            ->cssClass('col-24'));

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $query = $this->getFilterRecords();

        return (new FarmaciaAlmacenCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }

    private function getFilterRecords()
    {
        $query = FarmaciaAlmacen::query()
            ->where('idTipoLocales', 'F');
        foreach ($this->requestFilters as $filter => $value) {
            if (is_null($value) || $value === '') {
                continue;
            }
            switch ($filter) {
                case 'descripcion':
                    $query->where('descripcion', 'like', "%{$value}%");
                    break;
                default:
                    break;
            }
        }

        if (!is_null($this->sortBy) && $this->sortBy !== '') {
            $query->orderBy($this->sortBy, $this->direction);
        }

        return $query;
    }
}
