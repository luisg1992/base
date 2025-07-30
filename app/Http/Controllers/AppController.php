<?php

namespace App\Http\Controllers;

use App\Cache\Configuracion\DiagnosticoCache;
use App\Core\Services\StoredProcedureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Farmacia\Models\FarmaciaProveedor;

class AppController extends Controller
{
    public function getTables($table = null)
    {
        $tables = [
            'catalogo_servicios_especialidades' => fn() => [], //fn() => cache_fact_catalogo_servicios_especialidades(),

            'farmacia_almacenes' => fn() => cache_farmacia_almacenes(),
            'farmacia_tipo_conceptos' => fn() => cache_farmacia_tipo_conceptos(),
            'farmacia_tipo_documentos' => fn() => cache_farmacia_tipo_documentos(),
            'farmacia_tipo_procesos' => fn() => cache_farmacia_tipo_procesos(),
            'farmacia_tipo_compras' => fn() => cache_farmacia_tipo_compras(),
            'farmacia_estado_movimientos' => fn() => cache_farmacia_estado_movimientos(),

            'configuracion_departamentos_hospital' => fn() => cache_configuracion_departamentos_hospital(),
            'configuracion_tipo_servicios' => fn() => cache_configuracion_tipo_servicios(),

            'configuracion_especialidades' => fn() => cache_configuracion_especialidades(),
            'configuracion_especialidades_servicios' => fn() => cache_configuracion_especialidades_servicios(),

            'configuracion_tipo_edades' => fn() => cache_configuracion_tipo_edades(),
            'configuracion_tipo_sexos' => fn() => cache_configuracion_tipo_sexos(),
            'configuracion_tipo_terapias' => fn() => cache_configuracion_tipo_terapias(),
            'configuracion_ups_servicios' => fn() => cache_configuracion_ups_servicios(),
            'configuracion_su_salud_servicios' => fn() => cache_configuracion_su_salud_servicios(),
            'configuracion_ups_renaes' => fn() => cache_configuracion_ups_renaes(),
            'configuracion_especialidades_primarias' => fn() => cache_configuracion_especialidades_primarias(),
            'configuracion_ubicaciones_fisicas' => fn() => cache_ubicacion_fisica(),
            'configuracion_tipo_lugares_laborales' => fn() => cache_configuracion_tipo_lugares_laborales(),
            'configuracion_his_colegios' => fn() => cache_configuracion_his_colegios(),
            'configuracion_capitulos' => fn() => cache_configuracion_capitulos(),
            'configuracion_grupo' => fn() => cache_configuracion_grupo(),
            'configuracion_categoria' => fn() => cache_configuracion_categoria(),

            'configuracion_ubigeos' => fn() => cache_configuracion_ubigeos(),
            //            'configuracion_centros_poblados'  => fn() => cache_configuracion_centros_poblados(),

            'configuracion_formas_ingreso' => fn() => cache_triaje_emergencia_formas_ingreso(),
            'configuracion_estados_ingreso' => fn() => cache_triaje_emergencia_estado_ingreso(),
            'configuracion_servicio' => fn() => cache_configuracion_servicio(),
            'configuracion_fuente_financiamiento' => fn() => cache_configuracion_fuente_financiamiento(),

            'configuracion_tipo_financiamiento' => fn() => cache_configuracion_tipo_financiamiento(),
            'configuracion_tipo_financiador' => fn() => cache_configuracion_tipo_financiador(),
            'configuracion_farm_tipo_conceptos' => fn() => cache_configuracion_farm_tipo_conceptos(),
            'configuracion_area_tramita_seguros' => fn() => cache_configuracion_area_tramita_seguros(),
            'configuracion_caja_tipo_comprobante' => fn() => cache_configuracion_caja_tipo_comprobante(),
            'configuracion_tipo_consultorios' => fn() => cache_configuracion_tipo_consultorios(),

            'configuracion_emergencia_formas_ingreso' => fn() => cache_triaje_emergencia_formas_ingreso(),
            'configuracion_emergencia_estados_ingreso' => fn() => cache_triaje_emergencia_estado_ingreso(),
            'configuracion_emergencia_motivo_ingreso' => fn() => cache_triaje_emergencia_motivo(),
            'configuracion_emergencia_prioridad' => fn() => cache_triaje_emergencia_prioridad(),

            'core_parametros' => fn() => cache_core_parametros(),

            'persona_tipo_empleados' => fn() => cache_persona_tipo_empleados(),
            'persona_tipo_cargos' => fn() => cache_persona_tipo_cargos(),
            'persona_tipo_condiciones_trabajo' => fn() => cache_persona_tipo_condiciones_trabajo(),
            'persona_tipo_documentos_identidad' => fn() => cache_persona_tipo_documentos_identidad(),
            'persona_tipo_destacados' => fn() => cache_persona_tipo_destacados(),
            'persona_empleados' => fn() => cache_persona_empleados(),
            'persona_empleados_usuario_roles' => fn() => cache_persona_empleados_usuario_roles(),
            'persona_medicos' => fn() => cache_persona_medicos(),
            'persona_tipo_sexos' => fn() => cache_persona_tipo_sexos(),
            'persona_tipo_estados_civil' => fn() => cache_persona_tipo_estados_civil(),
            'persona_tipo_grados_instruccion' => fn() => cache_persona_tipo_grados_instruccion(),
            'persona_tipo_etnias' => fn() => cache_persona_tipo_etnias(),
            'persona_tipo_idiomas' => fn() => cache_persona_tipo_idiomas(),
            'persona_tipo_religiones' => fn() => cache_persona_tipo_religiones(),
            'persona_tipo_ocupaciones' => fn() => cache_persona_tipo_ocupaciones(),
            'persona_tipo_procedencias' => fn() => cache_persona_tipo_procedencias(),

            'seguridad_roles' => fn() => cache_seguridad_roles(),
            'seguridad_role' => fn() => cache_seguridad_role(),
            'programacion_general_tipo_tunos' => fn() => cache_programacion_tipo_turnos(),

            'cita_atencion_proxima_especialidades' => fn() => cache_atencion_proxima_especialidades(),
            'cita_atencion_interconsulta_especialidades' => fn() => cache_atencion_interconsulta_especialidades(),
            'cita_anulada_motivo' => fn() => cache_configuracion_cita_anulada_motivo(),
            'cita_demanda_insatisfecha' => fn() => cache_configuracion_cita_demanda_insatisfecha_motivo(),

            'programas_instituciones' => fn() => cache_programas_instituciones(),
            'programas_tipos_documentos' => fn() => cache_programas_tipos_documentos(),
        ];

        if ($table) {
            if (isset($tables[$table])) {
                return $tables[$table]();
            } else {
                return response()->json(['error' => 'La tabla no es válido.'], 400);
            }
        }

        return array_map(function ($func) {
            return $func();
        }, $tables);
    }

    public function filtrarEmpleados(Request $request)
    {
        $search = $request->get('buscar');
        $records = cache_persona_empleados();
        return collect($records)->filter(function ($item) use ($search) {
            return str_contains(strtolower($item['label']), strtolower($search));
        })->values();
    }

    public function filtrarDiagnosticos(Request $request)
    {
        $search = $request->get('buscar');
        $records = DiagnosticoCache::getCache();
        return collect($records)->filter(function ($item) use ($search) {
            return str_contains(strtolower($item['label']), strtolower($search));
        })->values();
    }

    public function filtrarEstablecimientos(Request $request)
    {
        $search = $request->get('buscar');
        $records = cache_configuracion_establecimientos();
        return collect($records)->filter(function ($item) use ($search) {
            return str_contains(strtolower($item['label']), strtolower($search));
        })->values();
    }

    public function filtrarServicioEspecialidades(Request $request)
    {
        $search = $request->get('buscar');
        $records = cache_fact_catalogo_servicios_especialidades();
        return collect($records)->filter(function ($item) use ($search) {
            return str_contains(strtolower($item['Nombre']), strtolower($search));
        })->values();
    }

    public function filtrarCentrosPoblados(Request $request)
    {
        $search = $request->get('buscar');
        $records = cache_configuracion_centros_poblados();
        return collect($records)->filter(function ($item) use ($search) {
            return str_contains(strtolower($item['IdDistrito']), strtolower($search));
        })->values();
    }

    public function filtrarProveedores(Request $request)
    {
        $buscar = $request->input('buscar');

        $records = FarmaciaProveedor::query()
            ->where('RazonSocial', 'like', '%' . $buscar . '%')
            ->orWhere('Ruc', 'like', '%' . $buscar . '%')
            ->orderBy('RazonSocial', 'asc')
            ->take(50)
            ->get();

        return $records->transform(function ($row) {
            return [
                'value' => $row->idProveedor,
                'label' => $row->Ruc . ' - ' . $row->RazonSocial,
            ];
        });
    }

    public function filtrarTipoConceptos(Request $request)
    {
        $TipoAlmacen = $request->input('TipoAlmacen');
        $TipoMov = $request->input('TipoMov');
        $TipoSuministro = $request->input('TipoSuministro');

        $sp = new StoredProcedureService();
        $params = [
            $TipoAlmacen,
            $TipoMov,
            $TipoSuministro,
        ];

        $records = collect($sp->executeSinPaginacion('dbo.FarmTipoConceptosDevuelveParaRegistroDeNiNs', $params));

        return $records->transform(function ($row) {
            $item = (array)$row;
            $item['value'] = (int)$row->idTipoConcepto;
            $item['label'] = trim($row->Concepto);

            return $item;
        });
    }

    public function filtrarTipoConceptosDetalle(Request $request)
    {
        $TipoAlmacen = $request->input('TipoAlmacen');
        $TipoMov = $request->input('TipoMov');
        $TipoSuministro = $request->input('TipoSuministro');
        $TipoConcepto = $request->input('TipoConcepto');

        $sp = new StoredProcedureService();
        $params = [
            $TipoAlmacen,
            $TipoMov,
            $TipoSuministro,
            $TipoConcepto
        ];

        return $sp->executeSinPaginacion('dbo.FarmTipoConceptosDevuelveParaRegistroDeNiNsSinDist', $params);
    }

    public function filtrarCatalogoBienesInsumos(Request $request)
    {
        $buscar = $request->input('buscar');
        $lcFiltro = 'and FactPuntosCargaBienesInsumos.IdPuntoCarga = 5 and FactCatalogoBienesInsumosHosp.IdTipoFinanciamiento = 1 and FactCatalogoBienesInsumos.Nombre like "%' . $buscar . '%"';
        $sp = new StoredProcedureService();
        $params = [
            $lcFiltro
        ];

        $records = collect($sp->executeSinPaginacion('dbo.FactCatalogoBienesInsumosSeleccionarBienesLike', $params));

        return $records->transform(function ($row) {
            $item = (array)$row;
            $item['value'] = $row->IdProducto;
            $item['label'] = trim($row->Nombre);

            return $item;
        });
    }

    public function filtrarAlmanacenesPorTipoDeConceptos(Request $request)
    {
        $filtro = $request->input('filtro');

        $sp = new StoredProcedureService();
        $params = [
            $filtro,
        ];

        $records = collect($sp->executeSinPaginacion('dbo.FarmAlmacenFiltrar', $params));

        return $records->transform(function ($row) {
            $item = (array)$row;
            $item['value'] = (int)$row->idAlmacen;
            $item['label'] = trim($row->descripcion);

            return $item;
        });
    }

    public function filtrarLugaresLaboralesPorTipo(Request $request)
    {
        $tipo = $request->input('tipo');

        if ($tipo === 1) {
            $spName = 'dbo.FarmAlmacenFiltrar';
            $parameter = "idTipoLocales<>'X' and idEstado=1";
            $idColumn = 'idAlmacen';
            $descriptionColumn = 'descripcion';
        } elseif ($tipo === 2) {
            $spName = 'dbo.FactPuntosCargaSeleccionarPorFiltro';
            $parameter = "TipoPunto='I'";
            $idColumn = 'IdPuntoCarga';
            $descriptionColumn = 'Descripcion';
        } elseif ($tipo === 3) {
            $spName = 'dbo.FactPuntosCargaSeleccionarPorFiltro';
            $parameter = "TipoPunto='L'";
            $idColumn = 'IdPuntoCarga';
            $descriptionColumn = 'Descripcion';
        } elseif ($tipo === 4) {
            $spName = 'dbo.TiposFinanciamientoSegunFiltro';
            $parameter = "esOficina=1";
            $idColumn = 'IdTipoFinanciamiento';
            $descriptionColumn = 'Descripcion';
        } elseif ($tipo === 5) {
            $spName = 'dbo.DevuelveEspecialidadesDelHospitalfiltro';
            $parameter = "(1)";
            $idColumn = 'IdEspecialidad';
            $descriptionColumn = 'Nombre';
        } elseif ($tipo === 6) {
            $spName = 'dbo.DevuelveEspecialidadesDelHospitalfiltro';
            $parameter = "(3)";
            $idColumn = 'IdEspecialidad';
            $descriptionColumn = 'Nombre';
        } elseif ($tipo === 7) {
            $spName = 'dbo.DevuelveEspecialidadesDelHospitalfiltro';
            $parameter = "(4)";
            $idColumn = 'IdEspecialidad';
            $descriptionColumn = 'Nombre';
        } elseif ($tipo === 8) {
            $spName = 'dbo.DevuelveEspecialidadesDelHospitalfiltro';
            $parameter = "(2)";
            $idColumn = 'IdEspecialidad';
            $descriptionColumn = 'Nombre';
        } else {
            $spName = 'dbo.AreaTramitaSegurosDevuelveTodosSegunFiltro';
            $parameter = "";
            $idColumn = 'IdAreaTramitaSeguros';
            $descriptionColumn = 'Descripcion';
        }

        $sp = new StoredProcedureService();
        $params = [
            $parameter
        ];

        $records = collect($sp->executeSinPaginacion($spName, $params));

        return $records->transform(function ($row) use ($idColumn, $descriptionColumn) {
            $item = (array)$row;
            $item['value'] = (int)$row->{$idColumn};
            $item['label'] = trim($row->{$descriptionColumn});

            return $item;
        });
    }

    public function getUserLogin(Request $request)
    {
        $menu = (new \Modules\Core\Http\Controllers\ModuloController())->getModulosPermitidosUsuario($request);
        if (empty($menu)) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'No se pudo cargar el menú del usuario. Contacte al administrador.',
            ]);
        }

        $user = auth()->user();
        $permisos = $user->getAllPermissions();

        $data = [
            'success' => true,
            'data' => [
                'menu' => $menu,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'isAdmin' => (bool)$user->esAdministrador(),
                ],
                'permission' => $permisos,
            ]
        ];

        return response()->json($data);
    }

    /*CARGA DE CPT Y DE APOYO AL DX*/
    public function filtrarCPT(Request $request)
    {
        $IdServicio = $request->input('IdServicio');
        $IdTipoFinanciamiento = $request->input('IdTipoFinanciamiento');
        $Filtro = $request->input('Filtro');

        return $this->ejecutarFiltroPuntoCarga($IdServicio, $IdTipoFinanciamiento, $Filtro);
    }

    public function filtrarApoyoDX(Request $request)
    {
        $IdServicio = $request->input('IdServicio');
        $IdTipoFinanciamiento = $request->input('IdTipoFinanciamiento');
        $Filtro = $request->input('Filtro');

        return $this->ejecutarFiltroPuntoCarga($IdServicio, $IdTipoFinanciamiento, $Filtro);
    }


    private function ejecutarFiltroPuntoCarga($IdServicio, $IdTipoFinanciamiento, $Filtro)
    {
        $sp = new StoredProcedureService();
        $params = [
            $IdServicio,
            $IdTipoFinanciamiento,
            $Filtro,
        ];

        $records = collect($sp->executeSinPaginacion('dbo.WebS_ProcedimentosFiltarNombrePuntoCarga', $params));

        return $records->transform(function ($row) {
            return [
                'value' => (int)$row->IdProducto,
                'label' => ($row->Nombre),
                'IdProducto' => (int)$row->IdProducto,
                'IdPuntoCarga' => (int)$row->IdPuntoCarga,
                'PuntoCarga' => ($row->PuntoCarga),
                'PrecioUnitario' => (float)$row->PrecioUnitario,
                'IdTipoFinanciamiento' => (int)$row->idTipoFinanciamiento,
            ];
        });
    }
 
    public function filtrarProductosFarmacia(Request $request)
    {
        $Filtro = $request->input('Filtro');
        $sp = new StoredProcedureService();
        $params = [
            $Filtro,
        ];

        $records = collect($sp->executeSinPaginacion('dbo.WebS_FarmaciaCatalogoBienesInsumosFiltarNombre', $params));

        return $records->transform(function ($row) {
            $item = (array)$row;
            $item['value'] = (int)$row->IdProducto;
            $item['label'] = trim($row->Nombre);
            $item['stock'] = trim($row->Stock);
            $item['precio'] = trim($row->Precio);
            $item['tipoProducto'] = trim($row->TipoProducto);

            return $item;
        });
    }
    
 
    public function filtrarEstablecimientosPorNombre(Request $request)
    {
        $Filtro = $request->input('Filtro');
        $sp = new StoredProcedureService();
        $params = [
            $Filtro,
        ];

        $records = collect($sp->executeSinPaginacion('dbo.WebS_BuscarEstablecimientosPorNombre', $params));

        return $records->transform(function ($row) {
            $item = (array)$row;
            $item['value'] = (int)$row->IdEstablecimiento;
            $item['label'] = trim($row->nombreCompleto);
            $item['nombre'] = trim($row->Nombre);
            $item['codigo'] = trim($row->Codigo);
            $item['iddistrito'] = trim($row->IdDistrito);
            $item['idtipo'] = trim($row->IdTipo); 
            return $item;
        });
    }
}
