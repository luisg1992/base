<?php

namespace App\Cache\Configuracion;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Configuracion\Models\RefconCondiContraRef;

class RefconCondiContraRefCache
{
    use TraitCache;

    const string TABLE_NAME = 'Refcon_Condi_ContraRef';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = RefconCondiContraRef::query()
            ->orderBy('Descripcion')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int) $row->{$row->getKeyName()},
                    'value' => (int) $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'name' => str_to_upper_utf8($row->Descripcion),
                    'valor' => $row->Valor,
                    'tipo' => (int) $row->Tipo,
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
