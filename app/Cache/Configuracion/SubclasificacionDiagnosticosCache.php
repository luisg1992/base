<?php

namespace App\Cache\Configuracion;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Configuracion\Models\SubclasificacionDiagnosticos;

class SubclasificacionDiagnosticosCache
{
    use TraitCache;

    const string TABLE_NAME = 'subclasificacion_diagnosticos';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = SubclasificacionDiagnosticos::query()
            ->where('IdTipoServicio', 1)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->{$row->getKeyName()},
                    'value' => $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'name' => str_to_upper_utf8($row->Descripcion),
                    'Codigo' => str_to_upper_utf8($row->Codigo),
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
