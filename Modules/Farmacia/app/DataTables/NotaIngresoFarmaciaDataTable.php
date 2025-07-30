<?php

namespace Modules\Farmacia\DataTables;

use App\Builders\Table\Button;
use App\Builders\Table\ButtonBuilder;
use App\Builders\Table\Column;
use App\Builders\Table\Filter;
use App\Builders\Table\FilterBuilder;
use App\Builders\Table\TableBuilder;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use Modules\Farmacia\Http\Resources\NotaIngresoFarmaciaCollection;
use Modules\Farmacia\Models\NotaIngresoFarmacia;

trait NotaIngresoFarmaciaDataTable
{
    use PaginationTrait;

    public function initTable(): array
    {
        $this->pageTitle = config('app.name') . ' | Notas de Ingreso Farmacias';
        $this->tableName = 'farmacias';
        $this->tableTitle = 'Listado de notas de ingreso a farmacias';
        $this->columns = $this->getColumns();
        $this->filters = $this->getFilters();
        $this->getConfigurationDataTable();

        return [
            'page_title' => $this->pageTitle,
            'table_name' => $this->tableName,
            'table_title' => $this->tableTitle,
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
        $builder->addButton(Button::newButton());

        return $builder->getButtons();
    }

    private function getColumns(): array
    {
        $builder = new TableBuilder();

        $builder->addColumn(Column::make('nombre')->label('Name'));
        $builder->addColumn(Column::actions());

        return $builder->getColumns();
    }

    public function getFilters(): array
    {
        $filter = new FilterBuilder();

        $filter->addFilter(Filter::makeInput('nombre')
            ->label('Name')
            ->cssClass('col-24'));

        return $filter->getFilters();
    }

    public function getRecords(Request $request)
    {
        $this->updatePagination($request);
        $query = $this->getFilterRecords();

        return (new NotaIngresoFarmaciaCollection($query->paginate($this->perPage)))->additional($this->metaAdditional);
    }

    private function getFilterRecords()
    {
        $query = NotaIngresoFarmacia::query();
        foreach ($this->requestFilters as $filter => $value) {
            if (is_null($value) || $value === '') {
                continue;
            }
            switch ($filter) {
                case 'nombre':
                    $query->where('nombre', 'like', "%{$value}%");
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
