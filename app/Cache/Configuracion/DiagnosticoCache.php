<?php

namespace App\Cache\Configuracion;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Configuracion\Models\Diagnostico;
use Modules\Configuracion\Models\TipoReferencia;

class DiagnosticoCache
{
    use TraitCache;

    const string TABLE_NAME = 'diagnostico';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = Diagnostico::query()
            ->where('EsActivo', 1)
            ->whereNotNull('CodigoCIE10')
            ->orderBy('CodigoCIE10')
            ->orderBy('Descripcion')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int) $row->{$row->getKeyName()},
                    'value' => (int) $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->CodigoCIE10 . ' ' . $row->Descripcion),
                    'name' => str_to_upper_utf8($row->CodigoCIE10 . ' ' . $row->Descripcion),
                    'CodigoCIE10' => str_to_upper_utf8($row->CodigoCIE10),
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
