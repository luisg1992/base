<?php

namespace App\Cache\Core;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\Parametro;

class ParametroCache
{
    use TraitCache;

    const string TABLE_NAME = 'parametro';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = Parametro::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int) $row->{$row->getKeyName()},
                    'value' => (int) $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'name' => str_to_upper_utf8($row->Descripcion),
                    'Tipo' => $row->Tipo,
                    'Codigo' => $row->Codigo,
                    'ValorTexto' => $row->ValorTexto,
                    'ValorInt' => $row->ValorInt,
                    'ValorFloat' => $row->ValorFloat,
                    'Grupo' => $row->Grupo,
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
