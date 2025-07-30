<?php

namespace App\Cache\Configuracion;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Configuracion\Models\FuenteFinanciamiento;
use Modules\Configuracion\Models\FuenteFinanciamientoTarifas;
use Modules\Configuracion\Models\TipoFinanciamiento;

class FuenteFinanciamientoCache
{
    use TraitCache;

    const string TABLE_NAME = 'fuente_financiamiento';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = FuenteFinanciamiento::query()
            ->orderBy('Descripcion')
            ->get()
            ->transform(function ($row) {
                $ffs = FuenteFinanciamientoTarifas::query()
                    ->where('idFuenteFinanciamiento', $row->{$row->getKeyName()})
                    ->pluck('idTipoFinanciamiento');

                $tfs = TipoFinanciamiento::query()
                    ->whereIn('IdTipoFinanciamiento', $ffs)
                    ->where('esFuenteFinanciamiento', 1)
                    ->get()
                    ->transform(function ($tf) {
                        return [
                            'id' => (int) $tf->{$tf->getKeyName()},
                            'value' => (int) $tf->{$tf->getKeyName()},
                            'label' => str_to_upper_utf8($tf->Descripcion),
                            'name' => str_to_upper_utf8($tf->Descripcion),
                        ];
                    });

                return [
                    'id' => (int) $row->{$row->getKeyName()},
                    'value' => (int) $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'name' => str_to_upper_utf8($row->Descripcion),
                    'UtilizadoEn' => (int)$row->UtilizadoEn,
                    'IdTipoFinanciamiento' => (int)$row->IdTipoFinanciamiento,
                    'tipoFinanciamientos' => $tfs->toArray(),
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
