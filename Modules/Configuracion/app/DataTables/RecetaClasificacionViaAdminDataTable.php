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
use Modules\Configuracion\Http\Resources\RecetaClasificacionViaAdminCollection;
use Modules\Configuracion\Http\Resources\RecetaViaAdministracionCollection;
use Modules\Configuracion\Http\Resources\TiposModalidadAsistenciaCollection;
use Modules\Configuracion\Models\RecetaClasificacionViaAdmin;
use Modules\Configuracion\Models\RecetaViaAdministracion;
use Modules\Configuracion\Models\TiposModalidadAsistencia;

trait RecetaClasificacionViaAdminDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Receta Clasificacion Via Administracion';
        $this->titulo_tabla = 'RECETA CLASIFICACION VIA ADMINISTRACION';
        $this->nombre_tabla = 'RecetaClasificacionViaAdmin';
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
        $builder->agregarColumna(Column::make('Categoria')->label('Categoria')->sortable(true));
        //$builder->agregarColumna(Column::make('Estado')->label('Estado')->type('indicador')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter->agregarFiltro(
            Filter::makeInput('Categoria')
                ->label('Categoria')
                ->cssClass('col-12')
                ->placeholder('Ingrese una categoria y presione Enter')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();
        $filters = collect($request->input('filters'));
        $Categoria = optional($filters->firstWhere('name', 'Categoria'))['value'] ?? '';

        /* $query = CitaAnuladaMotivo::query()
            ->when($Descripcion, fn(Builder $q) => $q->where('Descripcion', 'like', "%$Descripcion%")); */
        $query = RecetaClasificacionViaAdmin::query()
            ->when($Categoria, function (Builder $q) use ($Categoria) {

                $q->where(function ($subquery) use ($Categoria) {
                    $subquery->where('Categoria', 'LIKE', "%{$Categoria}%");
                });
            });

        $query->orderBy('IdCategoria');
        return (new RecetaClasificacionViaAdminCollection($query->paginate($this->limit)))->additional($this->metaAdditional);
    }
}
