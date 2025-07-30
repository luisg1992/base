<?php

namespace App\DataTables\Traits;

use App\Models\Configuracion\DataTables\ConfiguracionDataTable;
use Illuminate\Http\Request;

trait PaginationTrait
{
    protected string $titulo_pagina;
    protected string $nombre_tabla;
    protected string $titulo_tabla;
    protected $visibleColumns;
    protected $columns;
    protected $filters;
    protected $sortBy;
    protected $descending;
    protected $direction;
    protected $limit;
    protected $metaAdditional;

    public function initPagination($sortBy = 'id', $direction = 'desc', $limit = 10)
    {
        $page_sizes = [10, 15, 20, 50, 100];
        array_push($page_sizes, $this->limit);
        $page_sizes = array_unique($page_sizes);

        return [
            'sort_by' => $sortBy,
            'descending' => ($direction === 'desc'),
            'limit' => $this->limit,
            'page_sizes' => $page_sizes
        ];
    }

    public function actualizaPaginacion()
    {
        $this->nombre_tabla = request()->input('nombre_tabla');
        $this->visibleColumns = request()->input('visibleColumns');
        $this->sortBy = request()->input('sortBy');
        $this->descending = request()->input('descending');
        $this->direction = $this->descending ? 'desc' : 'asc';
        $this->limit = request()->input('limit');
        $this->metaAdditional = [
            'meta' => [
                'sort_by' => $this->sortBy,
                'descending' => $this->descending,
            ]
        ];

        $this->updateConfiguracionDataTable();
    }

    public function getConfiguracionDataTable()
    {
        $record = ConfiguracionDataTable::query()
            ->where('user_id', auth()->id())
            ->where('tabla', $this->nombre_tabla)
            ->first();

        if (!$record) {
            $columnas_visibles = [];
            foreach ($this->columns as $col) {
                if (key_exists('locked', $col) && !$col['locked']) {
                    $columnas_visibles[] = $col['name'];
                }
            }
            $record = ConfiguracionDataTable::query()->create([
                'user_id' => auth()->id(),
                'tabla' => $this->nombre_tabla,
                'columnas_visibles' => $columnas_visibles,
                'registros_por_pagina' => 10
            ]);
        }

        $this->limit = $record->registros_por_pagina;
        $this->visibleColumns = $record->columnas_visibles;
    }

    public function updateConfiguracionDataTable()
    {
        ConfiguracionDataTable::query()
            ->where('user_id', auth()->id())
            ->where('tabla', $this->nombre_tabla)
            ->update([
                'registros_por_pagina' => $this->limit
            ]);
    }

    public function actualizarVisibilidadColumnas(Request $request)
    {
        $nombre_tabla = $request->input('nombre_tabla');
        $columnas_visibles = $request->input('visible_columns');

        ConfiguracionDataTable::query()
            ->where('user_id', auth()->id())
            ->where('tabla', $nombre_tabla)
            ->update([
                'columnas_visibles' => json_encode($columnas_visibles)
            ]);
    }
}