<?php

namespace Modules\Configuracion\DataTables;

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
use Modules\Configuracion\Http\Resources\FarmTipoConceptoCollection;
use Modules\Configuracion\Models\FarmTipoConcepto;

trait FarmTipoConceptoDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Farmacia Tipo Concepto ';
        $this->titulo_tabla = 'FARMACIA TIPO CONCEPTO';
        $this->nombre_tabla = 'farmTipoConceptos';
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
        $builder->agregarColumna(Column::make('Concepto')->label('Concepto')->sortable(true));
        $builder->agregarColumna(Column::make('codigoMINSA')->label('codigoMINSA')->sortable(true));
        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter->agregarFiltro(
            Filter::makeInput('Concepto')
                ->label('Concepto')
                ->cssClass('col-12')
                ->placeholder('Ingrese una descripción y presione Enter')
        );
        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $Concepto = optional($filters->firstWhere('name', 'Concepto'))['value'] ?? '';

        $query = FarmTipoConcepto::query()
            ->when($Concepto, function (Builder $q) use ($Concepto) {
                $q->where(function ($subquery) use ($Concepto) {
                    $subquery->where('Concepto', 'LIKE', "%{$Concepto}%");
                });
            });
        $query->orderBy('idTipoConcepto');
        return (new FarmTipoConceptoCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
