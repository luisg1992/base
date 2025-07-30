<?php

namespace Modules\Auditoria\DataTables\Login;

use App\Core\Table\Column;
use App\Core\Table\Filter;
use App\Core\Table\FilterBuilder;
use App\Core\Table\TableBuilder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\DataTables\Traits\PaginationTrait;
use Modules\Auditoria\Http\Resources\Login\LoginSessionCollection;
use Modules\Auditoria\Models\Login\LoginSession;

trait LoginSessionDataTable
{
    use PaginationTrait;

    public function inicializarTabla()
    {
        $this->titulo_pagina = config('app.name') . ' | Auditoría de Login';
        $this->titulo_tabla = 'AUDITORÍA DE LOGIN';
        $this->nombre_tabla = 'sesiones_login';
        $this->columns = $this->getColumns();
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
        ];
    }


    private function getColumns(): array
    {
        $builder = new TableBuilder();

        $builder->agregarColumna(Column::make('usuario')->label('USUARIO')->sortable(true));
        $builder->agregarColumna(Column::make('razon')->label('OBSERVACIÓN')->sortable(true));
        $builder->agregarColumna(Column::make('hora_inicio_sesion')->label('INICIO SESION')->sortable(true));
        $builder->agregarColumna(Column::make('hora_cierre_sesion')->label('CIERRE SESION')->sortable(true));
        $builder->agregarColumna(Column::actions());

        return $builder->obtenerColumnasTabla();
    }

    public function obtenerCamposFiltro(): array
    {
        $filter = new FilterBuilder();
        $filter->agregarFiltro(
            Filter::makeInput('usuario')
                ->label('Usuario')
                ->cssClass('col-md-4 col-sm-6')
                ->placeholder('Ingrese el nombre de usuario')
        );

        $filter->agregarFiltro(
            Filter::makeInput('fecha_desde')
                ->label('Fecha Inicio')
                ->type('date')
                ->cssClass('col-md-4 col-sm-6')
        );

        $filter->agregarFiltro(
            Filter::makeInput('fecha_hasta')
                ->label('Fecha Fin')
                ->type('date')
                ->cssClass('col-md-4 col-sm-6')
        );

        return $filter->obtenerCamposFiltro();
    }

    public function records(Request $request)
    {
        $this->actualizaPaginacion();

        $filters = collect($request->input('filters'));
        $usuario = optional($filters->firstWhere('name', 'usuario'))['value'] ?? '';
        $fecha_desde = optional($filters->firstWhere('name', 'fecha_desde'))['value'] ?? '';
        $fecha_hasta = optional($filters->firstWhere('name', 'fecha_hasta'))['value'] ?? '';

        $query = LoginSession::query()
            ->with('user')
            ->when($usuario, function (Builder $q) use ($usuario) {
                $q->whereHas('user', function (Builder $q2) use ($usuario) {
                    $q2->where('name', 'LIKE', "%{$usuario}%");
                });
            });

        if ($fecha_desde) {
            $query->whereDate('hora_inicio_sesion', '>=', $fecha_desde);
        }

        if ($fecha_hasta) {
            $query->whereDate('hora_inicio_sesion', '<=', $fecha_hasta);
        }

        $query->orderBy($this->sortBy, $this->direction);

        return (new LoginSessionCollection($query->paginate($this->limit)))
            ->additional($this->metaAdditional);
    }
}
