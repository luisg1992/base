<?php

namespace App\Cache\Configuracion;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Configuracion\Models\RecetaDosisUnidadMedida;

class RecetaDosisUnidadMedidaCache
{
    use TraitCache;

    const string TABLE_NAME = 'receta_dosis_unidad_medida';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = RecetaDosisUnidadMedida::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->{$row->getKeyName()},
                    'value' => $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->DosisUnidadMedida),
                    'name' => str_to_upper_utf8($row->DosisUnidadMedida),
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
