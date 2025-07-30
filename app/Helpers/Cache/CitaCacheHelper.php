<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Configuracion\Models\CitaAnuladaMotivo;
use Modules\Configuracion\Models\CitasDemandaInsatisfechaMotivo;

if (!function_exists('cache_configuracion_cita_demanda_insatisfecha_motivo')) {
    function cache_configuracion_cita_demanda_insatisfecha_motivo()
    {
        if (Cache::has('cache_configuracion_cita_demanda_insatisfecha_motivo')) {
            return Cache::get('cache_configuracion_cita_demanda_insatisfecha_motivo');
        }

        $records = CitasDemandaInsatisfechaMotivo::query()
            ->where('Estado', 1)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdDemandaInsatisfechaMotivo,
                    'value' => (int)$row->IdDemandaInsatisfechaMotivo,
                    'label' => str_to_upper_utf8($row->Descripcion)
                ];
            });
        Cache::put('cache_configuracion_cita_demanda_insatisfecha_motivo', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_configuracion_cita_demanda_insatisfecha_motivo_limpiar')) {
    function cache_configuracion_cita_demanda_insatisfecha_motivo_limpiar()
    {
        Cache::forget('cache_configuracion_cita_demanda_insatisfecha_motivo');
        cache_configuracion_cita_demanda_insatisfecha_motivo();
    }
}
 
if (!function_exists('cache_configuracion_cita_anulada_motivo')) {
    function cache_configuracion_cita_anulada_motivo()
    {
        if (Cache::has('cache_configuracion_cita_anulada_motivo')) {
            return Cache::get('cache_configuracion_cita_anulada_motivo');
        }

        $records = CitaAnuladaMotivo::query()
            ->where('IdEstado', 1)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdMotivo,
                    'value' => (int)$row->IdMotivo,
                    'label' => str_to_upper_utf8($row->Descripcion)
                ];
            });
        Cache::put('cache_configuracion_cita_anulada_motivo', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_configuracion_cita_anulada_motivo_limpiar')) {
    function cache_configuracion_cita_anulada_motivo_limpiar()
    {
        Cache::forget('cache_configuracion_cita_anulada_motivo');
        cache_configuracion_cita_anulada_motivo();
    }
}

if (!function_exists('cache_atencion_proxima_especialidades')) {
    function cache_atencion_proxima_especialidades()
    {
        if (Cache::has('cache_atencion_proxima_especialidades')) {
            return Cache::get('cache_atencion_proxima_especialidades');
        }

        $records = DB::table('AtencionesProximaCita as a')
            ->join('Especialidades as e', 'e.IdEspecialidad', '=', 'a.IdEspecialidad')
            ->where('a.IdEstadoVincula', 1)
            ->selectRaw('DISTINCT e.IdEspecialidad, e.Nombre, UPPER(e.Nombre) as Especialidad')
            ->orderBy('e.Nombre')
            ->get()
            ->unique('IdEspecialidad') // evita duplicados si los hay
            ->transform(function ($row) {
                return [
                    'id'    => $row->IdEspecialidad,
                    'value' => $row->IdEspecialidad,
                    'label' => $row->Especialidad,
                ];
            });

        Cache::put('cache_atencion_proxima_especialidades', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_atencion_interconsulta_especialidades')) {
    function cache_atencion_interconsulta_especialidades()
    {
        if (Cache::has('cache_atencion_interconsulta_especialidades')) {
            return Cache::get('cache_atencion_interconsulta_especialidades');
        }

        $records = DB::table('AtencionesSolicitudEspecialidades as a')
            ->join('Especialidades as e', 'e.IdEspecialidad', '=', 'a.IdEspecialidad')
            ->where('a.IdEstado', 1)
            ->selectRaw('DISTINCT e.IdEspecialidad, e.Nombre, UPPER(e.Nombre) as Especialidad')
            ->orderBy('e.Nombre')
            ->get()
            ->unique('IdEspecialidad')
            ->transform(function ($row) {
                return [
                    'id'    => $row->IdEspecialidad,
                    'value' => $row->IdEspecialidad,
                    'label' => $row->Especialidad,
                ];
            });

        Cache::put('cache_atencion_interconsulta_especialidades', $records, now()->addHours(24));
        return $records;
    }
}
