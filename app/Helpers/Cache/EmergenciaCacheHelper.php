<?php

use Illuminate\Support\Facades\Cache;
use Modules\Configuracion\Models\TipoGravedadAtencion;
use Modules\Configuracion\Models\TriajeEmergenciaEstadoIngreso;
use Modules\Configuracion\Models\TriajeEmergenciaFormaIngreso;
use Modules\Configuracion\Models\TipoMotivoTriajeEmergencia;
use Modules\Emergencia\Models\EmergenciaCausaExternaMorbilidad;
use Modules\Emergencia\Models\EmergenciaClaseAccidente;
use Modules\Emergencia\Models\EmergenciaGrupoOcupacionalALAB;
use Modules\Emergencia\Models\EmergenciaLugarEvento;
use Modules\Emergencia\Models\EmergenciaPosicionLesionadoALAB;
use Modules\Emergencia\Models\EmergenciaRelacionAgresorVictima;
use Modules\Emergencia\Models\EmergenciaSeguridad;
use Modules\Emergencia\Models\EmergenciaTipoAgenteAGAN;
use Modules\Emergencia\Models\EmergenciaTipoEvento;
use Modules\Emergencia\Models\EmergenciaTipoTransporte;
use Modules\Emergencia\Models\EmergenciaTipoVehiculo;
use Modules\Emergencia\Models\EmergenciaUbicacionLesionado;

//TipoGravedadAtencion
if (!function_exists('cache_triaje_emergencia_prioridad')) {
    function cache_triaje_emergencia_prioridad()
    {
        if (Cache::has('cache_triaje_emergencia_prioridad')) {
            return Cache::get('cache_triaje_emergencia_prioridad');
        }

        $records = TipoGravedadAtencion::query()
            ->where('Estado', 1)
            ->orderBy('OrdenPrioridad', 'asc')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdTipoGravedad,
                    'value' => (int)$row->IdTipoGravedad,
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'OrdenPrioridad' => (int)$row->OrdenPrioridad,
                ];
            });
        Cache::put('cache_triaje_emergencia_prioridad', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_triaje_emergencia_prioridad_limpiar')) {
    function cache_triaje_emergencia_prioridad_limpiar()
    {
        Cache::forget('cache_triaje_emergencia_prioridad');
        cache_triaje_emergencia_prioridad();
    }
}


//TipoMotivoTriajeEmergencia
if (!function_exists('cache_triaje_emergencia_motivo')) {
    function cache_triaje_emergencia_motivo()
    {
        if (Cache::has('cache_triaje_emergencia_motivo')) {
            return Cache::get('cache_triaje_emergencia_motivo');
        }

        $records = TipoMotivoTriajeEmergencia::query()
            ->where('IdEstado', 1)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdMotivo,
                    'value' => (int)$row->IdMotivo,
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'IdPrioridad' => (int)$row->IdPrioridad,
                    'IdServicio' => (int)$row->IdServicio,
                ];
            });
        Cache::put('cache_triaje_emergencia_motivo', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_triaje_emergencia_motivo_limpiar')) {
    function cache_triaje_emergencia_motivo_limpiar()
    {
        Cache::forget('cache_triaje_emergencia_motivo');
        cache_triaje_emergencia_motivo();
    }
}



//TriajeEmergenciaFormaIngreso
if (!function_exists('cache_triaje_emergencia_formas_ingreso')) {
    function cache_triaje_emergencia_formas_ingreso()
    {
        if (Cache::has('cache_triaje_emergencia_formas_ingreso')) {
            return Cache::get('cache_triaje_emergencia_formas_ingreso');
        }

        $records = TriajeEmergenciaFormaIngreso::query()
            ->where('Estado', 1)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdFormaIngreso,
                    'value' => (int)$row->IdFormaIngreso,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });
        Cache::put('cache_triaje_emergencia_formas_ingreso', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_triaje_emergencia_formas_ingreso_limpiar')) {
    function cache_triaje_emergencia_formas_ingreso_limpiar()
    {
        Cache::forget('cache_triaje_emergencia_formas_ingreso');
        cache_triaje_emergencia_formas_ingreso();
    }
}


//TriajeEmergenciaEstadoIngreso
if (!function_exists('cache_triaje_emergencia_estado_ingreso')) {
    function cache_triaje_emergencia_estado_ingreso()
    {
        if (Cache::has('cache_triaje_emergencia_estado_ingreso')) {
            return Cache::get('cache_triaje_emergencia_estado_ingreso');
        }

        $records = TriajeEmergenciaEstadoIngreso::query()
            ->where('Estado', 1)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdEstadoIngreso,
                    'value' => (int)$row->IdEstadoIngreso,
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'IdFormaIngreso' => (int)$row->IdFormaIngreso,
                ];
            });
        Cache::put('cache_triaje_emergencia_estado_ingreso', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_triaje_emergencia_estado_ingreso_limpiar')) {
    function cache_triaje_emergencia_estado_ingreso_limpiar()
    {
        Cache::forget('cache_triaje_emergencia_estado_ingreso');
        cache_triaje_emergencia_estado_ingreso();
    }
}

/*
 * EmergenciaCausaExternaMorbilidad
 */
//if (!function_exists('cache_emergencia_causa_externa_morbilidad')) {
//    function cache_emergencia_causa_externa_morbilidad()
//    {
//        if (Cache::has('cache_emergencia_causa_externa_morbilidad')) {
//            return Cache::get('cache_emergencia_causa_externa_morbilidad');
//        }
//
//        $records = EmergenciaCausaExternaMorbilidad::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdCausaExternaMorbilidad,
//                    'value' => (int)$row->IdCausaExternaMorbilidad,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                    'Codigo' => $row->Codigo,
//                    'MotivoSEM' => $row->MotivoSEM,
//                ];
//            });
//        Cache::put('cache_triaje_emergencia_estado_ingreso', $records, now()->addHours(24));
//        return $records;
//    }
//}
//
//if (!function_exists('cache_emergencia_causa_externa_morbilidad_limpiar')) {
//    function cache_emergencia_causa_externa_morbilidad_limpiar()
//    {
//        Cache::forget('cache_emergencia_causa_externa_morbilidad');
//        cache_emergencia_causa_externa_morbilidad();
//    }
//}

/*
 * EmergenciaClaseAccidente
 */
//if (!function_exists('cache_emergencia_clase_accidente')) {
//    function cache_emergencia_clase_accidente()
//    {
//        if (Cache::has('cache_emergencia_clase_accidente')) {
//            return Cache::get('cache_emergencia_clase_accidente');
//        }
//
//        $records = EmergenciaClaseAccidente::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdCausaExternaMorbilidad,
//                    'value' => (int)$row->IdCausaExternaMorbilidad,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                ];
//            });
//        Cache::put('cache_emergencia_clase_accidente', $records, now()->addHours(24));
//        return $records;
//    }
//}
//
//if (!function_exists('cache_emergencia_clase_accidente_limpiar')) {
//    function cache_emergencia_clase_accidente_limpiar()
//    {
//        Cache::forget('cache_emergencia_clase_accidente');
//        cache_emergencia_clase_accidente();
//    }
//}

/*
 * EmergenciaGrupoOcupacionalALAB
 */
//if (!function_exists('cache_emergencia_grupo_ocupacional_alab')) {
//    function cache_emergencia_grupo_ocupacional_alab()
//    {
//        if (Cache::has('cache_emergencia_grupo_ocupacional_alab')) {
//            return Cache::get('cache_emergencia_grupo_ocupacional_alab');
//        }
//
//        $records = EmergenciaGrupoOcupacionalALAB::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdGrupoOcupacionalALAB,
//                    'value' => (int)$row->IdGrupoOcupacionalALAB,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                ];
//            });
//        Cache::put('cache_emergencia_grupo_ocupacional_alab', $records, now()->addHours(24));
//        return $records;
//    }
//}

//if (!function_exists('cache_emergencia_grupo_ocupacional_alab_limpiar')) {
//    function cache_emergencia_grupo_ocupacional_alab_limpiar()
//    {
//        Cache::forget('cache_emergencia_grupo_ocupacional_alab');
//        cache_emergencia_grupo_ocupacional_alab();
//    }
//}

/*
 * EmergenciaLugarEvento
 */
//if (!function_exists('cache_emergencia_lugar_evento')) {
//    function cache_emergencia_lugar_evento()
//    {
//        if (Cache::has('cache_emergencia_lugar_evento')) {
//            return Cache::get('cache_emergencia_lugar_evento');
//        }
//
//        $records = EmergenciaLugarEvento::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdLugarEvento,
//                    'value' => (int)$row->IdLugarEvento,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                ];
//            });
//        Cache::put('cache_emergencia_lugar_evento', $records, now()->addHours(24));
//        return $records;
//    }
//}
//
//if (!function_exists('cache_emergencia_lugar_evento_limpiar')) {
//    function cache_emergencia_lugar_evento_limpiar()
//    {
//        Cache::forget('cache_emergencia_lugar_evento');
//        cache_emergencia_lugar_evento();
//    }
//}

/*
 * EmergenciaPosicionLesionadoALAB
 */
//if (!function_exists('cache_emergencia_posicion_lesionado_alab')) {
//    function cache_emergencia_posicion_lesionado_alab()
//    {
//        if (Cache::has('cache_emergencia_lugar_evento')) {
//            return Cache::get('cache_emergencia_lugar_evento');
//        }
//
//        $records = EmergenciaPosicionLesionadoALAB::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdPosicionLesionadoALAB,
//                    'value' => (int)$row->IdPosicionLesionadoALAB,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                ];
//            });
//        Cache::put('cache_emergencia_posicion_lesionado_alab', $records, now()->addHours(24));
//        return $records;
//    }
//}
//
//if (!function_exists('cache_emergencia_posicion_lesionado_alab_limpiar')) {
//    function cache_emergencia_posicion_lesionado_alab_limpiar()
//    {
//        Cache::forget('cache_emergencia_posicion_lesionado_alab');
//        cache_emergencia_posicion_lesionado_alab();
//    }
//}

/*
 * EmergenciaRelacionAgresorVictima
 */
//if (!function_exists('cache_emergencia_relacion_agresor_victima')) {
//    function cache_emergencia_relacion_agresor_victima()
//    {
//        if (Cache::has('cache_emergencia_relacion_agresor_victima')) {
//            return Cache::get('cache_emergencia_relacion_agresor_victima');
//        }
//
//        $records = EmergenciaRelacionAgresorVictima::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdRelacionAgresorVictima,
//                    'value' => (int)$row->IdRelacionAgresorVictima,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                ];
//            });
//        Cache::put('cache_emergencia_relacion_agresor_victima', $records, now()->addHours(24));
//        return $records;
//    }
//}
//
//if (!function_exists('cache_emergencia_relacion_agresor_victima_limpiar')) {
//    function cache_emergencia_relacion_agresor_victima_limpiar()
//    {
//        Cache::forget('cache_emergencia_relacion_agresor_victima');
//        cache_emergencia_relacion_agresor_victima();
//    }
//}

/*
 * EmergenciaSeguridad
 */
//if (!function_exists('cache_emergencia_seguridad')) {
//    function cache_emergencia_seguridad()
//    {
//        if (Cache::has('cache_emergencia_seguridad')) {
//            return Cache::get('cache_emergencia_seguridad');
//        }
//
//        $records = EmergenciaSeguridad::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdSeguridad,
//                    'value' => (int)$row->IdSeguridad,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                ];
//            });
//        Cache::put('cache_emergencia_seguridad', $records, now()->addHours(24));
//        return $records;
//    }
//}
//
//if (!function_exists('cache_emergencia_seguridad_limpiar')) {
//    function cache_emergencia_seguridad_limpiar()
//    {
//        Cache::forget('cache_emergencia_seguridad');
//        cache_emergencia_seguridad();
//    }
//}

/*
 * EmergenciaTipoAgenteAGAN
 */
//if (!function_exists('cache_emergencia_tipo_agente_agan')) {
//    function cache_emergencia_tipo_agente_agan()
//    {
//        if (Cache::has('cache_emergencia_tipo_agente_agan')) {
//            return Cache::get('cache_emergencia_tipo_agente_agan');
//        }
//
//        $records = EmergenciaTipoAgenteAGAN::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdTipoAgenteAGAN,
//                    'value' => (int)$row->IdTipoAgenteAGAN,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                ];
//            });
//        Cache::put('cache_emergencia_tipo_agente_agan', $records, now()->addHours(24));
//        return $records;
//    }
//}
//
//if (!function_exists('cache_emergencia_tipo_agente_agan_limpiar')) {
//    function cache_emergencia_seguridad_limpiar()
//    {
//        Cache::forget('cache_emergencia_tipo_agente_agan');
//        cache_emergencia_tipo_agente_agan();
//    }
//}

/*
 * EmergenciaTipoEvento
 */
//if (!function_exists('cache_emergencia_tipo_evento')) {
//    function cache_emergencia_tipo_evento()
//    {
//        if (Cache::has('cache_emergencia_tipo_evento')) {
//            return Cache::get('cache_emergencia_tipo_evento');
//        }
//
//        $records = EmergenciaTipoEvento::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdTipoEvento,
//                    'value' => (int)$row->IdTipoEvento,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                ];
//            });
//        Cache::put('cache_emergencia_tipo_evento', $records, now()->addHours(24));
//        return $records;
//    }
//}
//
//if (!function_exists('cache_emergencia_tipo_evento_limpiar')) {
//    function cache_emergencia_tipo_evento_limpiar()
//    {
//        Cache::forget('cache_emergencia_tipo_evento');
//        cache_emergencia_tipo_evento();
//    }
//}

/*
 * EmergenciaTipoTransporte
 */
//if (!function_exists('cache_emergencia_tipo_transporte')) {
//    function cache_emergencia_tipo_transporte()
//    {
//        if (Cache::has('cache_emergencia_tipo_transporte')) {
//            return Cache::get('cache_emergencia_tipo_transporte');
//        }
//
//        $records = EmergenciaTipoTransporte::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdTipoTransporte,
//                    'value' => (int)$row->IdTipoTransporte,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                ];
//            });
//        Cache::put('cache_emergencia_tipo_transporte', $records, now()->addHours(24));
//        return $records;
//    }
//}

//if (!function_exists('cache_emergencia_tipo_transporte_limpiar')) {
//    function cache_emergencia_tipo_evento_limpiar()
//    {
//        Cache::forget('cache_emergencia_tipo_transporte');
//        cache_emergencia_tipo_transporte();
//    }
//}

/*
 * EmergenciaTipoVehiculo
 */
//if (!function_exists('cache_emergencia_tipo_vehiculo')) {
//    function cache_emergencia_tipo_vehiculo()
//    {
//        if (Cache::has('cache_emergencia_tipo_vehiculo')) {
//            return Cache::get('cache_emergencia_tipo_vehiculo');
//        }
//
//        $records = EmergenciaTipoVehiculo::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdTipoVehiculo,
//                    'value' => (int)$row->IdTipoVehiculo,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                ];
//            });
//        Cache::put('cache_emergencia_tipo_vehiculo', $records, now()->addHours(24));
//        return $records;
//    }
//}
//
//if (!function_exists('cache_emergencia_tipo_vehiculo_limpiar')) {
//    function cache_emergencia_tipo_vehiculo_limpiar()
//    {
//        Cache::forget('cache_emergencia_tipo_vehiculo');
//        cache_emergencia_tipo_vehiculo();
//    }
//}

/*
 * EmergenciaUbicacionLesionado
 */
//if (!function_exists('cache_emergencia_ubicacion_lesionado')) {
//    function cache_emergencia_ubicacion_lesionado()
//    {
//        if (Cache::has('cache_emergencia_ubicacion_lesionado')) {
//            return Cache::get('cache_emergencia_ubicacion_lesionado');
//        }
//
//        $records = EmergenciaUbicacionLesionado::query()
//            ->get()
//            ->transform(function ($row) {
//                return [
//                    'id' => (int)$row->IdUbicacionLesionado,
//                    'value' => (int)$row->IdUbicacionLesionado,
//                    'label' => str_to_upper_utf8($row->Descripcion),
//                ];
//            });
//        Cache::put('cache_emergencia_ubicacion_lesionado', $records, now()->addHours(24));
//        return $records;
//    }
//}
//
//if (!function_exists('cache_emergencia_ubicacion_lesionado_limpiar')) {
//    function cache_emergencia_ubicacion_lesionado_limpiar()
//    {
//        Cache::forget('cache_emergencia_ubicacion_lesionado');
//        cache_emergencia_ubicacion_lesionado();
//    }
//}


