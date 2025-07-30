<?php

namespace Modules\ConsultaExterna\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Referencia;
use Illuminate\Http\Request;
use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\impresion\ApiAuditoriaImpresionController;
use Modules\Api\Http\Controllers\Refcon\ApiRefconController;
use Modules\ConsultaExterna\Models\CitaDemandaInsatisfecha;
use Modules\Persona\Models\Paciente;

class CitasController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/ConsultaExterna/Citas/IndexCitas');
    }

    public function WebS_ProgramacionMedica_Servicios_BuscarFiltro(Request $request)
    {
        $idMedico = $request->IdMedico ?? null;
        $idEspecialidad = $request->IdEspecialidad ?? null;
        $codUps = $request->CodUps ?? null;
        $IdTipoServicio = $request->IdTipoServicio ?? null;

        $data = DB::select(
            'EXEC WebS_ProgramacionMedica_Servicios_BuscarFiltro @IdMedico = ?, @IdEspecialidad = ?, @CodUps = ?, @IdTipoServicio = ?',
            [$idMedico, $idEspecialidad, $codUps, $IdTipoServicio]
        );
        return response()->json($data);
    }

    public function WebS_ProgramacionMedica_Lista_BuscarFiltro(Request $request)
    {
        try {
            $Fecha = Carbon::parse($request->Fecha)->format('d/m/Y');
            $IdServicio = $request->IdServicio ?? null;
            $IdMedico = $request->IdMedico ?? null;
            $IdEspecialidad = $request->IdEspecialidad ?? null;
            $CodUps = $request->CodUps ?? null;
            $IdTipoServicio = $request->IdTipoServicio ?? null;

            $data = DB::select(
                'EXEC WebS_ProgramacionMedica_Lista_BuscarFiltro @IdServicio = ?, @IdMedico = ?, @Fecha = ?, @IdEspecialidad = ?, @CodUps = ?, @IdTipoServicio = ?',
                [$IdServicio, $IdMedico, $Fecha, $IdEspecialidad, $CodUps, $IdTipoServicio]
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la consulta: ' . $e->getMessage()], 500);
        }
    }

    public function WebS_CitasCupos_BuscarFiltro(Request $request)
    {
        try {
            $data = DB::select('EXEC WebS_CitasCupos_BuscarFiltro @IdProgramacion = ?', [$request->IdProgramacion]);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la consulta: ' . $e->getMessage()], 500);
        }
    }

    /*CONSULTAMOS EL SERVICIO DE IMPRESIÓN - JAVA*/
    public function CitasPrinter(Request $request)
    {
        try {
            // Construir los valores base
            $valores = [
                'IdCita' => $request->IdCita,
                'Formato' => $request->Formato ?? 'ticket',
                'Usuario' => Auth::user()->email,
            ];

            // Si el lote está presente, lo añadimos al array
            if ($request->filled('Lote')) {
                $valores['Lote'] = $request->Lote;
            }

            if ($request->filled('Grupo')) {
                $valores['Grupo'] = $request->Grupo;
                $valores['actionGrupo'] = 'service_printer_cita_resumen';
            }

            // Armamos la trama para el servicio
            $DataArray = [
                'IdUsuario' => Auth::user()->id,
                'action' => 'service_printer_cita',
                'valores' => $valores,
            ];

            // Convertimos a JSON
            $dataJson = json_encode($DataArray);
            $response = (new ApiAuditoriaImpresionController())->store($dataJson)->getData(true);
            $externalId = $response['success'] ? $response['data']['external_id'] : null;

            return response()->json([
                'success' => true,
                'codigo' => 'OK',
                'mensaje' => 'PROCESO DE IMPRESIÓN REALIZADO DE FORMA EXITOSA',
                'response' => $externalId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    public function CitasBuscarIdCitaFormatoPDF(Request $request)
    {
        try {
            $data = DB::select('EXEC WebS_CitasBuscarIdCita @IdCita = ?', [$request->IdCita]);
            if ($data) {
                $data = $data[0];
                if ($data) {
                    return func_generate_pdf(
                        'templates/citas',
                        'cita',
                        [
                            'filename' => 'CITA',
                            'Hospital' => 'HOSPITAL NACIONAL DOS DE MAYO',
                            'NumCupo' => $data->NumCupo,
                            'IdOrdenPago' => $data->IdOrdenPago,
                            'CuentaAtencion' => $data->IdCuentaAtencion,
                            'FechaCita' => $data->Fecha,
                            'HoraCita' => $data->HoraInicio,
                            'Consultorio' => $data->Consultorio,
                            'Medico' => $data->Medico,
                            'Paciente' => $data->Paciente,
                            'Documento' => $data->Documento,
                            'NroHistoriaClinica' => $data->NroHistoriaClinica,
                            'Financiamientos' => $data->Financiamientos,

                            'EsCitaAdicional' => $data->EsCitaAdicional,
                            'Turno' => $data->Turno ?? null,
                            'Genera' => $data->Genera,
                            'Imprime' => (optional(Auth::user())->email ? (optional(Auth::user())->email . ' ' . $data->Imprime) : ($request->Usuario . ' ' . $data->Imprime)),

                            'Mensaje' => $data->Mensaje,
                            'Terminal' => optional(Auth::user())->TerminalLogin ?? $request->TerminalLogin,
                        ],
                        $request->Formato,
                        false
                    );
                }
            }

            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'LA CITA CONULTADA NO EXISTE EN NUESTRA BASE DE DATOS, POR FAVOR COMUNÍQUESE CON SOPORTE TÉCNICO',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }


    public function CitasBuscarResumenFormatoPDF(Request $request)
    {
        try {
            $data = DB::select('EXEC WebS_CitasMasivasBuscarIdCita @IdCitas = ?', [$request->IdCitas]);
            if ($data) {
                $Citas = [];
                foreach ($data as $key => $item) {
                    $Citas[] = [
                        'Item' => $key + 1,
                        'Fecha' => $item->Fecha,
                        'Hora' => $item->HoraInicio,
                        'IdCuentaAtencion' => $item->IdCuentaAtencion,
                    ];
                }
                $data = $data[0];
                if ($data) {
                    return func_generate_pdf(
                        'templates/citas',
                        'cita_resumen',
                        [
                            'filename' => 'CITAS',
                            'Hospital' => 'HOSPITAL NACIONAL DOS DE MAYO',
                            'Consultorio' => $data->Consultorio,
                            'Medico' => $data->Medico,

                            'Paciente' => $data->Paciente,
                            'NroHistoriaClinica' => $data->NroHistoriaClinica,
                            'Documento' => $data->Documento,
                            'Financiamiento' => $data->Financiamientos,
                            'Citas' => $Citas,

                            'FechaImpresion' => $data->FechaImpresion,
                            'HoraImpresion' => $data->HoraImpresion,

                            'Mensaje' => $data->Mensaje,
                            'Usuario' => (optional(Auth::user())->email ? (optional(Auth::user())->email . ' ' . $data->Imprime) : ($request->Usuario . ' ' . $data->Imprime)),
                            'Terminal' => optional(Auth::user())->TerminalLogin ?? $request->TerminalLogin,
                        ],
                        $request->Formato,
                        false
                    );
                }
            }
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'LA CITA CONULTADA NO EXISTE EN NUESTRA BASE DE DATOS, POR FAVOR COMUNÍQUESE CON SOPORTE TÉCNICO',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    public function WebS_InsertarCita(Request $request)
    {
        $resultado = DB::select(
            'EXEC WebS_InsertarCita
                    @idpaciente = ?,
                    @horacita = ?,
                    @horafincita = ?,
                    @idfuentefinanciamiento = ?,
                    @IdTipoFinanciamiento = ?,
                    @idprogramacion = ?,
                    @TipoOrigenCita = ?,
                    @IdOrigenCita = ?,
                    @EsAdicional = ?,
                    @IdUsuario = ? ,
                    @Validador = ? ,
                    @NumCupo = ? ',
            [
                $request->idpaciente,
                $request->horacita,
                $request->horafincita,
                $request->idfuentefinanciamiento,
                $request->IdTipoFinanciamiento,
                $request->idprogramacion,
                $request->TipoOrigenCita,
                $request->IdOrigenCita,
                $request->EsAdicional,
                Auth::user()->IdEmpleado,
                $request->Validador ?? 0,
                $request->NumCupo ?? 0
            ]
        );

        if (!empty($resultado)) {
            if ($resultado[0]->Codigo == 'OK') {
                /* VISUALIZAR Y REGISTRAR PDF DE LA REFERENCIA */
                $dataReferencia = $request->input('dataReferencia');
                $IdReferencia = NULL;
                if ($dataReferencia) {
                    $dataReferencia['IdPaciente'] = $request->idpaciente;
                    $IdReferencia = Referencia::createOrUpdate($dataReferencia);

                    $refcon = new ApiRefconController();
                    $parametrosReferencia = [
                        "IdReferencia" => $IdReferencia,
                        "IdReferenciaRefCon" => $dataReferencia['idReferencia'],
                        "ArchivoB64" => '',
                        "IdCita" => $resultado[0]->IdCita,
                        "IdcuentaAtencion" => $resultado[0]->IdcuentaAtencion
                    ];
                    $refcon->AtencionesReferenciaGuardar(request()->merge($parametrosReferencia));
                }

                /* VISUALIZAR Y REGISTRAR PDF DE LA REFERENCIA */
                return response()->json([
                    'success' => true,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                    'IdCita' => $resultado[0]->IdCita,
                    'IdcuentaAtencion' => $resultado[0]->IdcuentaAtencion,
                    'ImprimirFua' => $resultado[0]->ImprimirFua ?? 0,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                    'IdCita' => $resultado[0]->IdCita ?? null,
                    'IdcuentaAtencion' => $resultado[0]->IdcuentaAtencion ?? null,
                    'ImprimirFua' => $resultado[0]->ImprimirFua ?? 0,
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
            ]);
        }
    }


    public function WebS_Listas_Paciente(Request $request)
    {
        try {
            $idPaciente = $request->IdPaciente;
            $tipo = strtoupper($request->TipoFiltro);

            $result = [];
            switch ($tipo) {
                case 'TODOS':
                    $result['CitasPendientes'] = DB::select('EXEC WebS_Pacientes_CitasPendientes_BuscarFiltro @IdPaciente = ?', [$idPaciente]);
                    $result['Interconsultas']  = DB::select('EXEC WebS_Interconsultas_BuscarFiltro @IdPaciente = ?', [$idPaciente]);
                    $result['CitaControl']     = DB::select('EXEC WebS_Pacientes_CitaControl_BuscarFiltro @IdPaciente = ?', [$idPaciente]);
                    $result['CitaAdicional']   = DB::select('EXEC WebS_CitasAdicionales_Lista_BuscarFiltro @IdPaciente = ?', [$idPaciente]);
                    break;

                case 'CITAS':
                    $result['CitasPendientes'] = DB::select('EXEC WebS_Pacientes_CitasPendientes_BuscarFiltro @IdPaciente = ?', [$idPaciente]);
                    break;

                case 'INTERCONSULTAS':
                    $result['Interconsultas'] = DB::select('EXEC WebS_Interconsultas_BuscarFiltro @IdPaciente = ?', [$idPaciente]);
                    break;

                case 'CONTROL':
                    $result['CitaControl'] = DB::select('EXEC WebS_Pacientes_CitaControl_BuscarFiltro @IdPaciente = ?', [$idPaciente]);
                    break;

                case 'ADICIONAL':
                    $result['CitaAdicional'] = DB::select('EXEC WebS_CitasAdicionales_Lista_BuscarFiltro @IdPaciente = ?', [$idPaciente]);
                    break;

                default:
                    return response()->json(['error' => 'TipoFiltro inválido.'], 400);
            }
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la consulta: ' . $e->getMessage()], 500);
        }
    }

    public function WebS_EliminarCita(Request $request)
    {
        $resultado = DB::select(
            'EXEC WebS_EliminarCita @IdCita = ?, @IdUsuarioAuditoria = ?, @Motivo = ?',
            [
                $request->IdCita,
                Auth::user()->IdEmpleado,
                $request->Motivo
            ]
        );

        $respuesta = $resultado[0] ?? null;
        return response()->json([
            'success' => $respuesta?->Codigo === 'OK',
            'codigo' => $respuesta->Codigo ?? 'ERROR',
            'mensaje' => $respuesta->Mensaje ?? 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
        ]);
    }

    public function WebS_CitasFiltrar(Request $request)
    {
        try {
            if ($request->input('TipoHistorial') === 'FUA') {
                $CitasFiltro = DB::select(
                    'EXEC WebS_CitasFiltrarFuaMasivo @FechaFiltro = ?, @IdEspecialidad = ?',
                    [
                        $request->input('FechaFiltro'),
                        $request->input('IdEspecialidad')
                    ]
                );

                $total = count($CitasFiltro) > 0 ? ($CitasFiltro[0]->TotalCount ?? 0) : 0;
                $response = ['Total' => $total];
                $agrupados = [];
                foreach ($CitasFiltro as $index => $cita) {
                    $consultorio = $cita->Consultorio ?? 'Sin Consultorio';
                    $IdConsultorio = $cita->IdConsultorio;

                    if (!isset($agrupados[$consultorio])) {
                        $agrupados[$consultorio] = [
                            'key' => $IdConsultorio,
                            'data' => [
                                'paciente' => $consultorio,
                                'cita' => '',
                                'medico' => 'Consultorio'
                            ],
                            'children' => []
                        ];
                    }

                    $agrupados[$consultorio]['children'][] = [
                        'key' => $IdConsultorio . '-' . $index,
                        'IdCita' => $cita->IdCita,
                        'IdPaciente' => $cita->IdPaciente,
                        'data' => [
                            'paciente' => trim($cita->Documento) . ' - ' . trim($cita->Paciente),
                            'cita' => $cita->FechaCita . ' - ' . $cita->HorarioCita,
                            'medico' => trim($cita->Medico)
                        ]
                    ];
                }

                $response['CitasFiltro'] = array_values($agrupados);
            } else if ($request->input('TipoHistorial') === 'CITAS') {
                $Page = $request->input('Page', 1);
                $PerPage = $request->input('PerPage', 50);

                $canPermission = ModuloHelper::obtenerPermisoBaseDesdeRuta();
                if (Auth::user()->can($canPermission . '.tab.historial.de.citas.seleccionar.empleados')) {
                    $IdEmpleadoRegistra = $request->input('IdEmpleadoRegistra');
                } else {
                    $IdEmpleadoRegistra = Auth::user()->IdEmpleado;
                }
                $fecha = formatear_fecha($request->input('FechaFiltro'));
                $CitasFiltro = DB::select(
                    'EXEC WebS_CitasFiltrar @FechaFiltro = ?, @IdEmpleadoRegistra = ?, @IdEspecialidad = ?, @BusquedaPaciente = ?, @PerPage = ?, @Page = ?',
                    [
                        $fecha,
                        $IdEmpleadoRegistra,
                        $request->input('IdEspecialidad'),
                        $request->input('BusquedaPaciente'),
                        $PerPage,
                        $Page
                    ]
                );

                $total = count($CitasFiltro) > 0 ? ($CitasFiltro[0]->TotalCount ?? 0) : 0;
                $response = ['Total' => $total];
                $response['CitasFiltro'] = $CitasFiltro;
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la consulta: ' . $e->getMessage()], 500);
        }
    }

    public function WebS_Interconsultas_Lista_BuscarFiltro(Request $request)
    {
        $fecha = $request->input('Fecha');
        $IdEspecialidad = $request->input('IdEspecialidad');
        $Page = $request->input('Page', 1);
        $PerPage = $request->input('PerPage', 50);
        $DocumentoHC = $request->input('DocumentoHC'); // ← Nuevo

        // Si DocumentoHC tiene valor, ignoramos la fecha
        if (!empty($DocumentoHC)) {
            $fecha = null;
        }

        try {
            $CitasInterconsultaFiltro = DB::select(
                'EXEC WebS_Interconsultas_Lista_BuscarFiltro 
                    @IdEspecialidad = ?, 
                    @PerPage = ?, 
                    @Page = ?, 
                    @Fecha = ?, 
                    @DocumentoHC = ?',
                [
                    $IdEspecialidad,
                    $PerPage,
                    $Page,
                    $fecha,
                    $DocumentoHC
                ]
            );

            $total = 0;
            if (count($CitasInterconsultaFiltro)) {
                $total = $CitasInterconsultaFiltro[0]->TotalCount ?? 0;
            }

            return response()->json([
                'CitasInterconsultaFiltro' => $CitasInterconsultaFiltro,
                'Total' => $total
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la consulta: ' . $e->getMessage()], 500);
        }
    }

    public function WebS_Pacientes_CitaControl_Lista_BuscarFiltro(Request $request)
    {
        try {
            $IdEspecialidad = $request->input('IdEspecialidad');
            $Page = $request->input('Page', 1);
            $PerPage = $request->input('PerPage', 50);
            $DocumentoHC = $request->input('DocumentoHC');

            $CitaControlFiltro = DB::select(
                'EXEC WebS_Pacientes_CitaControl_Lista_BuscarFiltro 
                    @IdEspecialidad = ?, 
                    @PerPage = ?, 
                    @Page = ?, 
                    @DocumentoHC = ?',
                [
                    $IdEspecialidad,
                    $PerPage,
                    $Page,
                    $DocumentoHC
                ]
            );

            $total = count($CitaControlFiltro) > 0 ? ($CitaControlFiltro[0]->TotalCount ?? 0) : 0;

            return response()->json([
                'CitaControlFiltro' => $CitaControlFiltro,
                'Total' => $total
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en la consulta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function WebS_InsertarCita_Interconsulta_CitaControl(Request $request)
    {
        $resultado = DB::select('EXEC WebS_Programacion_ObtenerDatosCupo  @IdProgramacion = ?', [$request->IdProgramacion]);
        if (!empty($resultado)) {
            if ($resultado[0]->Codigo == 'OK') {
                $parametrosCita = [
                    "idpaciente" => $request->IdPaciente,
                    "horacita" =>  $resultado[0]->HoraCita,
                    "horafincita" =>  $resultado[0]->HoraFinCita,
                    "idfuentefinanciamiento" => $request->IdFuentefinanciamiento,
                    "IdTipoFinanciamiento" =>  $request->IdTipoFinanciamiento,
                    "idprogramacion" =>  $resultado[0]->IdProgramacion,
                    "TipoOrigenCita" =>  $request->Tipo,
                    "IdOrigenCita" =>  $request->IdIdentificador,
                    "EsAdicional" => 0,
                    "Validador" =>  null
                ];
                return  $this->WebS_InsertarCita(request()->merge($parametrosCita));
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
            ]);
        }
    }

    public function WebS_Programacion_CuposDisponibles(Request $request)
    {
        try {
            $data = DB::select('EXEC WebS_Programacion_CuposDisponibles @IdEspecialidad = ?', [$request->IdEspecialidad]);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la consulta: ' . $e->getMessage()], 500);
        }
    }

    public function CitasFuaAdmisionPrinter(Request $request)
    {
        try {
            // Construir los valores base
            $valores = [
                'IdCita' => $request->IdCita,
                'Formato' => $request->Formato,
                'Usuario' => Auth::user()->email,
            ];

            // Si el lote está presente, lo añadimos al array
            if ($request->filled('Lote')) {
                $valores['Lote'] = $request->Lote;
            }

            // Armamos la trama para el servicio
            $DataArray = [
                'IdUsuario' => Auth::user()->id,
                'action' => 'service_printer_cita_fua',
                'valores' => $valores,
            ];

            // Convertimos a JSON
            $dataJson = json_encode($DataArray);

            // Enviamos al servicio de impresión
            $response = (new ApiAuditoriaImpresionController())->store($dataJson)->getData(true);
            $externalId = $response['success'] ? $response['data']['external_id'] : null;

            return response()->json([
                'success' => true,
                'codigo' => 'OK',
                'mensaje' => 'PROCESO DE IMPRESIÓN REALIZADO DE FORMA EXITOSA',
                'response' => $externalId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    public function WebS_GenerarFormatoFua(Request $request)
    {
        $resultado = DB::select('EXEC WebS_GenerarFormatoFua  @IdCita = ?', [$request->IdCita]);
        if (!empty($resultado)) {
            if ($resultado[0]->Codigo == 'OK') {
                return response()->json([
                    'success' => true,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            }
        }
    }

    public function WebS_AdmisionCitasFormatoFua(Request $request)
    {
        try {
            $data = DB::select('EXEC WebS_AdmisionCitasFormatoFua @IdCita = ?', [$request->IdCita]);
            if ($data) {
                $data = $data[0];
                if ($data) {
                    return  func_generate_pdf(
                        'templates/citas',
                        'fua',
                        [
                            'filename' => $request->IdCita . '_Fua_Admision_A4',
                            'Hospital' => 'HOSPITAL NACIONAL DOS DE MAYO',

                            'Usuario' => (optional(Auth::user())->email ? (optional(Auth::user())->email) : ($request->Usuario)),
                            'Terminal' => optional(Auth::user())->TerminalLogin ?? $request->TerminalLogin,
                            'LogoOficial' => null,
                            'LogoInstitucional' => null,

                            'A' => $data->A,
                            'B' => $data->B,
                            'C' => $data->C,
                            'CodRenipress' => $data->CodRenipress,
                            'NombreDeIpress' => $data->NombreDeIpress,
                            'HojaReferencia' => $data->HojaReferencia,
                            'TDI' => $data->TDI,
                            'DocumentoIdentidad' => $data->DocumentoIdentidad,

                            'ApellidoPaterno' => $data->ApellidoPaterno,
                            'ApellidoMaterno' => $data->ApellidoMaterno,
                            'PrimerNombre' => $data->PrimerNombre,
                            'OtrosNombres' => $data->OtrosNombres,
                            'Masculino' => $data->Masculino,
                            'Femenino' => $data->Femenino,

                            'FechaNacimientoD' => $data->FechaNacimientoD,
                            'FechaNacimientoM' => $data->FechaNacimientoM,
                            'FechaNacimientoA' => $data->FechaNacimientoA,

                            'HistoriaClinica' => $data->HistoriaClinica,
                            'Etnia' => $data->Etnia,
                            'DiaAtencion' => $data->DiaAtencion,
                            'MesAtencion' => $data->MesAtencion,
                            'AnoAtencion' => $data->AnoAtencion,
                            'Hora' => $data->Hora,
                            'UPS' => $data->UPS,

                            'nDNI' => $data->nDNI,
                            'NombreRespAtenc' => $data->NombreRespAtenc,
                            'NumeroColegiatura' => $data->NumeroColegiatura,
                            'ResponsableAtencion' => $data->ResponsableAtencion,
                            'Especialidad' => $data->Especialidad,
                            'nRne' => $data->nRne,
                            'Egresado' => $data->Egresado,
                            'Cod_Prestacional' => $data->Cod_Prestacional,
                            'Cuenta' => $data->Cuenta
                        ],
                        'a4',
                        $request->GuardarPDF,
                        false
                    );
                }
            }

            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'EL DOCUMENTO FUA NO EXISTE EN NUESTRA BASE DE DATOS, POR FAVOR COMUNÍQUESE CON SOPORTE TÉCNICO',
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    public function WebS_InsertarCitaControl(Request $request)
    {
        $resultado = DB::select(
            'EXEC WebS_InsertarCitaControl
                    @IdEspecialidad = ?,
                    @IdUsuarioReg = ?,
                    @IdPaciente = ? ,
                    @IdCuentaAtencion = ? ',
            [
                $request->IdEspecialidad,
                Auth::user()->IdEmpleado,
                $request->IdPaciente,
                $request->IdCuentaAtencion
            ]
        );

        if (!empty($resultado)) {
            if ($resultado[0]->Codigo == 'OK') {
                return response()->json([
                    'success' => true,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                    'IdProximaCita' => $resultado[0]->IdProximaCita,
                    'IdEspecialidad' => $resultado[0]->IdEspecialidad,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                    'IdEstadoVincula' => $resultado[0]->IdEstadoVincula ?? null,
                    'IdProximaCita' => $resultado[0]->IdProximaCita ?? null,
                    'IdEspecialidad' => $resultado[0]->IdEspecialidad ?? null,
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
            ]);
        }
    }

    public function WebS_PacienteConsultarCitaControl(Request $request)
    {
        $resultado = DB::select('EXEC WebS_PacienteConsultarCitaControl @IdPaciente = ? ', [$request->IdPaciente]);
        if (!empty($resultado)) {
            if ($resultado[0]->Codigo == 'OK') {
                return response()->json([
                    'success' => true,
                    'codigo' => $resultado[0]->Codigo ?? null,
                    'mensaje' => $resultado[0]->Mensaje ?? null,
                    'IdCuentaAtencion' => $resultado[0]->IdCuentaAtencion ?? null,
                    'IdProximaCita' => $resultado[0]->IdProximaCita ?? null,
                    'IdEstadoVincula' => $resultado[0]->IdEstadoVincula ?? null,
                    'IdEspecialidad' => $resultado[0]->IdEspecialidad ?? null,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo ?? null,
                    'mensaje' => $resultado[0]->Mensaje ?? null,
                    'IdCuentaAtencion' => $resultado[0]->IdCuentaAtencion ?? null,
                    'IdProximaCita' => $resultado[0]->IdProximaCita ?? null,
                    'IdEstadoVincula' => $resultado[0]->IdEstadoVincula ?? null,
                    'IdEspecialidad' => $resultado[0]->IdEspecialidad ?? null,
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
            ]);
        }
    }

    public function WebS_PreQuirurgico_BuscarFiltro(Request $request)
    {
        $IdCuentaAtencion = $request->IdCuentaAtencion ?? null;
        $IdPaciente = $request->IdPaciente ?? null;
        $data = DB::select(
            'EXEC WebS_PreQuirurgico_BuscarFiltro @IdCuentaAtencion = ?, @IdPaciente = ?',
            [$IdCuentaAtencion, $IdPaciente]
        );
        return response()->json($data);
    }

    public function WebS_InsertarInterconsulta_PreQuirurgico(Request $request)
    {
        $resultado = DB::select(
            'EXEC WebS_InsertarInterconsulta_PreQuirurgico
                    @IdCuentaAtencion = ?,
                    @IdPaciente = ?,
                    @IdEspecialidad = ? ,
                    @IdUsuario = ? ',
            [
                $request->IdCuentaAtencion,
                $request->IdPaciente,
                $request->IdEspecialidad,
                Auth::user()->IdEmpleado,
            ]
        );

        if (!empty($resultado)) {
            if ($resultado[0]->Codigo == 'OK') {
                return response()->json([
                    'success' => true,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                    'IdInterconsulta' => $resultado[0]->IdInterconsulta,
                    'IdEspecialidad' => $resultado[0]->IdEspecialidad,
                    'Especialidad' => $resultado[0]->Especialidad,
                    'Tipo' => $resultado[0]->Tipo,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
            ]);
        }
    }

    public function WebS_EliminarInterconsulta_PreQuirurgico(Request $request)
    {
        $resultado = DB::select(
            'EXEC WebS_EliminarInterconsulta_PreQuirurgico @IdInterconsulta = ? ',
            [$request->IdInterconsulta]
        );

        if (!empty($resultado)) {
            if ($resultado[0]->Codigo == 'OK') {
                return response()->json([
                    'success' => true,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
            ]);
        }
    }

    public function WebS_Validar_Cuentas_Pendientes(Request $request)
    {
        $resultado = DB::select('EXEC WebS_Validar_Cuentas_Pendientes @IdPaciente = ? ', [$request->IdPaciente]);
        if (!empty($resultado)) {
            if ($resultado[0]->Mensaje) {
                return response()->json([
                    'success' => true,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
            ]);
        }
    }

    public function WebS_Validar_EmpleadosEspecialidades(Request $request)
    {
        $tieneEspecialidades = Auth::user()?->empleado?->especialidades()->exists();
        if ($tieneEspecialidades) {
            $resultado = DB::select(
                'EXEC WebS_Validar_EmpleadosEspecialidades @IdEmpleado = ?, @IdEspecialidad = ?, @CodUps = ? ',
                [Auth::user()->IdEmpleado, $request->IdEspecialidad, $request->CodUps]
            );
            if (!empty($resultado)) {
                if ($resultado[0]->Codigo == 'OK') {
                    return response()->json([
                        'success' => true,
                        'codigo' => $resultado[0]->Codigo,
                        'mensaje' => $resultado[0]->Mensaje,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'codigo' => $resultado[0]->Codigo,
                        'mensaje' => $resultado[0]->Mensaje,
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => 'ERROR',
                    'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
                ]);
            }
        } else {
            return [
                'success' => true,
                'codigo' => 'OK',
                'mensaje' => 'El usuario no tiene especialidades asignadas.',
            ];
        }
    }

    public function WebS_CitaProximaCE_BuscarFiltro(Request $request)
    {
        $IdInterconsulta = $request->IdInterconsulta ?? null;
        $IdPaciente = $request->IdPaciente ?? null;
        $data = DB::select(
            'EXEC WebS_CitaProximaCE_BuscarFiltro @IdInterconsulta = ?, @IdPaciente = ?',
            [$IdInterconsulta, $IdPaciente]
        );
        return response()->json($data);
    }

    public function WebS_CitaProximaCE_UpdateEstado(Request $request)
    {
        try {
            DB::update(
                'UPDATE AtencionesSolicitudEspecialidades SET IdEstado = 3, IdCita = NULL WHERE IdSolicitudEspecialidad = ?',
                [$request->IdInterconsulta]
            );
            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente.',
                'data'    => $request->IdInterconsulta
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function InsertarCitaDemandaInsatisfecha(Request $request)
    {
        $demandaInsatisfecha = CitaDemandaInsatisfecha::CitaDemandaInsatisfechaStore($request->all());
        if ($demandaInsatisfecha) {

            $paciente = Paciente::where('IdPaciente', $request->IdPaciente)
                ->where('NroDocumento', $request->NumeroDocumento)
                ->first();
            if ($paciente) {
                $paciente->Telefono = $request->Celular;
                $paciente->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Cita registrada exitosamente.',
                'data'    => $demandaInsatisfecha
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la cita.'
            ], 500);
        }
    }

    public function WebS_DemandaInsatisfecha_Lista_BuscarFiltro(Request $request)
    {
        $fecha = $request->input('Fecha');
        $IdEspecialidad = $request->input('IdEspecialidad');
        $Page = $request->input('Page', 1);
        $PerPage = $request->input('PerPage', 50);
        $DocumentoHC = $request->input('DocumentoHC'); // ← Nuevo

        // Si DocumentoHC tiene valor, ignoramos la fecha
        if (!empty($DocumentoHC)) {
            $fecha = null;
        }

        try {
            $CitasDemandaInsatisfecha = DB::select(
                'EXEC WebS_DemandaInsatisfecha_Lista_BuscarFiltro 
                    @IdEspecialidad = ?, 
                    @PerPage = ?, 
                    @Page = ?, 
                    @Fecha = ?, 
                    @DocumentoHC = ?',
                [
                    $IdEspecialidad,
                    $PerPage,
                    $Page,
                    $fecha,
                    $DocumentoHC
                ]
            );

            $total = 0;
            if (count($CitasDemandaInsatisfecha)) {
                $total = $CitasDemandaInsatisfecha[0]->TotalCount ?? 0;
            }

            return response()->json([
                'CitasDemandaInsatisfecha' => $CitasDemandaInsatisfecha,
                'Total' => $total
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la consulta: ' . $e->getMessage()], 500);
        }
    }
}
