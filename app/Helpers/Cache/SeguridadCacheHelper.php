<?php

use Illuminate\Support\Facades\Cache;
use Modules\Seguridad\Models\Role;

/*Role Galenos*/

if (!function_exists('cache_seguridad_roles')) {
    function cache_seguridad_roles()
    {
        if (Cache::has('cache_seguridad_roles')) {
            return Cache::get('cache_seguridad_roles');
        }

        $records = Role::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdRol,
                    'value' => $row->IdRol,
                    'label' => str_to_upper_utf8($row->Nombre),
                ];
            });
        Cache::put('cache_seguridad_roles', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_seguridad_roles_limpiar')) {
    function cache_seguridad_roles_limpiar()
    {
        Cache::forget('cache_seguridad_roles');
        cache_seguridad_roles();
    }
}


if (!function_exists('cache_seguridad_role')) {
    function cache_seguridad_role()
    {
        if (Cache::has('cache_seguridad_role')) {
            return Cache::get('cache_seguridad_role');
        }

        $records = \Spatie\Permission\Models\Role::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'value' => $row->id,
                    'label' => str_to_upper_utf8($row->name),
                ];
            });
        Cache::put('cache_seguridad_role', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_seguridad_role_limpiar')) {
    function cache_seguridad_role_limpiar()
    {
        Cache::forget('cache_seguridad_role');
        cache_seguridad_role();
    }
}
