<?php

use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\Parametro;
use Modules\Core\Models\UsuarioRefCon;

/*Parametros*/
if (!function_exists('cache_core_parametros')) {
    function cache_core_parametros()
    {
        if (Cache::has('cache_core_parametros')) {
            return Cache::get('cache_core_parametros');
        }

        $records = Parametro::query()->get();
        Cache::put('cache_core_parametros', $records, now()->addHours(24));
        return $records;
    }
}
if (!function_exists('cache_core_parametros_limpiar')) {
    function cache_core_parametros_limpiar()
    {
        Cache::forget('cache_core_parametros_limpiar');
        cache_core_parametros();
    }
}

if (!function_exists('cache_core_usuario_ref_con')) {
    function cache_core_usuario_ref_con()
    {
        if (Cache::has('cache_core_usuario_ref_con')) {
            return Cache::get('cache_core_usuario_ref_con');
        }

        $records = UsuarioRefCon::query()
            ->where('Estado', 1)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdUsuarioRefCon,
                    'value' => (int)$row->IdUsuarioRefCon,
                    'nombres' => str_to_upper_utf8($row->Nombres),
                    'usuario' => str_to_upper_utf8($row->Usuario),
                    'clave' => str_to_upper_utf8($row->Clave)
                ];
            });
        Cache::put('cache_core_usuario_ref_con', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_core_usuario_ref_con_limpiar')) {
    function cache_core_usuario_ref_con_limpiar()
    {
        Cache::forget('cache_core_usuario_ref_con');
        cache_core_usuario_ref_con();
    }
}