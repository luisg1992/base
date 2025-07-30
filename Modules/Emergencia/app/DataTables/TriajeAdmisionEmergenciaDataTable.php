<?php

namespace Modules\Emergencia\DataTables;

use App\Cache\Configuracion\TipoGravedadAtencionCache;
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
use Modules\Emergencia\Http\Resources\TriajeAdmisionEmergenciaCollection;
use Modules\Emergencia\Models\TriajeEmergencia;
use Modules\Emergencia\Http\Resources\TriajeEmergenciaCollection;

trait TriajeAdmisionEmergenciaDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Triaje Emergencia';
        $this->titulo_tabla = '';
        $this->nombre_tabla = 'TriajeAdmisionEmergencia';
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
        $builder->agregarColumna(Column::make('CodigoTriaje')->label('Codigo Triaje'));
        $builder->agregarColumna(Column::make('FechaHora')->label('Fecha'));
        $builder->agregarColumna(Column::make('FechaHoraAdmision')->label('Fecha Admisión'));
        $builder->agregarColumna(Column::make('Paciente')->label('Paciente'));
        $builder->agregarColumna(Column::make('Prioridad')->label('Prioridad'));
        $builder->agregarColumna(Column::make('Servicio')->label('Servicio'));
        $builder->agregarColumna(Column::make('FormaIngreso')->label('Forma Ingreso'));
        $builder->agregarColumna(Column::make('EstadoIngreso')->label('Estado Ingreso'));
        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador'));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();

        $filter->agregarFiltro(
            Filter::makeInputDate('mes')
                ->label('Fecha')
                ->cssClass('col-12 col-sm-6 col-md-4 col-lg-3')
                ->placeholder('Seleccione una Fecha')
                ->value(date('m/Y'))
        );

        $filter->agregarFiltro(
            Filter::makeSelect('estado')
                ->label('Estado')
                ->cssClass('col-3')
                ->options([
                    ['id' => 'all', 'label' => 'Todos'],
                    ['id' => '1', 'label' => 'Sin admitir'],
                    ['id' => '2', 'label' => 'Admitidos'],
                ])
                ->value('1')
        );
        $prioridades = collect(TipoGravedadAtencionCache::getCache())
            ->where('id', '<', 5)
            ->values()
            ->toArray();
        array_unshift($prioridades, ['id' => 'all', 'label' => 'Todos']);

        $filter->agregarFiltro(
            Filter::makeSelect('prioridad')
                ->label('Prioridad')
                ->cssClass('col-3')
                ->options($prioridades)
                ->value('all')
        );
        $filter->agregarFiltro(
            Filter::makeInput('Paciente')
                ->label('Paciente')
                ->cssClass('col-3')
                ->placeholder('Ingrese una descripción y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $Paciente = optional($filters->firstWhere('name', 'Paciente'))['value'] ?? '';
        $Estado = optional($filters->firstWhere('name', 'estado'))['value'] ?? 'all';
        $Prioridad = optional($filters->firstWhere('name', 'prioridad'))['value'] ?? 'all';
        $Mes = optional($filters->firstWhere('name', 'mes'))['value'] ?? date('m/Y');

        $query = TriajeEmergencia::query()
            ->with(['servicio', 'tipoGravedad', 'tipoDocTriaje', 'motivoIngreso', 'formaIngreso', 'estadoIngreso', 'atencion'])
            ->when($Estado !== 'all', function ($q) use ($Estado) {
                if ($Estado == '1') {
                    $q->whereNull('IdAtencion');
                } elseif ($Estado == '2') {
                    $q->whereNotNull('IdAtencion');
                }
            })
            ->whereDate('TriajeFecha', $Mes)
            ->when($Paciente, function (Builder $q) use ($Paciente) {
                $q->where(function ($subquery) use ($Paciente) {
                    $subquery->where('NroDocTriaje', 'LIKE', "%{$Paciente}%")
                        ->orWhere('PrimerNombreTriaje', 'LIKE', "%{$Paciente}%")
                        ->orWhere('ApellidoPaternoTriaje', 'LIKE', "%{$Paciente}%")
                        ->orWhere('ApellidoMaternoTriaje', 'LIKE', "%{$Paciente}%");
                });
            })
            ->when($Prioridad !== 'all', function ($q) use ($Prioridad) {
                $q->where('IdTipoGravedad', $Prioridad);
            });

        $query->orderBy('IdTriajeEmergencia', 'desc');
        return (new TriajeAdmisionEmergenciaCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
