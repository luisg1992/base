<?php

namespace App\Cache\Configuracion;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Configuracion\Models\RecetaClasificacionViaAdmin;

class RecetaClasificacionViaAdminCache
{
    use TraitCache;

    const string TABLE_NAME = 'receta_clasificacion_via_admin';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = RecetaClasificacionViaAdmin::query()
           // ->where('IdEstado', 1)
            ->orderBy('Categoria', 'asc')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->{$row->getKeyName()},
                    'value' => $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->Categoria),
                    'name' => str_to_upper_utf8($row->Categoria),
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
