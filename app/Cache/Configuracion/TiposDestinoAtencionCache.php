<?php

namespace App\Cache\Configuracion;

use App\Cache\TraitCache;
use Illuminate\Support\Facades\Cache;
use Modules\Configuracion\Models\TipoDestinoAtencion;

class TiposDestinoAtencionCache
{
    use TraitCache;

    const string TABLE_NAME = 'tipos_destino_atencion';

    static function getCache()
    {
        $cache_name = self::getNameCache();
        if (Cache::has($cache_name)) {
            return Cache::get($cache_name);
        }

        $records = TipoDestinoAtencion::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->{$row->getKeyName()},
                    'value' => $row->{$row->getKeyName()},
                    'label' => str_to_upper_utf8($row->Codigo).'='.str_to_upper_utf8($row->Descripcion),
                    'name' => str_to_upper_utf8($row->Codigo).'='.str_to_upper_utf8($row->Descripcion),
                    'codigo' => str_to_upper_utf8($row->Codigo),
                    'idTipoServicio' => str_to_upper_utf8($row->IdTipoServicio),
                    'tipoServicioHosp' => str_to_upper_utf8($row->TipoServicioHosp),
                    'destinoSEM' => str_to_upper_utf8($row->DestinoSEM),
                    'id_destinoAseguradoSIS' => str_to_upper_utf8($row->id_destinoAseguradoSIS),
                ];
            })
            ->toArray();

        Cache::put($cache_name, $records, 7200);

        return $records;
    }
}
