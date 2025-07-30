<?php

namespace Modules\Api\Http\Controllers\Refcon;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller; 
use Modules\Api\Http\Controllers\RefconMicroservicios\RefconConsultaReferenciaController;
use Modules\Api\Http\Controllers\RefconReport\RefconContingenciaController; 

class ApiRefconController extends Controller
{
    //https://tabler.io/icons --- ICONOS  
    private $usoWs;
    function removeAccents($value)
    {
        if (is_string($value)) {
            $accents = [
                'Á' => 'A',
                'É' => 'E',
                'Í' => 'I',
                'Ó' => 'O',
                'Ú' => 'U',
                'á' => 'a',
                'é' => 'e',
                'í' => 'i',
                'ó' => 'o',
                'ú' => 'u',
                'Ñ' => 'N',
                'ñ' => 'n'
            ];
            return strtr($value, $accents);
        }
        return $value;
    }

    private function setInstitucionYUsoWs()
    {
        $this->usoWs = DB::select('EXEC WebS_ParametrosAccesoServicosWeb'); 
    }

    public function consultaReferenciaPaciente(Request $request)
    {
        $this->setInstitucionYUsoWs();
        if ($this->usoWs[0]->REFCON == 'S') {
            try {
                $parametros = [
                    "tipodocumento" => $request->tipodocumento,
                    "numerodocumento" => $request->numerodocumento
                ];

                $respuestaApi = (new RefconConsultaReferenciaController())->consultaReferencia(request()->merge($parametros));
                $data = $respuestaApi->getData(true);
                $responseOrigen = $data['data'];

                if ($responseOrigen['codigo'] !== '0000') {
                    if ($request->IdPaciente) {
                        $datosPaciente = ["IdPaciente" => $request->IdPaciente];
                        $responseRefconGalenos = $this->generarReferenciasGalen_Buscar_IdPaciente(request()->merge($datosPaciente));
                        if ($responseRefconGalenos['success']) {
                            return [
                                'success' => true,
                                'data' => null,
                                'referenciaGalenos' => $responseRefconGalenos,
                                'codRespuesta' => '0000',
                                'upsDestinos' => '',
                                'try' => '',
                                'message' => 'CONSULTA SOLO GALENOS',
                            ];
                        } else {
                            return [
                                'success' => false,
                                'data' => null,
                                'referenciaGalenos' => null,
                                'codRespuesta' => $responseOrigen['codigo'],
                                'upsDestinos' => '',
                                'try' => 'ConnectionException',
                                'message' => 'responseOrigen, RESPUESTA FALSE',
                            ];
                        }
                    }
                }

                $referenciasOrigen = $responseOrigen['datos']['datos'] ?? [];
                $referencias = [];
                $upsDestinosUnicos = [];

                foreach ($referenciasOrigen as $item) {
                    $ref = $item['data']; 
                    $referencias[] = $item['data'];
                    $params = [
                        "IdReferenciaRefCon" => $ref['idReferencia'],
                        "CodigoEstado" => $ref['codigoEstado'],
                        "EstadoReferencia" => $ref['estado'],
                        "NumeroReferencia" => $ref['numeroReferencia'],
                        "FechaEnvio" => $ref['fechaEnvio'],
                        "UpsOrigen" => $ref['upsOrigen'],
                        "DescUpsOrigen" => $ref['descUpsOrigen'],
                        "UpsDestino" => $ref['upsDestino'],
                        "DescUpsDestino" => $ref['descUpsDestino'],
                        "CodigoEstablecimientoOrigen" => $ref['codigoestablecimientoOrigen'],
                        "idEstablecimientoReferencia" => $ref['codigoestablecimientoOrigen'],
                        "EstablecimientoOrigen" => $ref['establecimientoOrigen'],
                        "TipoDocumento" => $ref['tipoDocumento'],
                        "NumeroDocumento" => $ref['numeroDocumento'],
                        "CodigoEspecialidad" => $ref['codigoEspecialidad'],
                        "Especialidad" => $ref['especialidad'],
                        "IdEmpleadoRegistra" => Auth::user()->IdEmpleado,
                        "Tipo" => 'Refcon'
                    ];
                    $this->WebS_ReferenciasGuardar(request()->merge($params));

                    if (!empty($ref['upsDestino'])) {
                        $upsDestinosUnicos[] = $ref['upsDestino'];
                    }
                }

                $res = [
                    'success' => true,
                    'codRespuesta' => $responseOrigen['codigo'],
                    'upsDestinos' => '',
                    'referencia' => $referencias,
                    'referenciaGalenos' => null,
                ];

                if ($request->IdPaciente) {
                    $datosPaciente = ["IdPaciente" => $request->IdPaciente];
                    $responseRefconGalenos = $this->generarReferenciasGalen_Buscar_IdPaciente(request()->merge($datosPaciente));
                    $res['referenciaGalenos'] = $responseRefconGalenos;
                } 

                return $res;
            } catch (\Throwable $e) {
                return [
                    'success' => false,
                    'data' => null,
                    'codRespuesta' => -2,
                    'upsDestinos' => '',
                    'try' => class_basename($e),
                    'message' => $e->getMessage(),
                ];
            }
        } else if ($this->usoWs[0]->REFCON == 'N') {


            try {
                $form = [
                    "codigo_tipo_documento" => $request->tipodocumento,
                    "numero_documento" => $request->numerodocumento
                ];
                $response = (new RefconContingenciaController())->historicoPacientesReferidosContingencia(request()->merge($form));

                $referencias = [];
                if ($response["success"]) {
                    $data = $response['data'] ?? [];
                    if (!empty($data)) {
                        $referencias = array_map(function ($referencia) {
                            return [
                                'IdReferenciaDB' => $referencia['idreferencia'] ?? null,
                                'codigoEspecialidad' => $referencia['idespecialidad'] ?? null,
                                'codigoEstablecimientoOrigen' => $referencia['idestorigen'] ?? null,
                                'codigoEstado' => $referencia['fgestado'] ?? null,
                                'descUpsDestino' => $referencia['descupsdestino'] ?? null,
                                'descUpsOrigen' => $referencia['idupsorigen'] ?? null,
                                'especialidad' => $referencia['especialidad'] ?? null,
                                'establecimientoOrigen' => $referencia['nombestorigen'] ?? null,
                                'estado' => $referencia['estadoactual'] ?? null,
                                'fechaEnvio' => isset($referencia['fechaenvio'])
                                    ? date('d-m-Y', strtotime(str_replace('/', '-', explode(' - ', $referencia['fechaenvio'])[0])))
                                    : null,
                                'idReferencia' => $referencia['idreferencia'] ?? null,
                                'numeroDocumento' => $referencia['numdoc'] ?? null,
                                'numeroReferencia' => $referencia['nroreferencia'] ?? null,
                                'tipoDocumento' => $referencia['idtipodoc'] ?? null,
                                'upsDestino' => $referencia['idupsdestino'] ?? null,
                                'upsOrigen' => $referencia['idupsorigen'] ?? null,
                            ];
                        }, $data);
                    } else {
                        $this->generarConsutlaReferenciaBD($request);
                    }
                } else {
                    $this->generarConsutlaReferenciaBD($request);
                }
                $responseRefconGalenos = null;
                if ($request->IdPaciente) {
                    $datosPaciente = ["IdPaciente" => $request->IdPaciente];
                    $responseRefconGalenos = $this->generarReferenciasGalen_Buscar_IdPaciente(request()->merge($datosPaciente));
                }

                return [
                    'URLRespuesta' => "/refcon/historico_pacientes_referidos_contingencia",
                    'codRespuesta' => '0000',
                    'referencia' => $referencias,
                    'success' => true,
                    'upsDestinos' => '',
                    'referenciaGalenos' => $responseRefconGalenos,
                ];
            } catch (\Exception $e) {
                $this->generarConsutlaReferenciaBD($request);
            }
        } else {
            return [
                'success' => false,
                'referencia' => null,
                'data' => null,
                'codRespuesta' => -2,
                'message' => 'SERVICIO DE REFCON INACTIVO',
                'referenciaGalenos' => null,
            ];
        }
    }

    public function generarConsutlaReferenciaBD(Request $request)
    {
        try {
            // CONSULTAMOS A LA BASE DE DATOS LOCAL EL LISTADO DE REFERENCIAS EXISTENTES
            $ReferenciasBuscarTipoNumeroDocumento = DB::select(
                'EXEC WebS_ReferenciasBuscarTipoNumeroDocumento @TipoDocumento = ?, @NumeroDocumento = ?, @IdTipoServicio  = ?',
                [$request->tipodocumento, $request->numerodocumento, 1]
            );
            // Transformar los datos para incluir solo ciertos campos 
            $referenciasFiltradas = array_map(function ($referencia) {
                return [
                    'IdReferenciaRefCon' => $referencia->IdReferenciaRefCon,
                    'codigoEspecialidad' => $referencia->CodigoEspecialidad,
                    'idEstablecimientoReferencia' => $referencia->CodigoEstablecimientoOrigen?? $referencia->codigoestablecimientoOrigen ,
                    'codigoEstablecimientoOrigen' => $referencia->CodigoEstablecimientoOrigen ?? $referencia->codigoestablecimientoOrigen ,
                    'codigoEstado' => $referencia->CodigoEstado,
                    'descUpsDestino' => $referencia->DescUpsDestino,
                    'descUpsOrigen' => $referencia->DescUpsOrigen,
                    'especialidad' => $referencia->Especialidad,
                    'establecimientoOrigen' => $referencia->EstablecimientoOrigen,
                    'estado' => $referencia->EstadoReferencia,
                    'fechaEnvio' => $referencia->FechaEnvio,
                    'idReferencia' => $referencia->IdReferenciaRefCon,
                    'numeroDocumento' => $referencia->NumeroDocumento,
                    'numeroReferencia' => $referencia->NumeroReferencia,
                    'tipoDocumento' => $referencia->TipoDocumento,
                    'upsDestino' => $referencia->UpsDestino,
                    'upsOrigen' => $referencia->UpsOrigen,
                ];
            }, $ReferenciasBuscarTipoNumeroDocumento);

            // Extraer upsDestino únicos
            $upsDestinosUnicos = array_unique(array_column($referenciasFiltradas, 'upsDestino'));
            $upsDestinosCadena = implode(',', $upsDestinosUnicos);

            $responseRefconGalenos = null;
            if ($request->IdPaciente) {
                $datosPaciente = ["IdPaciente" => $request->IdPaciente];
                $responseRefconGalenos = $this->generarReferenciasGalen_Buscar_IdPaciente(request()->merge($datosPaciente));
            }

            $res['referencia'] = $referenciasFiltradas;
            $res['upsDestinos'] = $upsDestinosCadena;
            $res['referenciaGalenos'] = $responseRefconGalenos;

            $res['codRespuesta'] = '0000';
            $res['URLRespuesta'] = 'CONSULTA A LA BASE LOCAL';
            $res['message'] = 'CONSULTA A LA BASE LOCAL';
            return $res;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'codRespuesta' => -2,
                'upsDestinos' => '',
                'message' => $e->getMessage()
            ];
        }
    }

    public function generarReferenciasGalen_Buscar_IdPaciente(Request $request)
    {
        try {
            // CONSULTAMOS A LA BASE DE DATOS LOCAL EL LISTADO DE REFERENCIAS EXISTENTES
            $ReferenciaGalenos = DB::select('EXEC WebS_ReferenciasGalen_Buscar_IdPaciente @IdPaciente = ?', [$request->IdPaciente]);
            $referenciasFiltradas = array_map(function ($referencia) {
                return [
                    'IdReferenciaGalenos' => $referencia->IdReferencia,
                    'NroReferencia' => $referencia->NroReferencia,
                    'IdEspecialidad' => $referencia->IdEspecialidad,
                    'Especialidad' => $referencia->Especialidad,
                    'Tipo' => 'Galenos'
                ];
            }, $ReferenciaGalenos);

            return [
                'success' => true,
                'data' => $referenciasFiltradas,
                'message' => 'Consulta Exitosa'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    public function WebS_ReferenciasGuardar(Request $request)
    {
        try {
            $resultado = DB::select(
                'EXEC WebS_ReferenciasGuardar 
                    @IdReferenciaRefCon = ?, 
                    @IdPaciente = ?, 
                    @CodigoEstado = ?, 
                    @EstadoReferencia = ?, 
                    @NumeroReferencia = ?, 
                    @FechaEnvio = ?, 
                    @UpsOrigen = ?, 
                    @DescUpsOrigen = ?, 
                    @UpsDestino = ?, 
                    @DescUpsDestino = ?, 
                    @CodigoEstablecimientoOrigen = ?, 
                    @EstablecimientoOrigen = ?, 
                    @TipoDocumento = ?, 
                    @NumeroDocumento = ?, 
                    @CodigoEspecialidad = ?, 
                    @Especialidad = ?',
                [
                    $request->IdReferenciaRefCon,
                    $request->IdPaciente,
                    $request->CodigoEstado,
                    $request->EstadoReferencia,
                    $request->NumeroReferencia,
                    $request->FechaEnvio,
                    $request->UpsOrigen,
                    $request->DescUpsOrigen,
                    $request->UpsDestino,
                    $request->DescUpsDestino,
                    $request->CodigoEstablecimientoOrigen,
                    $request->EstablecimientoOrigen,
                    $request->TipoDocumento,
                    $request->NumeroDocumento,
                    $request->CodigoEspecialidad,
                    $request->Especialidad
                ]
            );

            // Manejar el resultado
            if (!empty($resultado) && $resultado[0]->Codigo === 'OK') {
                return response()->json([
                    'success' => true,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo ?? 'ERROR',
                    'mensaje' => $resultado[0]->Mensaje ?? 'Error desconocido.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'EXCEPTION',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    public function AtencionesReferenciaGuardar(Request $request)
    {
        try {
            $resultado = DB::select(
                'EXEC WebS_AtencionesReferenciaGuardar 
                    @IdReferencia = ?, 
                    @IdReferenciaRefCon = ?, 
                    @ArchivoB64 = ?, 
                    @IdEmpleadoRegistra = ?, 
                    @IdCita = ?, 
                    @IdcuentaAtencion = ?',
                [
                    $request->IdReferencia,
                    $request->IdReferenciaRefCon,
                    $request->ArchivoB64,
                    Auth::user()->IdEmpleado,
                    $request->IdCita,
                    $request->IdcuentaAtencion
                ]
            );

            if (!empty($resultado) && $resultado[0]->Codigo === 'OK') {
                return response()->json([
                    'success' => true,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo ?? 'ERROR',
                    'mensaje' => $resultado[0]->Mensaje ?? 'Error desconocido.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'EXCEPTION',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    public function WebS_RefConPacientesGuardar(Request $request)
    {
        try {
            $resultado = DB::select(
                'EXEC WebS_RefConPacientesGuardar 
                    @IdPaciente = ?, 
                    @IdEtnia = ?, 
                    @IdSexo = ?, 
                    @NroHis = ?, 
                    @NumDoc = ?, 
                    @IdTipoDoc = ?, 
                    @IdUbigeoNac = ?, 
                    @TDoc = ?, 
                    @NomCompPac = ?, 
                    @CelularPac = ?, 
                    @CorreoPac = ?, 
                    @IdUbigeoRes = ?, 
                    @FechaNacPac = ?, 
                    @IdEmpleadoRegistra = ?',
                [
                    $request->IdPaciente,              // ID del paciente
                    $request->IdEtnia,                 // ID de la etnia (puede ser null)
                    $request->IdSexo,                  // Sexo del paciente
                    $request->NroHis,                  // Número de historia
                    $request->NumDoc,                  // Número de documento
                    $request->IdTipoDoc,               // ID del tipo de documento
                    $request->IdUbigeoNac,             // ID del ubigeo de nacimiento (puede ser null)
                    $request->TDoc,                    // Tipo de documento
                    $request->NomCompPac,              // Nombre completo del paciente
                    $request->CelularPac,              // Número de celular (puede ser null)
                    $request->CorreoPac,               // Correo del paciente (puede ser null)
                    $request->IdUbigeoRes,             // ID del ubigeo de residencia
                    $request->FechaNacPac,             // Fecha de nacimiento
                    $request->IdEmpleadoRegistra       // ID del empleado que registra
                ]
            );

            // Manejar el resultado
            if (!empty($resultado) && $resultado[0]->Codigo === 'OK') {
                return response()->json([
                    'success' => true,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo ?? 'ERROR',
                    'mensaje' => $resultado[0]->Mensaje ?? 'Error desconocido.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'EXCEPTION',
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }
}
