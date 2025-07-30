<?php

namespace App\Cache\Configuracion;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Configuracion\Models\EstablecimientoNoMinsa;

class EstablecimientoNoMinsaCache
{
    use TraitCache;

    const string TABLE_NAME = 'establecimiento_no_minsa';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = EstablecimientoNoMinsa::query()
            ->orderBy('Nombre')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int) $row->{$row->getKeyName()},
                    'value' => (int) $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->Nombre),
                    'name' => str_to_upper_utf8($row->Nombre),
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
