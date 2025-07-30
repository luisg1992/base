<?php

namespace App\Cache\Emergencia;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Emergencia\Models\EmergenciaClaseAccidente;

class EmergenciaClaseAccidenteCache
{
    use TraitCache;
    const string TABLE_NAME = 'emergencia_clase_accidente';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = EmergenciaClaseAccidente::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->{$row->getKeyName()},
                    'value' => $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'name' => str_to_upper_utf8($row->Descripcion),
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
