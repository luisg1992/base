<?php

namespace App\Cache\Configuracion;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Configuracion\Models\TipoEdadCategoria;
use Modules\Configuracion\Models\TipoReferencia;

class TipoEdadCategoriaCache
{
    use TraitCache;

    const string TABLE_NAME = 'tipo_edad_categoria';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = TipoEdadCategoria::query()
            ->orderBy('EdadMinima')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int) $row->{$row->getKeyName()},
                    'value' => (int) $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->GrupoEdad),
                    'name' => str_to_upper_utf8($row->GrupoEdad),
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
