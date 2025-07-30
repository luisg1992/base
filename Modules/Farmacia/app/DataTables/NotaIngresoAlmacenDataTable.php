<?php

namespace Modules\Farmacia\DataTables;

use App\Core\Services\StoredProcedureService;
use App\Core\Table\Button;
use App\Core\Table\Column;
use App\Core\Table\Filter;
use App\Core\Table\TableBuilder;
use App\Core\Table\FilterBuilder;
use App\Core\Table\ButtonBuilder;
use App\DataTables\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Modules\Farmacia\Http\Resources\NotaIngresoAlmacenCollection;
use Modules\Farmacia\Models\FarmaciaAlmacen;

trait NotaIngresoAlmacenDataTable
{
    use PaginationTrait;

    public function inicializarTabla(): array
    {
        $this->titulo_pagina = config('app.name') . ' | Notas de ingreso almacén';
        $this->nombre_tabla = 'notas_ingreso_almacen';
        $this->titulo_tabla = 'Notas de ingreso almacén';
        $this->columns = $this->getColumns();
        $this->filters = $this->getFilters();
        $this->getConfiguracionDataTable();

        return [
            'titulo_pagina' => $this->titulo_pagina,
            'titulo_tabla' => $this->titulo_tabla,
            'nombre_tabla' => $this->nombre_tabla,
            'columns' => $this->columns,
            'filters' => $this->filters,
            'visible_columns' => $this->visibleColumns,
            'pagination' => $this->initPagination(),
            'header_buttons' => $this->getHeaderButtons(),
        ];
    }

    private function getHeaderButtons(): array
    {
        $builder = new ButtonBuilder();
        $builder->agregarBoton(Button::botonCrear());

        return $builder->getButtons();
    }

    private function getColumns(): array
    {
        $builder = new TableBuilder();

        $builder->agregarColumna(Column::make('MovNumero')->label('MovNumero'));
        $builder->agregarColumna(Column::make('MovTipo')->label('Tipo'));
        $builder->agregarColumna(Column::make('FDestino')->label('Destino'));
        $builder->agregarColumna(Column::make('FOrigen')->label('Origen'));
        $builder->agregarColumna(Column::make('Abreviatura')->label('Doc.Tipo'));
        $builder->agregarColumna(Column::make('DocumentoNumero')->label('Doc.Número'));
        $builder->agregarColumna(Column::make('FechaCreacion')->label('Fecha'));
        $builder->agregarColumna(Column::make('Estado')->label('Estado'));
        $builder->agregarColumna(Column::make('Concepto')->label('Concepto'));
        $builder->agregarColumna(Column::make('Total')->label('Total')->align('right'));
        $builder->agregarColumna(Column::make('NroCuenta')->label('Nro.Cuenta'));
        $builder->agregarColumna(Column::make('Observaciones')->label('Observaciones'));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function getFilters(): array
    {
        $filter = new FilterBuilder();

        $almacenes = FarmaciaAlmacen::query()
            ->where('idTipoLocales', 'A')
            ->whereActivo()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->idAlmacen,
                    'label' => $row->descripcion,
                ];
            });

        $filter->agregarFiltro(Filter::makeSelect('idAlmacen')
            ->options($almacenes)
            ->includeAllOption(false)
            ->label('Almacén')
            ->default('')
            ->cssClass('col-6'));

        $filter->agregarFiltro(Filter::makeInput('nroCuenta')
            ->label('Nro.Cuenta')
            ->cssClass('col-6'));

        return $filter->obtenerCamposFiltro();
    }

    public function getRecords(Request $request)
    {
        $this->actualizaPaginacion();

         return (new NotaIngresoAlmacenCollection($this->getFilterRecords($request)));
    }

    private function getFilterRecords(Request $request)
    {
        $filters = collect($request->input('filters'));
        $idAlmacen = null;
        $nroCuenta = '';
        $observaciones = '';

        foreach ($filters as $filter) {
            $value = key_exists('value', $filter)?$filter['value']:'';
            if (is_null($value) || $value === '') {
                continue;
            }
            switch ($filter['name']) {
                case 'idAlmacen':
                    $idAlmacen = $value;
                    break;
                case 'nroCuenta':
                    $nroCuenta = $value;
                    break;
                case 'observaciones':
                    $observaciones = $value;
                    break;
                default:
                    break;
            }
        }

        $sp = new StoredProcedureService();
        $params = [
            '2025-04-01 00:00:00',
            $idAlmacen,
            '2025-04-21 23:59:00',
            $nroCuenta,
            $observaciones,
            $request->input('limit'),
            $request->input('page'),
        ];

        return $sp->execute($request, 'dbo.WebS_NotaIngresoAlmacen_BuscarFiltro', $params);
    }
}
