<?php

use Illuminate\Support\Facades\Cache;
use Modules\ProgramacionGeneral\Models\TipoTurno;


/*TipoTurno*/
if (!function_exists('cache_programacion_tipo_turnos')) {
    function cache_programacion_tipo_turnos()
    {
        if (Cache::has('cache_programacion_tipo_turnos')) {
            return Cache::get('cache_programacion_tipo_turnos');
        }

        $records = TipoTurno::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdTurno,
                    'value' => $row->IdTurno,
                    'label' => str_to_upper_utf8($row->Turno),
                ];
            });
        Cache::put('cache_programacion_tipo_turnos', $records, now()->addHours(24));
        return $records;
    }
}
