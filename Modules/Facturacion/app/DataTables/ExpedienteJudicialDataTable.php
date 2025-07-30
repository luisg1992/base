<?php

namespace Modules\Facturacion\DataTables;

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
use Modules\Facturacion\Http\Resources\ExpedienteJudicialCollection;
use Modules\Facturacion\Models\ExpedienteJudicial;

trait ExpedienteJudicialDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Expedientes Judiciales';
        $this->titulo_tabla = 'EXPEDIENTES JUDICIALES';
        $this->nombre_tabla = 'ExpedientesJudiciales';
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
        $builder->agregarColumna(Column::make('Paciente')->label('Paciente')->sortable(true));
        $builder->agregarColumna(Column::make('NroDocumento')->label('N° Documento')->sortable(true));
        $builder->agregarColumna(Column::make('TipoFinancimiento')->label('Tipo Financimiento')->sortable(true));
        $builder->agregarColumna(Column::make('FuenteFinanciamiento')->label('Fuente Financiamiento')->sortable(true));

        $builder->agregarColumna(Column::make('Servicio')->label('Servicio')->sortable(true));
        $builder->agregarColumna(Column::make('Especialidad')->label('Especialidad')->sortable(true));
        $builder->agregarColumna(Column::make('ProgramaInstitucion')->label('Programa Institucion')->sortable(true));
        $builder->agregarColumna(Column::make('TipoDocumento')->label('Tipo Documento')->sortable(true));
        $builder->agregarColumna(Column::make('FechaDocumento')->label('Fecha Documento')->sortable(true));
        $builder->agregarColumna(Column::make('FechaVencimiento')->label('Fecha Vencimiento')->sortable(true));
        $builder->agregarColumna(Column::make('NumeroDocumento')->label('Numero Documento')->sortable(true));
        $builder->agregarColumna(Column::make('NumeroExpedienteTramiteDocumentario')->label('Nro Expediente')->sortable(true));
        $builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter->agregarFiltro(
            Filter::makeInput('Paciente')
                ->label('Paciente')
                ->cssClass('col-12')
                ->placeholder('Ingrese una descripción y presione Enter')
        );
       /*  $filter->agregarFiltro(
            Filter::makeInput('Carta')
                ->label('Carta')
                ->cssClass('col-6')
                ->placeholder('Ingrese una descripción y presione Enter')
        ); */

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $Paciente = optional($filters->firstWhere('name', 'Paciente'))['value'] ?? '';
        $Carta = optional($filters->firstWhere('name', 'Carta'))['value'] ?? '';

        /* $query = CitaAnuladaMotivo::query()
            ->when($Descripcion, fn(Builder $q) => $q->where('Descripcion', 'like', "%$Descripcion%")); */
        $query = ExpedienteJudicial::query()
            ->with(['paciente'])
            ->when($Paciente, function (Builder $q) use ($Paciente) {
                $q->whereHas('paciente', function ($subquery) use ($Paciente) {
                    $subquery->where('ApellidoPaterno', 'LIKE', "%{$Paciente}%")
                             ->orWhere('ApellidoMaterno', 'LIKE', "%{$Paciente}%")
                             ->orWhere('PrimerNombre', 'LIKE', "%{$Paciente}%")
                             ->orWhere('NroDocumento', 'LIKE', "%{$Paciente}%");
                });

            });
        /* if ($Carta) {
            # code...
            $query->where(function ($subquery) use ($Carta) {
                $subquery->where('NumeroPlaca', 'LIKE', "%{$Carta}%")
                         ->orWhere('NumeroPoliza', 'LIKE', "%{$Carta}%")
                         ->orWhere('NumeroSiniestro', 'LIKE', "%{$Carta}%");
            });
        } */

        $query->orderBy('IdProgramaExpedienteJudicial');
        return (new ExpedienteJudicialCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
