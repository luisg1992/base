<?php

namespace App\Cache\Admision;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Emergencia\Models\TipoFinanciamiento;

class TipoFinanciamientoCache
{
    use TraitCache;

    const string TABLE_NAME = 'tipo_financimiento';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = TipoFinanciamiento::query()
            ->orderBy('Descripcion')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->{$row->getKeyName()},
                    'value' => $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'name' => str_to_upper_utf8($row->Descripcion),
                    'esFuenteFinanciamiento' => (bool)$row->esFuenteFinanciamiento,
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
