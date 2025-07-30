<?php

namespace App\Cache\Admision;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Emergencia\Models\EmergenciaTipoEvento;
use Modules\Emergencia\Models\TipoOrigenAtencion;

class TipoOrigenAtencionCache
{
    use TraitCache;

    const string TABLE_NAME = 'tipo_origen_atencion';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = TipoOrigenAtencion::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->{$row->getKeyName()},
                    'value' => $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'name' => str_to_upper_utf8($row->Descripcion),
                    'IdTipoServicio' => (int)$row->IdTipoServicio,
                    'Codigo' => $row->Codigo,
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
