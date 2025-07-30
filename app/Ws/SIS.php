<?php

namespace App\Ws;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Api\Http\Controllers\Sis\SisContingenciaController;
use Modules\Api\Http\Controllers\Sis\SisController;
use Modules\Core\Models\SisAfiliado;
use Modules\Persona\Models\Paciente;

class SIS
{
    public static function getErrorCatalog($errorCode)
    {
        $catalog = [
            0 => 'DATOS EXITOSOS',
            12 => 'DNI CADUCADO (FUERA DE FECHA)',
            14 => 'NO SE ENCONTRÓ AFILIACIÓN PARA EL DNI CONSULTADO',
        ];

        return isset($catalog[$errorCode]) ? $catalog[$errorCode] : 'ERROR DESCONOCIDO';
    }

    public function ws_consulta_SISDatos(Request $request, $TipoDocSis)
    {
        try {
            $parametros = [
                "opcion" => $request->opcion,
                "TipoDocumento" => $TipoDocSis,
                "NroDocumento" => $request->numerodocumento,

                "nroHistoriaClinica" => $request->nroHistoriaClinica,
                "disa" => $request->disa,
                "tipoFormato" => $request->tipoFormato,
                "nroContrato" => $request->nroContrato,
                "correlativo" => $request->correlativo
            ];
            $res = (new SisController())->consultarAfiliadoFuaE(request()->merge($parametros));


            if ($res['success'] == false) {
                // Verificar si "message" es una cadena JSON válida
                if (isset($res['message'])) {
                    $jsonMessage = json_decode($res['message'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $errorMessage = $jsonMessage['message'] ?? null;
                        return [
                            'datos' => null,
                            'respuesta' => -1,
                            'descripcion' => $errorMessage,
                            'codigo' => -1
                        ];
                    }
                }
            }

            $result = $res['data'];
            // Elimina caracteres no deseados en la respuesta
            $search = ["'", "\"", "\\", "\n", "\r", "\t"];
            $replace = ["", "", "", "", "", ""];
            foreach ($result as $key => $value) {
                if (is_string($value)) {
                    $result[$key] = str_replace($search, $replace, $value);
                }
            }
            $TipoDocumento = $request->tipodocumento;
            $NroDocumento = $request->numerodocumento;
            if ($request->nroHistoriaClinica) {
                $paciente = Paciente::buscarPorNroHistoriaClinica($request->nroHistoriaClinica);
                if (!$paciente) {
                    return [
                        'sis' => $result,
                        'datos' => null,
                        'descripcion' => 'ERROR DE CONSULTA SIS: EL PACIENTE NO EXISTE EN NUESTRA BD PARA EL NUMERO DE HISTORIA ' . $request->nroHistoriaClinica,
                        'respuesta' => -1,
                        'codigo' => -1
                    ];
                }
                if ($result['ApePaterno']) {
                    $paciente->ApellidoPaterno = $result['ApePaterno'];
                    $paciente->UsoWebReniec = 1;
                }
                if ($result['ApeMaterno']) {
                    $paciente->ApellidoMaterno = $result['ApeMaterno'];
                }
                $paciente->save();

                $TipoDocumento = $paciente->IdDocIdentidad;
                $NroDocumento = $paciente->NroDocumento;
            } else {
                $paciente = Paciente::buscarPorDocumentoYTipoUpdate($NroDocumento, $TipoDocumento);
                if ($paciente) {
                    if ($result['ApePaterno']) {
                        $paciente->ApellidoPaterno = $result['ApePaterno'];
                        $paciente->UsoWebReniec = 1;
                    }
                    if ($result['ApeMaterno']) {
                        $paciente->ApellidoMaterno = $result['ApeMaterno'];
                    }
                    $paciente->save();
                }
            }

            // Mapeo de los datos para la inserción o actualización en la base de datos IdError
            $datos = [
                'IdError' => $result['IdError'],
                'Resultado' => $result['Resultado'],
                'TipoDocumento' => $TipoDocumento,
                'NroDocumento' => $NroDocumento,
                'ApePaterno' => $result['ApePaterno'],
                'ApeMaterno' => $result['ApeMaterno'],
                'Nombres' => $result['Nombres'],
                'FecAfiliacion' => $result['FecAfiliacion'],
                'EESS' => $result['EESS'],
                'DescEESS' => $result['DescEESS'],
                'EESSUbigeo' => $result['EESSUbigeo'],
                'DescEESSUbigeo' => $result['DescEESSUbigeo'],
                'Regimen' => $result['Regimen'],
                'TipoSeguro' => $result['TipoSeguro'],
                'DescTipoSeguro' => $result['DescTipoSeguro'],
                'Contrato' => $result['Contrato'],
                'FecCaducidad' => $result['FecCaducidad'],
                'Estado' => $result['Estado'],
                'Tabla' => $result['Tabla'],
                'IdNumReg' => $result['IdNumReg'],
                'Genero' => $result['Genero'],
                'FecNacimiento' => $result['FecNacimiento'],
                'IdUbigeo' => $result['IdUbigeo'],
                'Disa' => $result['Disa'],
                'TipoFormato' => $result['TipoFormato'],
                'NroContrato' => $result['NroContrato'],
                'Correlativo' => $result['Correlativo'],
                'IdPlan' => $result['IdPlan'],
                'IdGrupoPoblacional' => $result['IdGrupoPoblacional'],
                'MsgConfidencial' => 'Su consulta esta siendo registrada con su identificacion. Recuerde que usted es responsable por la confidencialidad de la informacion consultada, segun la Ley de Proteccion de Datos Personales.',
                'IdEmpleadoUltimaConsulta' => Auth::user()->IdEmpleado
            ];

            $afiliado = SisAfiliado::on('sqlsrvServicios')->where('TipoDocumento', $TipoDocumento)->where('NroDocumento', $NroDocumento)->first();

            if ($afiliado) {
                $afiliado->update($datos);
            } else {
                SisAfiliado::on('sqlsrvServicios')->create($datos);
            }
            /*SI LA CONSULTA A RENIEC NO SE PUDO PROCESAR POR ERROR DEL SERVICIO O SERVICIO INACTIVO
            LA CONSULTA AL SIS INSERTARÁ LOS DATOS DEL PACIENTE Y PERSONA*/
            if ($result['IdError'] != 14) {
                if (!$request->consultareniec) {
                    DB::update('EXEC WebS_Replicar_SIS_En_Pacientes @NumeroDocumento = ?, @TipoDocId = ? ', [$NroDocumento, $TipoDocumento]);
                }
            }
            /*SI LA CONSULTA A RENIEC NO SE PUDO PROCESAR POR ERROR DEL SERVICIO O SERVICIO INACTIVO
            LA CONSULTA AL SIS INSERTARÁ LOS DATOS DEL PACIENTE Y PERSONA*/

            // Utilizamos la función getErrorCatalog para obtener la descripción del error
            $descripcionError = self::getErrorCatalog($result['IdError']);

            if ($result['IdError'] == '0') {
                return [
                    'datos' => $datos,
                    'respuesta' => 1,
                    'descripcion' => 'CONSULTA SIS EXITOSA: ' . $descripcionError,
                    'codigo' => $res['code']
                ];
            } else {
                return [
                    'datos' => $datos,
                    'respuesta' => -1,
                    'descripcion' => 'ERROR DE CONSULTA SIS: ' . $descripcionError,
                    'codigo' => $res['code']
                ];
            }
        } catch (\SoapFault $e) {
            return [
                'datos' => null,
                'respuesta' => -1,
                'descripcion' => $e->getMessage(),
                'codigo' => -1
            ];
        } catch (\Exception $e) {
            return [
                'datos' => null,
                'respuesta' => -1,
                'descripcion' => $e->getMessage(),
                'codigo' => -1
            ];
        }
    }

    public function ws_consulta_SISDatosContingencia($NroDoc, $TipoDoc, $TipoDocSis, $consultareniec)
    {
        try {
            $parametros = [
                "tipo" => $TipoDocSis,
                "numero" => $NroDoc
            ];
            $res = (new SisContingenciaController())->consultarContingencia(request()->merge($parametros));

            if (!$res) {
                return [
                    'datos' => null,
                    'respuesta' => -1,
                    'descripcion' => 'No existen registros para los datos ingresados. Pruebe una Nueva Consulta',
                    'codigo' => 14
                ];
            }

            $result = $res->original['data'];
            // Elimina caracteres no deseados en la respuesta
            $search = ["'", "\"", "\\", "\n", "\r", "\t"];
            $replace = ["", "", "", "", "", ""];
            foreach ($result as $key => $value) {
                if (is_string($value)) {
                    $result[$key] = str_replace($search, $replace, $value);
                }
            }

            $paciente = Paciente::buscarPorDocumentoYTipoUpdate($NroDoc, $TipoDoc);
            if ($result['apellido_paterno']) {
                $paciente->ApellidoPaterno = $result['apellido_paterno'];
            }

            if ($result['apellido_materno']) {
                $paciente->ApellidoMaterno = $result['apellido_materno'];
            }
            $paciente->save();

            $datos = array_filter([
                'IdError' => $res->original['success'] == false ? '14' : '0',
                'Resultado' => $res->original['success'] == false ? 'NO SE ENCONTRO AFILIACION PARA EL DNI CONSULTADO' : 'DATOS EXITOSOS',
                'TipoDocumento' => $TipoDoc,
                'NroDocumento' => $NroDoc,
                'ApePaterno' => $result['apellido_paterno'] ?? null,
                'ApeMaterno' => $result['apellido_materno'] ?? null,
                'Nombres' => $result['nombres'] ?? null,
                'FecAfiliacion' => $result['fecha_de_afiliacion'] ? str_replace('-', '', $result['fecha_de_afiliacion']) : null,
                'EESSUbigeo' => (!empty($result['eess']) && strlen($result['eess']) >= 6) ? substr($result['eess'], 0, 6) : null,
                'DescEESSUbigeo' => $result['ubicacion_eess'] ?? null,
                'Contrato' => $result['numero_afiliacion'] ?? null,
                'Estado' => $res->original['success'] == false ? '' : 'ACTIVO',
                'TipoFormato' => $this->obtenerTipoFormatoSeguro($result['tipo_de_formato']),
                'IdPlan' => $this->obtenerTipoPlanSeguro($result['plan_de_beneficios']),
                'Regimen' => $this->obtenerRegimen($result['tipo_de_seguro']),
                'TipoSeguro' => $this->obtenerTipoFinanciamientoSeguro($result['tipo_de_seguro']),
                'DescTipoSeguro' => $result['tipo_de_seguro'] ?? null,
                'MsgConfidencial' => $res->original['success'] == false ? '' : 'Su consulta esta siendo registrada con su identificacion. Recuerde que usted es responsable por la confidencialidad de la informacion consultada, segun la Ley de Proteccion de Datos Personales.',
                'IdEmpleadoUltimaConsulta' => Auth::user()->IdEmpleado
            ], function ($value) {
                return $value !== null && $value !== '';
            });

            $idError = $res->original['success'] == false ? 14 : 0;

            $afiliado = SisAfiliado::on('sqlsrvServicios')
                ->where('TipoDocumento', $TipoDoc)
                ->where('NroDocumento', $NroDoc)
                ->first();
            if ($afiliado) {
                $afiliado->update($datos);
            } else {
                SisAfiliado::on('sqlsrvServicios')->create($datos);
            }

            /*SI LA CONSULTA A RENIEC NO SE PUDO PROCESAR POR ERROR DEL SERVICIO O SERVICIO INACTIVO LA CONSULTA AL SIS INSERTARÁ LOS DATOS DEL PACIENTE Y PERSONA*/
            if ($idError == 0) {
                if ($consultareniec == false) {
                    DB::update('EXEC WebS_Replicar_SIS_En_Pacientes @NumeroDocumento = ?, @TipoDocId = ? ', [$NroDoc, $TipoDoc]);
                }
            }
            /*SI LA CONSULTA A RENIEC NO SE PUDO PROCESAR POR ERROR DEL SERVICIO O SERVICIO INACTIVO LA CONSULTA AL SIS INSERTARÁ LOS DATOS DEL PACIENTE Y PERSONA*/
            // Utilizamos la función getErrorCatalog para obtener la descripción del error
            // $contingencia = 'S';

            //CUANDO ESTOS CAMPOS SON NULOS, SE TIENE QUE INDICAR QUE SI VIENE DE CONTINGENCIA, PARA MOSTRAR VENTANA DE SOLICITUD DE INGRESO DE ESTOS CAMPOS
            if (empty($persona->FechaNacimiento) || empty($persona->IdSexo)) {
                $contingencia = 'S';
            }


            $descripcionError = self::getErrorCatalog($idError);
            if ($idError == 0) {
                return [
                    'datos' => $datos,
                    'respuesta' => 1,
                    'contingencia' => $contingencia,
                    'descripcion' => 'CONSULTA SIS EXITOSA: ' . $descripcionError,
                    'codigo' => $idError
                ];
            } else {
                return [
                    'datos' => $datos,
                    'respuesta' => -1,
                    'contingencia' => $contingencia,
                    'descripcion' => 'ERROR DE CONSULTA SIS: ' . $descripcionError,
                    'codigo' => $idError
                ];
            }
        } catch (\SoapFault $e) {
            return [
                'datos' => null,
                'respuesta' => -1,
                'descripcion' => $e->getMessage(),
                'codigo' => -1
            ];
        } catch (\Exception $e) {
            return [
                'datos' => null,
                'respuesta' => -1,
                'descripcion' => $e->getMessage(),
                'codigo' => -1
            ];
        }
    }

    public function obtenerRegimen($descripcion)
    {
        $desc = strtolower($this->quitarTildes(trim($descripcion)));

        if (str_contains($desc, 'gratuito')) return '1';
        if (str_contains($desc, 'emprendedor')) return '2';
        if (str_contains($desc, 'para todos')) return '1';

        return null;
    }

    public function obtenerTipoFinanciamientoSeguro($descripcion)
    {
        $desc = strtolower($this->quitarTildes(trim($descripcion)));

        if (str_contains($desc, 'gratuito')) return '01';
        if (str_contains($desc, 'emprendedor')) return '04';
        if (str_contains($desc, 'para todos')) return '05';

        return null;
    }

    public function obtenerTipoPlanSeguro($descripcion)
    {
        $desc = strtolower($this->quitarTildes(trim($descripcion)));

        if (str_contains($desc, 'peas') && str_contains($desc, 'complementario')) return 1;
        if (str_contains($desc, 'lpis')) return 2;
        if ($desc === 'peas') return 3;
        if (str_contains($desc, 'rj') && str_contains($desc, '261')) return 4;

        return null;
    }


    public function obtenerTipoFormatoSeguro($descripcion)
    {
        $desc = strtolower($this->quitarTildes(trim($descripcion)));

        if (str_contains($desc, 'modalidad anterior')) return 1;
        if (str_contains($desc, 'inscripcion temporal')) return 2;
        if ($desc === 'afiliacion') return 3;
        if (str_contains($desc, 'inscripcion rn')) return 4;
        if (str_contains($desc, 'afiliacion directa temporal')) return 5;
        if (str_contains($desc, 'afiliacion directa')) return 6;

        return null;
    }


    public function quitarTildes($cadena)
    {
        $originales = ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ', 'á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'];
        $reemplazos = ['A', 'E', 'I', 'O', 'U', 'U', 'N', 'a', 'e', 'i', 'o', 'u', 'u', 'n'];
        return str_replace($originales, $reemplazos, $cadena);
    }
}
