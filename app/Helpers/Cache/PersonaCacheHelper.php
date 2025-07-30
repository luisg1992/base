<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Modules\Persona\Models\Empleado;
use Modules\Persona\Models\Etnia;
use Modules\Persona\Models\Medico;
use Modules\Persona\Models\TipoCargo;
use Modules\Persona\Models\TipoCondicionTrabajo;
use Modules\Persona\Models\TipoDestacado;
use Modules\Persona\Models\TipoDocumentoIdentidad;
use Modules\Persona\Models\TipoEmpleado;
use Modules\Persona\Models\TipoEstadoCivil;
use Modules\Persona\Models\TipoEtnia;
use Modules\Persona\Models\TipoGradoInstruccion;
use Modules\Persona\Models\TipoIdioma;
use Modules\Persona\Models\TipoOcupacion;
use Modules\Persona\Models\TipoProcedencia;
use Modules\Persona\Models\TipoReligion;
use Modules\Persona\Models\TipoSexo;

/*Empledaos con Usuario y Roles*/

if (!function_exists('cache_persona_empleados_usuario_roles')) {
    function cache_persona_empleados_usuario_roles()
    {
        if (Cache::has('cache_persona_empleados_usuario_roles')) {
            return Cache::get('cache_persona_empleados_usuario_roles');
        }

        // Obtenemos todos los usuarios con su relación a empleados y roles
        $usuarios = User::with(['empleado', 'roles'])
            ->whereNotNull('IdEmpleado')
            ->get();

        $empleados = $usuarios->filter(fn($user) => $user->empleado !== null)
            ->map(function ($user) {
                $empleado = $user->empleado;

                return [
                    'id' => $empleado->IdEmpleado,
                    'label' => str_to_upper_utf8("{$empleado->ApellidoPaterno} {$empleado->ApellidoMaterno} {$empleado->Nombres}"),
                    'usuario' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'roles' => $user->roles->pluck('name')->toArray(),
                    ],
                ];
            })->values();

        Cache::put('cache_persona_empleados_usuario_roles', $empleados, now()->addHours(24));
        return $empleados;
    }
}

if (!function_exists('cache_persona_empleados_usuario_roles_limpiar')) {
    function cache_persona_empleados_usuario_roles_limpiar()
    {
        Cache::forget('cache_persona_empleados_usuario_roles');
        cache_persona_empleados_usuario_roles();
    }
}

/*TipoEmpleado*/
if (!function_exists('cache_persona_tipo_empleados')) {
    function cache_persona_tipo_empleados()
    {
        if (Cache::has('cache_persona_tipo_empleados')) {
            return Cache::get('cache_persona_tipo_empleados');
        }

        $records = TipoEmpleado::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdTipoEmpleado,
                    'value' => $row->IdTipoEmpleado,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });
        Cache::put('cache_persona_tipo_empleados', $records, now()->addHours(24));
        return $records;
    }
}

/*Empleado*/
if (!function_exists('cache_persona_empleados')) {
    function cache_persona_empleados()
    {
        if (Cache::has('cache_persona_empleados')) {
            return Cache::get('cache_persona_empleados');
        }

        $records = Empleado::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdEmpleado,
                    'value' => $row->IdEmpleado,
                    'label' => str_to_upper_utf8($row->ApellidoPaterno . ' ' . $row->ApellidoMaterno . ' ' . $row->Nombres),
                ];
            });
        Cache::put('cache_persona_empleados', $records, now()->addHours(24));
        return $records;
    }
}

/*Medico*/
if (!function_exists('cache_persona_medicos')) {
    function cache_persona_medicos()
    {
        if (Cache::has('cache_persona_medicos')) {
            return Cache::get('cache_persona_medicos');
        }

        $records = Medico::query()
            ->with('empleado', 'especialidades', 'especialidades.especialidad')
            ->whereHas('empleado', function ($query) {
                $query->where('esActivo', 1);
            })
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdMedico,
                    'value' => $row->IdMedico,
                    'label' => str_to_upper_utf8($row->empleado->ApellidoPaterno . ' ' . $row->empleado->ApellidoMaterno . ' ' . $row->empleado->Nombres),
                    'especialidades' => $row->especialidades->transform(function ($es) use ($row) {
                        return [
                            'IdEspecialidad' => $es->IdEspecialidad,
                            'Nombre' => $es->especialidad->Nombre,
                            'idEstado' => $es->idEstado,
                            'id' => $row->IdMedico,
                            'label' => str_to_upper_utf8($row->empleado->ApellidoPaterno . ' ' . $row->empleado->ApellidoMaterno . ' ' . $row->empleado->Nombres . '  - (' . $es->especialidad->Nombre . ')'),
                        ];
                    }),
                ];
            });
        Cache::put('cache_persona_medicos', $records, now()->addHours(24));
        return $records;
    }
}

/*TipoCargo*/
if (!function_exists('cache_persona_tipo_cargos')) {
    function cache_persona_tipo_cargos()
    {
        if (Cache::has('cache_persona_tipo_cargos')) {
            return Cache::get('cache_persona_tipo_cargos');
        }

        $records = TipoCargo::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->idTipoCargo,
                    'value' => $row->idTipoCargo,
                    'label' => str_to_upper_utf8($row->Cargo),
                ];
            });
        Cache::put('cache_persona_tipo_cargos', $records, now()->addHours(24));
        return $records;
    }
}

/*TipoCondicionTrabajo*/
if (!function_exists('cache_persona_tipo_condiciones_trabajo')) {
    function cache_persona_tipo_condiciones_trabajo()
    {
        if (Cache::has('cache_persona_tipo_condiciones_trabajo')) {
            return Cache::get('cache_persona_tipo_condiciones_trabajo');
        }

        $records = TipoCondicionTrabajo::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdCondicionTrabajo,
                    'value' => $row->IdCondicionTrabajo,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });

        Cache::put('cache_persona_tipo_condiciones_trabajo', $records, now()->addHours(24));
        return $records;
    }
}

//tiposSexo
if (!function_exists('cache_persona_tipo_sexos')) {
    function cache_persona_tipo_sexos()
    {
        if (Cache::has('cache_persona_tipo_sexos')) {
            return Cache::get('cache_persona_tipo_sexos');
        }

        $records = TipoSexo::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdTipoSexo,
                    'value' => $row->IdTipoSexo,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });
        Cache::put('cache_persona_tipo_sexos', $records, now()->addHours(24));
        return $records;
    }
}

if (!function_exists('cache_persona_tipo_documentos_identidad')) {
    function cache_persona_tipo_documentos_identidad()
    {
        if (Cache::has('cache_persona_tipo_documentos_identidad')) {
            return Cache::get('cache_persona_tipo_documentos_identidad');
        }

        // Registro especial "SIN DOCUMENTO"
        $sinDocumento = collect([[
            'id' => 0,
            'value' => 0,
            'label' => 'SIN DOCUMENTO',
            'CodigoSUNASA' => null,
            'CodigoHIS' => null,
            'CodigoSIS' => null,
        ]]);

        // Obtener registros de la tabla
        $records = TipoDocumentoIdentidad::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdDocIdentidad,
                    'value' => $row->IdDocIdentidad,
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'CodigoSUNASA' => $row->CodigoSUNASA,
                    'CodigoHIS' => $row->CodigoHIS,
                    'CodigoSIS' => $row->CodigoSIS,
                ];
            });

        // Concatenar el registro especial con los registros normales
        $allRecords = $sinDocumento->concat($records)->values();
        Cache::put('cache_persona_tipo_documentos_identidad', $allRecords, now()->addHours(24));
        return $allRecords;
    }
}


/*TipoDocumentoIdentidad*/
if (!function_exists('cache_persona_tipo_destacados')) {
    function cache_persona_tipo_destacados()
    {
        if (Cache::has('cache_persona_tipo_destacados')) {
            return Cache::get('cache_persona_tipo_destacados');
        }

        $records = TipoDestacado::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->idDestacado,
                    'value' => $row->idDestacado,
                    'label' => str_to_upper_utf8($row->Destacado),
                ];
            });

        Cache::put('cache_persona_tipo_destacados', $records, now()->addHours(24));
        return $records;
    }
}

/*TipoEstadoCivil*/
if (!function_exists('cache_persona_tipo_estados_civil')) {
    function cache_persona_tipo_estados_civil()
    {
        if (Cache::has('cache_persona_tipo_estados_civil')) {
            return Cache::get('cache_persona_tipo_estados_civil');
        }

        $records = TipoEstadoCivil::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdEstadoCivil,
                    'value' => $row->IdEstadoCivil,
                    'label' => str_to_upper_utf8($row->Descripcion),
                    'IdReniec' => (int) $row->IdReniec,
                ];
            });

        Cache::put('cache_persona_tipo_estados_civil', $records, now()->addHours(24));
        return $records;
    }
}

/*TipoGradoInstruccion*/
if (!function_exists('cache_persona_tipo_grados_instruccion')) {
    function cache_persona_tipo_grados_instruccion()
    {
        if (Cache::has('cache_persona_tipo_grados_instruccion')) {
            return Cache::get('cache_persona_tipo_grados_instruccion');
        }

        $records = TipoGradoInstruccion::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdGradoInstruccion,
                    'value' => $row->IdGradoInstruccion,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });

        Cache::put('cache_persona_tipo_grados_instruccion', $records, now()->addHours(24));
        return $records;
    }
}

/*TipoEtnias*/
if (!function_exists('cache_persona_tipo_etnias')) {
    function cache_persona_tipo_etnias()
    {
        if (Cache::has('cache_persona_tipo_etnias')) {
            return Cache::get('cache_persona_tipo_etnias');
        }

        $records = TipoEtnia::query()
            ->where('Estado', 1)
            ->orderBy('desetni', 'asc')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->codetni,
                    'value' => $row->codetni,
                    'label' => str_to_upper_utf8($row->desetni),
                ];
            });

        Cache::put('cache_persona_tipo_etnias', $records, now()->addHours(24));
        return $records;
    }
}

/*TipoIdiomas*/
if (!function_exists('cache_persona_tipo_idiomas')) {
    function cache_persona_tipo_idiomas()
    {
        if (Cache::has('cache_persona_tipo_idiomas')) {
            return Cache::get('cache_persona_tipo_idiomas');
        }

        $records = TipoIdioma::query()
            ->where('Estado', 1)
            ->orderBy('Lengua', 'asc')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdIdioma,
                    'value' => $row->IdIdioma,
                    'label' => str_to_upper_utf8($row->Lengua),
                ];
            });

        Cache::put('cache_persona_tipo_idiomas', $records, now()->addHours(24));
        return $records;
    }
}

/*TipoReligiones*/
if (!function_exists('cache_persona_tipo_religiones')) {
    function cache_persona_tipo_religiones()
    {
        if (Cache::has('cache_persona_tipo_religiones')) {
            return Cache::get('cache_persona_tipo_religiones');
        }

        $records = TipoReligion::query()
            ->where('Estado', 1)
            ->orderBy('DEscripcion', 'asc')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdReligion,
                    'value' => $row->IdReligion,
                    'label' => str_to_upper_utf8($row->DEscripcion),
                ];
            });

        Cache::put('cache_persona_tipo_religiones', $records, now()->addHours(24));
        return $records;
    }
}

/*TipoOcupaciones*/
if (!function_exists('cache_persona_tipo_ocupaciones')) {
    function cache_persona_tipo_ocupaciones()
    {
        if (Cache::has('cache_persona_tipo_ocupaciones')) {
            return Cache::get('cache_persona_tipo_ocupaciones');
        }

        $records = TipoOcupacion::query()
            ->where('Estado', 1)
            ->orderBy('descripcion', 'asc')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdTipoOcupacion,
                    'value' => $row->IdTipoOcupacion,
                    'label' => str_to_upper_utf8($row->descripcion),
                ];
            });

        Cache::put('cache_persona_tipo_ocupaciones', $records, now()->addHours(24));
        return $records;
    }
}

/*TipoProcedencias*/
if (!function_exists('cache_persona_tipo_procedencias')) {
    function cache_persona_tipo_procedencias()
    {
        if (Cache::has('cache_persona_tipo_procedencias')) {
            return Cache::get('cache_persona_tipo_procedencias');
        }

        $records = TipoProcedencia::query()
            ->where('Estado', 1)
            ->orderBy('Descripcion', 'asc')
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->IdTipoProcedencia,
                    'value' => $row->IdTipoProcedencia,
                    'label' => str_to_upper_utf8($row->Descripcion),
                ];
            });

        Cache::put('cache_persona_tipo_procedencias', $records, now()->addHours(24));
        return $records;
    }
}
