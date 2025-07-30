<?php

namespace App\Cache\Emergencia;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Emergencia\Models\EmergenciaTipoVehiculo;

class EmergenciaTipoVehiculoCache
{
    use TraitCache;
    const string TABLE_NAME = 'emergencia_tipo_vehiculo';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = EmergenciaTipoVehiculo::query()
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
