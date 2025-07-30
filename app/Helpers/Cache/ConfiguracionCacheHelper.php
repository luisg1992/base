<?php

use Illuminate\Support\Facades\Cache;

use Modules\Configuracion\Models\CentroPoblado;
use Modules\Configuracion\Models\Departamento;
use Modules\Configuracion\Models\AreaTramitaSeguros;
use Modules\Configuracion\Models\CajaTipoComprobante;
use Modules\Configuracion\Models\DepartamentoHospital;
use Modules\Configuracion\Models\DiagnosticoCapitulo;
use Modules\Configuracion\Models\DiagnosticoCategorias;
use Modules\Configuracion\Models\DiagnosticoGrupo;
use Modules\Configuracion\Models\Distrito;
use Modules\Configuracion\Models\Especialidad;
use Modules\Configuracion\Models\EspecialidadPrimaria;
use Modules\Configuracion\Models\Establecimiento;
use Modules\Configuracion\Models\FarmTipoConcepto;
use Modules\Configuracion\Models\HisColegio;
use Modules\Configuracion\Models\Provincia;
use Modules\Configuracion\Models\Servicio;
use Modules\Configuracion\Models\SuSaludServicio;
use Modules\Configuracion\Models\TipoConsultorio;
use Modules\Configuracion\Models\TipoEdad;
use Modules\Configuracion\Models\TipoFinanciador;
use Modules\Configuracion\Models\TipoFinanciamiento;
use Modules\Configuracion\Models\TipoServicio;
use Modules\Configuracion\Models\UbicacionFisica;
use Modules\Configuracion\Models\UpsRenaes;
use Modules\Configuracion\Models\UPSServicio;
use Modules\Configuracion\Models\FuenteFinanciamiento;
use Modules\Configuracion\Models\ProgramaInstitucion;
use Modules\Configuracion\Models\ProgramaTipoDocumento;
use Modules\Configuracion\Models\FactPuntoCarga;
use Modules\Configuracion\Models\RecetaClasificacionViaAdmin;
use Modules\Configuracion\Models\RecetaDosis;
use Modules\Configuracion\Models\TiposModalidadAsistencia;
use Modules\Configuracion\Models\TiposSolicitudInterconsulta;

if (! function_exists('cache_configuracion_especialidades_servicios')) {
    function cache_configuracion_especialidades_servicios()
    {
        $cacheKey = 'cache_configuracion_especialidades_servicios';
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $records = Especialidad::query()
            ->selectRaw('DISTINCT
                Especialidades.IdEspecialidad,
                Especialidades.Nombre,
                Especialidades.IdEspecialidadPrimaria,
                Especialidades.IdDepartamento,
                Servicios.IdTipoServicio')
            ->join('Servicios', 'Servicios.IdEspecialidad', '=', 'Especialidades.IdEspecialidad')
            ->where('Servicios.IdEstado', 1)
            ->orderBy('Especialidades.Nombre')
            ->get()
            ->transform(function ($row) {
                return [
                    'id'                    => (int) $row->IdEspecialidad,
                    'value'                 => (int) $row->IdEspecialidad,
                    'label'                 => str_to_upper_utf8($row->Nombre),
                    'IdEspecialidadPrimaria' => (int) $row->IdEspecialidadPrimaria,
                    'IdDepartamento'        => (int) $row->IdDepartamento,
                    'IdTipoServicio'        => (int) $row->IdTipoServicio,
                ];
            });

        Cache::put($cacheKey, $records, now()->addHours(24));
        return $records;
    }
}


/*Ubigeo*/
if (!function_exists('cache_configuracion_ubigeos')) {
    function cache_configuracion_ubigeos()
    {
        if (Cache::has('cache_configuracion_ubigeos')) {
            return Cache::get('cache_configuracion_ubigeos');
        }

        $ubigeos = [];
        $departamentos = Departamento::query()->get();
        foreach ($departamentos as $departamento) {
            $provincias = Provincia::query()
                ->where('IdDepartamento', $departamento->IdDepartamento)
                ->get();
            foreach ($provincias as $provincia) {
                $distritos = Distrito::query()
                    ->where('IdProvincia', $provincia->IdProvincia)
                    ->get();
                foreach ($distritos as $distrito) {
                    $ubigeos[] = [
                        'id' => $distrito->IdDistrito,
                        'value' => $distrito->IdDistrito,
                        'label' => str_to_upper_utf8($departamento->Nombre . ' - ' . $provincia->Nombre . ' - ' . $distrito->Nombre),
                        'IdProvincia' => $distrito->IdProvincia,
                        'IdDepartamento' => $provincia->IdDepartamento,
                        'IdReniec' => $distrito->IdReniec,
                    ];
                }
            }
        }

        Cache::put('cache_configuracion_ubigeos', $ubigeos, now()->addHours(24));

        return $ubigeos;
    }
}

//Prioridad
if (!function_exists('cache_configuracion_fuente_financiamiento')) {
    function cache_configuracion_fuente_financiamiento()
    {
        if (Cache::has('cache_configuracion_fuente_financiamiento')) {
            return Cache::get('cache_configuracion_fuente_financiamiento');
        }

        $records = FuenteFinanciamiento::query()
            ->where('idEstado', 1)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdFuenteFinanciamiento,
                    'value' => (int)$row->IdFuenteFinanciamiento,
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'UtilizadoEn' => (int)$row->UtilizadoEn,
                    'IdTipoFinanciamiento' => (int)$row->IdTipoFinanciamiento,
                ];
            });
        Cache::put('cache_configuracion_fuente_financiamiento', $records, now()->addHours(24));
        return $records;
    }
}


//SERVICIOS
if (!function_exists('cache_configuracion_servicio')) {
    function cache_configuracion_servicio()
    {
        if (Cache::has('cache_configuracion_servicio')) {
            return Cache::get('cache_configuracion_servicio');
        }

        $records = Servicio::query()
            ->with('especialidad')
            ->orderBy('Nombre')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdServicio,
                    'value' => (int)$row->IdServicio,
                    'label' => str_to_upper_utf8($row->Nombre),
                    'idEstado' => (int)$row->idEstado,
                    'IdTipoServicio' => (int)$row->IdTipoServicio,
                    'IdEspecialidad' => (int)$row->IdEspecialidad,
                    'Especialidad' => $row->Codigo . ' - (' . str_to_upper_utf8(optional($row->especialidad)->Nombre) . ')',
                    'Detalle' => $row->Codigo . ' - (' . str_to_upper_utf8($row->Nombre) . ' - ' . str_to_upper_utf8(optional($row->especialidad)->Nombre) . ')',
                ];
            });
        Cache::put('cache_configuracion_servicio', $records, now()->addHours(24));
        return $records;
    }
}


//TIPO FINANCIAMIENTO

if (!function_exists('cache_configuracion_tipo_financiamiento')) {
    function cache_configuracion_tipo_financiamiento()
    {
        if (Cache::has('cache_configuracion_tipo_financiamiento')) {
            return Cache::get('cache_configuracion_tipo_financiamiento');
        }

        $records = TipoFinanciamiento::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdTipoFinanciamiento,
                    'value' => (int)$row->IdTipoFinanciamiento,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });
        Cache::put('cache_configuracion_tipo_financiamiento', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_configuracion_tipo_financiamiento_limpiar')) {
    function cache_configuracion_tipo_financiamiento_limpiar()
    {
        Cache::forget('cache_configuracion_tipo_financiamiento');
        cache_configuracion_tipo_financiamiento();
    }
}

//TIPO FINANCIADOR
if (!function_exists('cache_configuracion_tipo_financiador')) {
    function cache_configuracion_tipo_financiador()
    {
        if (Cache::has('cache_configuracion_tipo_financiador')) {
            return Cache::get('cache_configuracion_tipo_financiador');
        }

        $records = TipoFinanciador::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->idTipoFinanciador,
                    'value' => (int)$row->idTipoFinanciador,
                    'label' => str_to_upper_utf8($row->nombre),
                ];
            });
        Cache::put('cache_configuracion_tipo_financiador', $records, now()->addHours(24));
        return $records;
    }
}

//TIPO FARM TIPO CONCEPTOS
if (!function_exists('cache_configuracion_farm_tipo_conceptos')) {
    function cache_configuracion_farm_tipo_conceptos()
    {
        if (Cache::has('cache_configuracion_farm_tipo_conceptos')) {
            return Cache::get('cache_configuracion_farm_tipo_conceptos');
        }

        $records = FarmTipoConcepto::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->idTipoConcepto,
                    'value' => (int)$row->idTipoConcepto,
                    'label' => str_to_upper_utf8($row->Concepto),
                ];
            });

        Cache::put('cache_configuracion_farm_tipo_conceptos', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_configuracion_tipo_consultorios')) {
    function cache_configuracion_tipo_consultorios()
    {
        if (Cache::has('cache_configuracion_tipo_consultorios')) {
            return Cache::get('cache_configuracion_tipo_consultorios');
        }

        $records = TipoConsultorio::query()
            ->where('estado', 1)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdTipoConsultorio,
                    'value' => (int)$row->IdTipoConsultorio,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });

        Cache::put('cache_configuracion_tipo_consultorios', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_configuracion_tipo_consultorios_limpiar')) {
    function cache_configuracion_tipo_consultorios_limpiar()
    {
        Cache::forget('cache_configuracion_tipo_consultorios');
        cache_configuracion_tipo_consultorios();
    }
}


//TIPO AREA TRAMITE SEGUROS
if (!function_exists('cache_configuracion_area_tramita_seguros')) {
    function cache_configuracion_area_tramita_seguros()
    {
        if (Cache::has('cache_configuracion_area_tramita_seguros')) {
            return Cache::get('cache_configuracion_area_tramita_seguros');
        }

        $records = AreaTramitaSeguros::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->idAreaTramitaSeguros,
                    'value' => (int)$row->idAreaTramitaSeguros,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });
        Cache::put('cache_configuracion_area_tramita_seguros', $records, now()->addHours(24));
        return $records;
    }
}

//CAJA TIPO COMPROBANTE
if (!function_exists('cache_configuracion_caja_tipo_comprobante')) {
    function cache_configuracion_caja_tipo_comprobante()
    {
        if (Cache::has('cache_configuracion_caja_tipo_comprobante')) {
            return Cache::get('cache_configuracion_caja_tipo_comprobante');
        }

        $records = CajaTipoComprobante::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdTipoComprobante,
                    'value' => (int)$row->IdTipoComprobante,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });
        Cache::put('cache_configuracion_caja_tipo_comprobante', $records, now()->addHours(24));
        return $records;
    }
}

//DIAGNOSTICOS CAPITULO
if (!function_exists('cache_configuracion_capitulos')) {
    function cache_configuracion_capitulos()
    {
        if (Cache::has('cache_configuracion_capitulos')) {
            return Cache::get('cache_configuracion_capitulos');
        }

        $records = DiagnosticoCapitulo::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdCapitulo,
                    'value' => (int)$row->IdCapitulo,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });
        Cache::put('cache_configuracion_capitulos', $records, now()->addHours(24));
        return $records;
    }
}

//DIAGNOSTICOS GRUPO
if (!function_exists('cache_configuracion_centros_poblados')) {
    function cache_configuracion_centros_poblados()
    {
        if (Cache::has('cache_configuracion_centros_poblados')) {
            return Cache::get('cache_configuracion_centros_poblados');
        }

        $records = CentroPoblado::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdCentroPoblado,
                    'value' => (int)$row->IdCentroPoblado,
                    'label' => str_to_upper_utf8($row->Nombre),
                    'IdDistrito' => (int)$row->IdDistrito,
                ];
            });
        Cache::put('cache_configuracion_centros_poblados', $records, now()->addHours(24));
        return $records;
    }
}


//DIAGNOSTICOS GRUPO
if (!function_exists('cache_configuracion_grupo')) {
    function cache_configuracion_grupo()
    {
        if (Cache::has('cache_configuracion_grupo')) {
            return Cache::get('cache_configuracion_grupo');
        }

        $records = DiagnosticoGrupo::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdGrupo,
                    'value' => (int)$row->IdGrupo,
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'IdCapitulo' => (int)$row->IdCapitulo,
                    'parent_id' => (int)$row->IdCapitulo,
                ];
            });
        Cache::put('cache_configuracion_grupo', $records, now()->addHours(24));
        return $records;
    }
}

//DIAGNOSTICOS CATEGORIA
if (!function_exists('cache_configuracion_categoria')) {
    function cache_configuracion_categoria()
    {
        if (Cache::has('cache_configuracion_categoria')) {
            return Cache::get('cache_configuracion_categoria');
        }

        $records = DiagnosticoCategorias::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdCategoria,
                    'value' => (int)$row->IdCategoria,
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'IdGrupo' => (int)$row->IdGrupo,
                    'parent_id' => (int)$row->IdGrupo,
                ];
            });
        Cache::put('cache_configuracion_categoria', $records, now()->addHours(24));
        return $records;
    }
}

/*Establecimiento*/
if (!function_exists('cache_configuracion_establecimientos')) {
    function cache_configuracion_establecimientos()
    {
        if (Cache::has('cache_configuracion_establecimientos')) {
            return Cache::get('cache_configuracion_establecimientos');
        }

        $records = Establecimiento::query()
            ->with('distrito.provincia', 'distrito.provincia.departamento')
            ->get()
            ->transform(function ($row) {
                $label = $row->Nombre . ', ' . optional(optional(optional($row->distrito)->provincia)->departamento)->Nombre . ' - ' .
                    optional(optional($row->distrito)->provincia)->Nombre . ' - ' .
                    optional($row->distrito)->Nombre;
                return [
                    'id' => $row->IdEstablecimiento,
                    'value' => $row->IdEstablecimiento,
                    'label' => str_to_upper_utf8($label),
                ];
            });
        Cache::put('cache_configuracion_establecimientos', $records, now()->addHours(24));
        return $records;
    }
}

/*TipoServicio*/
if (!function_exists('cache_configuracion_tipo_servicios')) {
    function cache_configuracion_tipo_servicios()
    {
        if (Cache::has('cache_configuracion_tipo_servicios')) {
            return Cache::get('cache_configuracion_tipo_servicios');
        }

        $records = TipoServicio::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdTipoServicio,
                    'value' => $row->IdTipoServicio,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });
        Cache::put('cache_configuracion_tipo_servicios', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_configuracion_tipo_servicios_limpiar')) {
    function cache_configuracion_tipo_servicios_limpiar()
    {
        Cache::forget('cache_configuracion_tipo_servicios');
        cache_configuracion_tipo_servicios();
    }
}

/*Especialidad*/
if (!function_exists('cache_configuracion_especialidades')) {
    function cache_configuracion_especialidades()
    {
        if (Cache::has('cache_configuracion_especialidades')) {
            return Cache::get('cache_configuracion_especialidades');
        }

        $records = Especialidad::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdEspecialidad,
                    'value' => (int)$row->IdEspecialidad,
                    'label' => str_to_upper_utf8($row->Nombre),
                    'IdEspecialidadPrimaria' => (int)$row->IdEspecialidadPrimaria,
                    'IdDepartamento' => (int)$row->IdDepartamento,
                ];
            });
        Cache::put('cache_configuracion_especialidades', $records, now()->addHours(24));

        return $records;
    }
}

if (!function_exists('cache_configuracion_especialidades_limpiar')) {
    function cache_configuracion_especialidades_limpiar()
    {
        Cache::forget('cache_configuracion_especialidades');
        cache_configuracion_especialidades();
    }
}

if (!function_exists('cache_configuracion_tipo_terapias')) {
    function cache_configuracion_tipo_terapias()
    {
        if (Cache::has('cache_configuracion_tipo_terapias')) {
            return Cache::get('cache_configuracion_tipo_terapias');
        }

        $records = [
            ['value' => 0, 'label' => 'Ninguno'],
            ['value' => 1, 'label' => 'Terapia Individual'],
            ['value' => 2, 'label' => 'Terapia Grupal'],
        ];
        Cache::put('cache_configuracion_tipo_terapias', $records, now()->addHours(24));

        return $records;
    }
}

if (!function_exists('cache_configuracion_tipo_sexos')) {
    function cache_configuracion_tipo_sexos()
    {
        if (Cache::has('cache_configuracion_tipo_sexos')) {
            return Cache::get('cache_configuracion_tipo_sexos');
        }

        $records = [
            ['value' => 1, 'label' => 'Sólo masculino'],
            ['value' => 2, 'label' => 'Sólo femenino'],
            ['value' => 3, 'label' => 'Ambos sexos'],
        ];
        Cache::put('cache_configuracion_tipo_sexos', $records, now()->addHours(24));

        return $records;
    }
}

if (!function_exists('cache_configuracion_tipo_lugares_laborales')) {
    function cache_configuracion_tipo_lugares_laborales()
    {
        if (Cache::has('cache_configuracion_tipo_lugares_laborales')) {
            return Cache::get('cache_configuracion_tipo_lugares_laborales');
        }

        $records = [
            ['value' => 0, 'label' => 'En otro lugar'],
            ['value' => 1, 'label' => 'Almacén o farmacia'],
            ['value' => 2, 'label' => 'Imageneología'],
            ['value' => 3, 'label' => 'Laboratorio'],
            ['value' => 4, 'label' => 'Seguros'],
            ['value' => 5, 'label' => 'Especialidades de consulta externa'],
            ['value' => 6, 'label' => 'Especialidades de hospitalización'],
            ['value' => 7, 'label' => 'Especialidades de emergencia - observaciones'],
            ['value' => 8, 'label' => 'Especialidades de emergencia - consulta'],
            ['value' => 9, 'label' => 'Area tramita seguros'],
        ];
        Cache::put('cache_configuracion_tipo_lugares_laborales', $records, now()->addHours(24));

        return $records;
    }
}

/*TipoEdad*/
if (!function_exists('cache_configuracion_tipo_edades')) {
    function cache_configuracion_tipo_edades()
    {
        if (Cache::has('cache_configuracion_tipo_edades')) {
            return Cache::get('cache_configuracion_tipo_edades');
        }

        $records = TipoEdad::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => $row->IdTipoEdad,
                    'label' => $row->Codigo . ' - ' . str_to_upper_utf8($row->Descripcion),
                ];
            });
        Cache::put('cache_configuracion_tipo_edades', $records, now()->addHours(24));
        return $records;
    }
}

/*UPSServicio*/
if (!function_exists('cache_configuracion_ups_servicios')) {
    function cache_configuracion_ups_servicios()
    {
        if (Cache::has('cache_configuracion_ups_servicios')) {
            return Cache::get('cache_configuracion_ups_servicios');
        }

        $records = UPSServicio::query()
            ->orderBy('descripcion')
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => (int)$row->UPShis,
                    'label' => $row->UPShis . ' - ' . str_to_upper_utf8($row->descripcion),
                ];
            });
        Cache::put('cache_configuracion_ups_servicios', $records, now()->addHours(24));

        return $records;
    }
}

/*SuSaludServicio*/
if (!function_exists('cache_configuracion_su_salud_servicios')) {
    function cache_configuracion_su_salud_servicios()
    {
        if (Cache::has('cache_configuracion_su_salud_servicios')) {
            return Cache::get('cache_configuracion_su_salud_servicios');
        }

        $records = SuSaludServicio::query()
            ->orderBy('Descripcion')
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => $row->Codigo,
                    'label' => $row->Codigo . ' - ' . str_to_upper_utf8($row->Descripcion),
                ];
            });
        Cache::put('cache_configuracion_su_salud_servicios', $records, now()->addHours(24));

        return $records;
    }
}

/*UpsRenaes*/
if (!function_exists('cache_configuracion_ups_renaes')) {
    function cache_configuracion_ups_renaes()
    {
        if (Cache::has('cache_configuracion_ups_renaes')) {
            return Cache::get('cache_configuracion_ups_renaes');
        }

        $records = UpsRenaes::query()
            ->orderBy('Descripcion')
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => (int)$row->UPS,
                    'label' => $row->UPS . ' - ' . str_to_upper_utf8($row->Descripcion),
                ];
            });
        Cache::put('cache_configuracion_ups_renaes', $records, now()->addHours(24));

        return $records;
    }
}

/*HisColegios*/
if (!function_exists('cache_configuracion_his_colegios')) {
    function cache_configuracion_his_colegios()
    {
        if (Cache::has('cache_configuracion_his_colegios')) {
            return Cache::get('cache_configuracion_his_colegios');
        }

        $records = HisColegio::query()
            ->orderBy('cod_col')
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => $row->cod_col,
                    'label' => $row->des_col,
                ];
            });
        Cache::put('cache_configuracion_his_colegios', $records, now()->addHours(24));

        return $records;
    }
}

/*DepartamentoHospital*/
if (!function_exists('cache_configuracion_departamentos_hospital')) {
    function cache_configuracion_departamentos_hospital()
    {
        if (Cache::has('cache_configuracion_departamentos_hospital')) {
            return Cache::get('cache_configuracion_departamentos_hospital');
        }

        $records = DepartamentoHospital::query()
            ->where('Estado', 1)
            ->orderBy('IdDepartamento')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdDepartamento,
                    'value' => (int)$row->IdDepartamento,
                    'label' => str_to_upper_utf8($row->Nombre),
                ];
            });
        Cache::put('cache_configuracion_departamentos_hospital', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_configuracion_departamentos_hospital_limpiar')) {

    function cache_configuracion_departamentos_hospital_limpiar()
    {
        Cache::forget('cache_configuracion_departamentos_hospital');
        cache_configuracion_departamentos_hospital();
    }
}

/*EspecialidadPrimaria*/
if (!function_exists('cache_configuracion_especialidades_primarias')) {
    function cache_configuracion_especialidades_primarias()
    {
        if (Cache::has('cache_configuracion_especialidades_primarias')) {
            return Cache::get('cache_configuracion_especialidades_primarias');
        }

        $records = EspecialidadPrimaria::query()
            ->where('Estado', 1)
            ->orderBy('IdEspecialidadPrimaria')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdEspecialidadPrimaria,
                    'value' => (int)$row->IdEspecialidadPrimaria,
                    'label' => str_to_upper_utf8($row->Nombre),
                    'IdDepartamento' => (int)$row->IdDepartamento,
                    'parent_id' => (int)$row->IdDepartamento,

                ];
            });
        Cache::put('cache_configuracion_especialidades_primarias', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_configuracion_especialidades_primarias_limpiar')) {
    function cache_configuracion_especialidades_primarias_limpiar()
    {
        Cache::forget('cache_configuracion_especialidades_primarias');
        cache_configuracion_especialidades_primarias();
    }
}

/*UbicacionFisica*/
if (!function_exists('cache_ubicacion_fisica')) {
    function cache_ubicacion_fisica()
    {
        if (Cache::has('cache_ubicacion_fisica')) {
            return Cache::get('cache_ubicacion_fisica');
        }

        $records = UbicacionFisica::with('especialidadPrimaria.departamento')
            ->where('Estado', 1)
            ->orderBy('IdUbicacionFisica')
            ->get()
            ->transform(function ($row) {
                $departamento = $row->especialidadPrimaria->departamento->Nombre ?? '';
                $especialidad = $row->especialidadPrimaria->Nombre ?? '';
                $ubicacion = $row->Nombre ?? '';

                return [
                    'id' => (int)$row->IdUbicacionFisica,
                    'value' => (int)$row->IdUbicacionFisica,
                    'label' => str_to_upper_utf8("{$departamento} ({$especialidad} - {$ubicacion})"),
                    'IdEspecialidadPrimaria' => $row->especialidadPrimaria->IdEspecialidadPrimaria ?? null,
                    'EspecialidadPrimaria' => $especialidad,
                    'IdDepartamento' => $row->especialidadPrimaria->departamento->IdDepartamento ?? null,
                    'Departamento' => $departamento,
                ];
            });

        Cache::put('cache_ubicacion_fisica', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_ubicacion_fisica_limpiar')) {
    function cache_ubicacion_fisica_limpiar()
    {
        Cache::forget('cache_ubicacion_fisica');
        cache_ubicacion_fisica();
    }
}

/*Programas Instituciones*/
if (!function_exists('cache_programas_instituciones')) {
    function cache_programas_instituciones()
    {
        if (Cache::has('cache_programas_instituciones')) {
            return Cache::get('cache_programas_instituciones');
        }

        $records = ProgramaInstitucion::query()
            ->where('Estado', 1)
            ->orderBy('IdInstitucion')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdInstitucion,
                    'value' => (int)$row->IdInstitucion,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });

        Cache::put('cache_programas_instituciones', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_programas_instituciones_limpiar')) {
    function cache_programas_instituciones_limpiar()
    {
        Cache::forget('cache_programas_instituciones');
        cache_programas_instituciones();
    }
}

/*Programas Tipos Documentos*/
if (!function_exists('cache_programas_tipos_documentos')) {
    function cache_programas_tipos_documentos()
    {
        if (Cache::has('cache_programas_tipos_documentos')) {
            return Cache::get('cache_programas_tipos_documentos');
        }

        $records = ProgramaTipoDocumento::query()
            ->where('Estado', 1)
            ->orderBy('IdTipoDocumento')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdTipoDocumento,
                    'value' => (int)$row->IdTipoDocumento,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });

        Cache::put('cache_programas_tipos_documentos', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_programas_tipos_documentos_limpiar')) {
    function cache_programas_tipos_documentos_limpiar()
    {
        Cache::forget('cache_programas_tipos_documentos');
        cache_programas_tipos_documentos();
    }
}

 
 
/*Tipos Solicitud Interconsulta*/
if (!function_exists('cache_tipos_solicitud_interconsulta')) {
    function cache_tipos_solicitud_interconsulta()
    {
        if (Cache::has('cache_tipos_solicitud_interconsulta')) {
            return Cache::get('cache_tipos_solicitud_interconsulta');
        }

        $records = TiposSolicitudInterconsulta::query()
            ->where('IdEstado', 1)
            ->orderBy('Descripcion')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->IdSolicitudInter,
                    'value' => (int)$row->IdSolicitudInter,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });

        Cache::put('cache_tipos_solicitud_interconsulta', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_tipos_solicitud_interconsulta_limpiar')) {
    function cache_tipos_solicitud_interconsulta_limpiar()
    {
        Cache::forget('cache_tipos_solicitud_interconsulta');
        cache_tipos_solicitud_interconsulta();
    }
}




/*Receta Dosis*/
if (!function_exists('cache_receta_dosis')) {
    function cache_receta_dosis()
    {
        if (Cache::has('cache_receta_dosis')) {
            return Cache::get('cache_receta_dosis');
        }

        $records = RecetaDosis::query()
            //->where('IdEstado', 1)
            ->orderBy('idDosis')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->idDosis,
                    'value' => (int)$row->idDosis,
                    'label' => str_to_upper_utf8($row->NumeroDosis),
                ];
            });

        Cache::put('cache_receta_dosis', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_receta_dosis_limpiar')) {
    function cache_receta_dosis_limpiar()
    {
        Cache::forget('cache_receta_dosis');
        cache_receta_dosis();
    }
}

/*Receta Clasificacion Via Admin*/
if (!function_exists('cache_receta_clasificacion_via_admin')) {
    function cache_receta_clasificacion_via_admin()
    {
        if (Cache::has('cache_receta_clasificacion_via_admin')) {
            return Cache::get('cache_receta_clasificacion_via_admin');
        }

        $records = RecetaClasificacionViaAdmin::query()
            //->where('IdEstado', 1)
            ->orderBy('idCategoria')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => (int)$row->idCategoria,
                    'value' => (int)$row->idCategoria,
                    'label' => str_to_upper_utf8($row->Categoria),
                ];
            });

        Cache::put('cache_receta_clasificacion_via_admin', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_receta_clasificacion_via_admin_limpiar')) {
    function cache_receta_clasificacion_via_admin_limpiar()
    {
        Cache::forget('cache_receta_clasificacion_via_admin');
        cache_receta_clasificacion_via_admin();
    }
}
 
