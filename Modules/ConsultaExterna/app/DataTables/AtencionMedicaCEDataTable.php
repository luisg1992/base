<?php

namespace Modules\ConsultaExterna\DataTables;

use App\Core\Table\Button;
use App\Core\Table\Column;
use App\Core\Table\Filter;
use Illuminate\Http\Request;
use App\Helpers\ModuloHelper;
use App\Core\Table\TableBuilder;
use App\Core\Table\FilterBuilder;
use App\Core\Table\ButtonBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\DataTables\Traits\PaginationTrait;
use App\Core\Services\StoredProcedureService;
use Modules\ConsultaExterna\Http\Resources\AtencionMedicaCECollection;

trait AtencionMedicaCEDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Atención CE';
        $this->titulo_tabla = 'ATENCIÓN CONSULTA EXTERNA';
        $this->nombre_tabla = 'AtencionCe';
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
            // 'crear' => Button::botonCrear()->label('Nuevo'),
        ];
        $builder = new ButtonBuilder();
        $builder->agregarBotonesDesdeAcciones(auth()->user(), $valorPermission, $acciones);
        $builder->agregarBoton(Button::botonRecargar());

        return $builder->getButtons();
    }

    private function obtenerColumnasTabla(): array
    {
        $builder = new TableBuilder();
        $builder->agregarColumna(Column::make('ImagenFoto')->label('Foto'));
        $builder->agregarColumna(Column::make('OrigenCita')->label('ORIGEN')->sortable(true));
        // $builder->agregarColumna(Column::make('IdCuentaAtencion')->label('CUENTA')->sortable(true));
        $builder->agregarColumna(Column::make('HoraAtencionInicio')->label('H.ATENCIÓN')->sortable(true));
        $builder->agregarColumna(Column::make('Paciente')->label('PACIENTE')->sortable(true));
        $builder->agregarColumna(Column::make('Servicio')->label('CITA')->sortable(true));
        $builder->agregarColumna(Column::make('Firmado')->label('FIRMADO')->sortable(true));
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
                ->ajaxUrl('/consulta-externa/atencion-medica/servicios/programados/por-fecha')
                ->target('IdServicio') // Indica que actualizará el campo 'IdServicio'
        );

        $filter->agregarFiltro(
            Filter::makeSelect('IdServicio')
                ->label('Servicio')
                ->cssClass('col-12 col-sm-6 col-md-4 col-lg-4')
                ->options($this->obtenerTurnosServiciosProgramados(null)) // Opciones vacías inicialmente
                ->includeAllOption(false)
                ->placeholder('Seleccione un Servicio...')
        );

        $filter->agregarFiltro(
            Filter::makeInputHidden('IdTipoServicio')->value('1')
        );

        return $filter->obtenerCamposFiltro();
    }

    private function obtenerTurnosServiciosProgramados($fecha): array
    {
        $canPermission = ModuloHelper::obtenerPermisoBaseDesdeRuta();
        if (Auth::user()->can($canPermission . '.seleccionar.servicios')) {
            $idEmpleado = NULL;
        } else {
            $idEmpleado = Auth::user()->IdEmpleado;
        }
        $fechaFormateada = $fecha ? formatear_fecha($fecha) : now()->format('Y-m-d');
        $servicios = DB::select('EXEC WebS_Atenciones_Servicios_Listar ?, ?', [
            $idEmpleado ?? null,
            $fechaFormateada
        ]);

        return collect($servicios)->map(function ($item) {
            return [
                'id' => (int)$item->IdServicio . '-' . (int)$item->IdTipoTurno,
                'value' => (int)$item->IdServicio . '-' . (int)$item->IdTipoTurno,
                'IdServicio' => (int)$item->IdServicio,
                'IdTipoTurnoRef' => (int)$item->IdTipoTurno,
                'Turno' => $item->Turnos,
                'label' => str_to_upper_utf8($item->Servicio . ' (' . $item->Turnos . ')')
            ];
        })->toArray();
    }


    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        return (new AtencionMedicaCECollection($this->getFilterRecords($request)));
    }

    private function getFilterRecords(Request $request)
    {
        $canPermission = ModuloHelper::obtenerPermisoBaseDesdeRuta();
        $filters = collect($request->input('filters'));
        $IdServicio = null;
        $IdTipoTurno = null;
        $IdTipoServicio = null;
        $Fecha = null;

        foreach ($filters as $filter) {
            $value = key_exists('value', $filter) ? $filter['value'] : '';
            if (is_null($value) || $value === '') {
                continue;
            }
            switch ($filter['name']) {
                case 'IdServicio':
                    [$IdServicio, $IdTipoTurno] = explode('-', $value);
                    break;
                case 'IdTipoServicio':
                    $IdTipoServicio = $value;
                    break;
                case 'Fecha':
                    $Fecha = $value;
                    break;
                default:
                    break;
            }
        }

        if (Auth::user()->can($canPermission . '.seleccionar.servicios')) {
            $idEmpleado = null;
        } else {
            $idEmpleado = Auth::user()->IdEmpleado;
        }

        $sp = new StoredProcedureService();
        $params = [
            $idEmpleado,
            $IdTipoServicio,
            $IdServicio,
            $IdTipoTurno,
            $Fecha,
            $request->input('limit', 50),
            $request->input('page'),
        ];
        return $sp->execute($request, 'dbo.WebS_Atenciones_Listar_Filtro', $params);
    }
}
