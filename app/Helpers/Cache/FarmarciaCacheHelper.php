<?php

use App\Core\Services\StoredProcedureService;
use Illuminate\Support\Facades\Cache;
use Modules\Farmacia\Models\FarmaciaAlmacen;
use Modules\Farmacia\Models\FarmaciaEstadoMovimiento;
use Modules\Farmacia\Models\FarmaciaTipoCompra;
use Modules\Farmacia\Models\FarmaciaTipoConcepto;
use Modules\Farmacia\Models\FarmaciaTipoDocumento;
use Modules\Farmacia\Models\FarmaciaTipoProceso;

if (!function_exists('cache_fact_catalogo_servicios_especialidades')) {
    function cache_fact_catalogo_servicios_especialidades()
    {
        $sp = new StoredProcedureService();
        $params = ['', '', '2'];

        if (Cache::has('cache_fact_catalogo_servicios_especialidades')) {
            return Cache::get('cache_fact_catalogo_servicios_especialidades');
        }

        $records = collect($sp->executeSinPaginacion('dbo.WebS_FactCatalogoServiciosSeleccionarPorCodigoOnombreTipoCatalogo', $params))
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdProducto,
                    'value' => (int)$row->IdProducto,
                    'label' => (int)$row->Nombre,
                    'IdProducto' => (int)$row->IdProducto,
                    'Codigo' => $row->Codigo,
                    'Nombre' => $row->Nombre
                ];
            });

        Cache::put('cache_fact_catalogo_servicios_especialidades', $records, now()->addHours(24));

        return $records;
    }
}

if (!function_exists('cache_farmacia_almacenes')) {
    function cache_farmacia_almacenes()
    {
        if (Cache::has('cache_farmacia_almacenes')) {
            return Cache::get('cache_farmacia_almacenes');
        }

        $records = FarmaciaAlmacen::query()
            ->whereIn('idTipoLocales', ['A', 'F'])
            ->whereActivo()
            ->orderBy('descripcion', 'ASC')
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => $row->idAlmacen,
                    'label' => str_to_upper_utf8($row->descripcion),
                    'idTipoLocales' => $row->idTipoLocales,
                    'idTipoSuministro' => $row->idTipoSuministro,
                ];
            });
        Cache::put('cache_farmacia_almacenes', $records, now()->addHours(24));

        return $records;
    }
}

if (!function_exists('cache_farmacia_tipo_conceptos')) {
    function cache_farmacia_tipo_conceptos()
    {
        if (Cache::has('cache_farmacia_tipo_conceptos')) {
            return Cache::get('cache_farmacia_tipo_conceptos');
        }

        $records = FarmaciaTipoConcepto::query()
            ->orderBy('Concepto')
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => $row->idTipoConcepto,
                    'label' => str_to_upper_utf8($row->Concepto),
                ];
            });
        Cache::put('cache_farmacia_tipo_conceptos', $records, now()->addHours(24));

        return $records;
    }
}

if (!function_exists('cache_farmacia_tipo_documentos')) {
    function cache_farmacia_tipo_documentos()
    {
        if (Cache::has('cache_farmacia_tipo_documentos')) {
            return Cache::get('cache_farmacia_tipo_documentos');
        }

        $records = FarmaciaTipoDocumento::query()
            ->orderBy('Nombre')
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => $row->idTipoDocumento,
                    'label' => str_to_upper_utf8($row->Nombre),
                ];
            });
        Cache::put('cache_farmacia_tipo_documentos', $records, now()->addHours(24));

        return $records;
    }
}

if (!function_exists('cache_farmacia_tipo_procesos')) {
    function cache_farmacia_tipo_procesos()
    {
        if (Cache::has('cache_farmacia_tipo_procesos')) {
            return Cache::get('cache_farmacia_tipo_procesos');
        }

        $records = FarmaciaTipoProceso::query()
            ->orderBy('descripcion')
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => $row->idTipoProceso,
                    'label' => str_to_upper_utf8($row->descripcion),
                ];
            });
        Cache::put('cache_farmacia_tipo_procesos', $records, now()->addHours(24));

        return $records;
    }
}

if (!function_exists('cache_farmacia_tipo_compras')) {
    function cache_farmacia_tipo_compras()
    {
        if (Cache::has('cache_farmacia_tipo_compras')) {
            return Cache::get('cache_farmacia_tipo_compras');
        }

        $records = FarmaciaTipoCompra::query()
            ->orderBy('descripcion')
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => $row->idTipoCompra,
                    'label' => str_to_upper_utf8($row->descripcion),
                ];
            });
        Cache::put('cache_farmacia_tipo_compras', $records, now()->addHours(24));

        return $records;
    }
}

if (!function_exists('cache_farmacia_estado_movimientos')) {
    function cache_farmacia_estado_movimientos()
    {
        if (Cache::has('cache_farmacia_estado_movimientos')) {
            return Cache::get('cache_farmacia_estado_movimientos');
        }

        $records = FarmaciaEstadoMovimiento::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => $row->idEstadoMovimiento,
                    'label' => str_to_upper_utf8($row->Estado),
                ];
            });
        Cache::put('cache_farmacia_estado_movimientos', $records, now()->addHours(24));

        return $records;
    }
}
