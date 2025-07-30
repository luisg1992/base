<?php

namespace Modules\Imagenologia\Datatables;

use App\DataTables\Traits\PaginationTrait;
use App\Core\Table\Button;
use App\Core\Table\Column;
use App\Core\Table\Filter;
use Illuminate\Http\Request;
use App\Core\Table\TableBuilder;
use App\Core\Table\FilterBuilder;
use App\Core\Table\ButtonBuilder;
use Illuminate\Database\Eloquent\Builder;
use Modules\Imagenologia\Models\RecetaCabecera;
use Modules\Imagenologia\Http\Resources\ListaEsperaCollection;

trait ListaEsperaDataTable
{
    use PaginationTrait;
    
    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Lista De espera ';
        $this->titulo_tabla = 'Lista de Espera';
        $this->nombre_tabla = 'Lista de Espera';
        $this->columns = $this->obtenerColumnasTabla();
        $this->getConfiguracionDataTable();
        $this->filters = $this->obtenerCamposFiltro();

        return[
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
        $builder = new ButtonBuilder();
        /*$builder->agregarBoton(Button::botonCrear()->label('NUEVO'));

         $builder->agregarBoton(Button::make()->label('MÓDULOS')
        ->icon('ti ti-hexagons')
        ->action("modulos_grafico")
        ->disable(false)
        ->url(null));  */
        return $builder->getButtons();
    }


    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        // Filtro de Punto Carga
        $puntosCarga = cache_configuracion_punto_carga();
        $filter->agregarFiltro(
            Filter::makeSelect('IdPuntoCarga')
                ->label('Punto de Carga')
                ->cssClass('col-12 col-sm-6 col-md-4 col-lg-4')
                ->options($puntosCarga)
                ->includeAllOption(false)
                ->placeholder('Seleccione un Punto de Carga...')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $IdPuntoCarga = optional($filters->firstWhere('name', 'IdPuntoCarga'))['value'] ?? '';
        $fecha_desde = optional($filters->firstWhere('name', 'fecha_desde'))['value'] ?? '';
        $fecha_hasta = optional($filters->firstWhere('name', 'fecha_hasta'))['value'] ?? '';

        $query = RecetaCabecera::query()
            ->with('factPuntoCarga')
            ->with('recetaDetalle')
            ->with('facturacionCuentasAtencion.paciente')
            ->when($IdPuntoCarga, function (Builder $q) use ($IdPuntoCarga) {
                $q->where(function ($subquery) use ($IdPuntoCarga) {
                    $subquery->where('IdPuntoCarga', '=', "{$IdPuntoCarga}");
                });
            });

         $query->orderBy('idReceta', 'desc');

       /*     if ($fecha_desde) {
            $query->whereDate('FechaReceta', '>=', $fecha_desde);
        }

        if ($fecha_hasta) {
            $query->whereDate('FechaReceta', '<=', $fecha_hasta);
        }

*/
        return (new ListaEsperaCollection($query->paginate($this->limit)))
            ->additional($this->metaAdditional);
    }
    

    private function obtenerColumnasTabla(): array
    {
        $builder = new TableBuilder();
        $builder->agregarColumna(Column::make('id')->label('Receta Id')->sortable(true));
        $builder->agregarColumna(Column::make('Descripcion')->label('Punto Carga')->sortable(true));    
        $builder->agregarColumna(Column::make('FechaReceta')->label('Fecha')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::make('idItem')->label('IdItem')->sortable(true));
        $builder->agregarColumna(Column::make('Paciente')->label('Paciente')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }
}
