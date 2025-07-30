<?php

namespace Modules\Emergencia\DataTables;

use App\Cache\Configuracion\TipoGravedadAtencionCache;
use App\Core\Services\StoredProcedureService;
use App\Core\Table\Button;
use App\Core\Table\Column;
use App\Core\Table\Filter;
use App\Helpers\ModuloHelper;
use Illuminate\Http\Request;
use App\Core\Table\TableBuilder;
use App\Core\Table\FilterBuilder;
use App\Core\Table\ButtonBuilder;
use App\DataTables\Traits\PaginationTrait;
use Modules\Emergencia\Http\Resources\AdmisionEmergenciaCollection;
use Modules\Emergencia\Http\Resources\AtencionMedicaEmergenciaCollection;

trait AtencionMedicaEmergenciaDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Atención Medica Emergencia';
        $this->titulo_tabla = 'Atención Medica Emergencia';
        $this->nombre_tabla = 'AtencionMedicaEmergencia';
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
        $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission, $acciones);
        $builder->agregarBoton(Button::botonRecargar());

        return $builder->getButtons();
    }

    private function obtenerColumnasTabla(): array
    {
        $builder = new TableBuilder();
        $builder->agregarColumna(Column::make('NroCuenta')->label('Nro. Cuenta'));
        $builder->agregarColumna(Column::make('Paciente')->label('Paciente'));
        $builder->agregarColumna(Column::make('FechaIngreso')->label('Fecha de ingreso'));
        $builder->agregarColumna(Column::make('FechaEgreso')->label('Fecha de egreso'));
        $builder->agregarColumna(Column::make('Prioridad')->label('Prioridad'));
        $builder->agregarColumna(Column::make('FechaEgresoAdministrativo')->label('Fecha de egreso administrativo'));
        $builder->agregarColumna(Column::make('FuenteFinanciamiento')->label('Fuente financimiento'));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        $filter->agregarFiltro(
            Filter::makeInputDate('Fecha')
                ->label('Fecha')
                ->cssClass('col-12 col-sm-6 col-md-4 col-lg-3')
                ->placeholder('Seleccione una Fecha')
        );

        $prioridades = collect(TipoGravedadAtencionCache::getCache())
            ->where('id', '<', 5)
            ->values()
            ->toArray();
        array_unshift($prioridades, ['id' => 'all', 'label' => 'Todos']);

        $filter->agregarFiltro(
            Filter::makeSelect('prioridad')
                ->label('Prioridad')
                ->cssClass('col-12 col-sm-6 col-md-4 col-lg-3')
                ->options($prioridades)
                ->value('all')
        );

        $filter->agregarFiltro(
            Filter::makeInput('Paciente')
                ->label('Paciente')
                ->cssClass('col-12 col-sm-6 col-md-4 col-lg-6')
                ->placeholder('Ingrese una descripción y presione Enter')
        );

        $filter->agregarFiltro(
            Filter::makeInputHidden('IdTipoServicio')->value(2)
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        return (new AtencionMedicaEmergenciaCollection($this->getFilterRecords($request)));
    }

    private function getFilterRecords(Request $request)
    {
        $filters = collect($request->input('filters'));
        $IdServicio = null;
        $IdTipoServicio = null;
        $Fecha = null;
        $Paciente = null;
        $Prioridad = null;

        foreach ($filters as $filter) {
            $value = key_exists('value', $filter) ? $filter['value'] : '';
            if (is_null($value) || $value === '') {
                continue;
            }
            switch ($filter['name']) {
                case 'IdServicio':
                    $IdServicio = $value;
                    break;
                case 'IdTipoServicio':
                    $IdTipoServicio = $value;
                    break;
                case 'Fecha':
                    $Fecha = $value;
                    break;
                case 'Paciente':
                    $Paciente = $value;
                    break;
                case 'prioridad':
                    $Prioridad = $value === 'all' ? null : $value;
                    break;
                default:
                    break;
            }
        }

        $sp = new StoredProcedureService();
        $params = [
            $IdTipoServicio,
            $IdServicio,
            $Fecha,
            $Prioridad,
            $Paciente,
            $request->input('limit', 50),
            $request->input('page'),
        ];

        return $sp->execute($request, 'dbo.WebS_AdmisionEmergenciaHospitalizacion_Listar_Filtro', $params);
    }
}
