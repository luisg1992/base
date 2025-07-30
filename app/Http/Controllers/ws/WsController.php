<?php

namespace App\Http\Controllers\ws;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\SisAfiliado;
use Modules\Persona\Models\Paciente;

class WsController extends Controller
{
    private $usoWs;
    private function setInstitucionYUsoWs()
    {
        $this->usoWs = DB::select('EXEC WebS_ParametrosAccesoServicosWeb');
    }

    public function obtenerDatosFiliacionSis(Request $request)
    {
        $wsSIS = new \App\Ws\SIS;
        $datosSis = $wsSIS->ws_consulta_SISDatos($request, "");
        $data = [
            'usoWs' => 'S',
            'contingencia' => 'N',
            'pri_cgarantia' => 0,
            'respuesta' => -1,
            'descripcion' => $datosSis['descripcion'] ?? 'Sin descripción',
            'codigo' => $datosSis['codigo'] ?? '9999',
            'data' => null,
        ];

        if ($datosSis['datos']) {
            if ($datosSis && isset($datosSis['datos']['NroDocumento'], $datosSis['datos']['TipoDocumento'])) {
                $paciente = Paciente::buscarPorDocumentoYTipo($datosSis['datos']['NroDocumento'], $datosSis['datos']['TipoDocumento']);
                if ($paciente) {
                    $data['data'] = $paciente;
                    $data['respuesta'] = $datosSis['respuesta'] ?? 1;
                }
            }
        }
        return $data;
    }

    public function obtenerDatosSisCompletos(Request $request)
    {
        $this->setInstitucionYUsoWs();
        if ($this->usoWs[0]->SIS == 'S') {
            try {
                $TiposDocIdentidad = (object) collect(cache_persona_tipo_documentos_identidad())->firstWhere('id', $request->tipodocumento);
                $WsSIS = new \App\Ws\SIS;
                $datos_sis = $WsSIS->ws_consulta_SISDatos($request, $TiposDocIdentidad->CodigoSIS);

                if ($datos_sis['respuesta'] == -1) {
                    $data['response'] = null;
                    $data['respuesta'] = -1;
                    $data['usoWs'] = 'S';
                    $data['contingencia'] = 'N';
                    $data['descripcion'] = $datos_sis['descripcion'];
                    $data['codigo'] = $datos_sis['codigo'];
                    $data['pri_cgarantia'] = 0;
                    return ($data);
                } else {

                    if ($request->consultareniec == false) {
                        $data['data'] =  Paciente::buscarPorDocumentoYTipo($request->numerodocumento, $request->tipodocumento);
                    } else {
                        $data['data'] = $datos_sis['datos'];
                    }

                    $data['respuesta'] = $datos_sis['respuesta'];
                    $data['usoWs'] = 'S';
                    $data['contingencia'] = 'N';
                    $data['descripcion'] = $datos_sis['descripcion'];
                    $data['codigo'] = $datos_sis['codigo'];
                    $data['pri_cgarantia'] = 0;
                    return ($data);
                }
            } catch (\Throwable $th) {
                $data['response'] = null;
                $data['respuesta'] = -1;
                $data['usoWs'] = 'S';
                $data['contingencia'] = 'N';
                $data['descripcion'] = $th->getMessage();
                $data['codigo'] = -1;
                $data['pri_cgarantia'] = 0;
                return ($data);
            }
        } else if ($this->usoWs[0]->SIS == 'N') {
            try {
                /*VERIFICAMOS SI EXISTE COMO PACIENTE CONTINUADOR*/
                $afiliado = SisAfiliado::on('sqlsrvServicios')
                    ->where('TipoDocumento', $request->tipodocumento)
                    ->where('NroDocumento', $request->numerodocumento)
                    ->first();
                if ($afiliado) {
                    $data['data'] = $afiliado;
                    $data['respuesta'] = 1;
                    $data['usoWs'] = 'S';
                    $data['contingencia'] = 'N';
                    $data['descripcion'] = 'CONSULTA SIS EXITOSA PACIENTE CONTINUADOR';
                    $data['codigo'] = 14;
                    $data['pri_cgarantia'] = 0;
                    return ($data);
                } else {
                    $TiposDocIdentidad = (object) collect(cache_persona_tipo_documentos_identidad())->firstWhere('id', $request->tipodocumento);
                    $WsSIS = new \App\Ws\SIS;
                    $datos_sis = $WsSIS->ws_consulta_SISDatosContingencia($request->numerodocumento, $request->tipodocumento, $TiposDocIdentidad->CodigoSIS, $request->consultareniec);

                    if ($datos_sis['respuesta'] == -1) {
                        $data['response'] = null;
                        $data['respuesta'] = -1;
                        $data['usoWs'] = 'S';
                        $data['contingencia'] = 'N';
                        $data['descripcion'] = $datos_sis['descripcion'];
                        $data['codigo'] = $datos_sis['codigo'];
                        $data['pri_cgarantia'] = 0;
                        return ($data);
                    } else {

                        if ($request->consultareniec == false) {
                            $data['data'] =  Paciente::buscarPorDocumentoYTipo($request->numerodocumento, $request->tipodocumento);
                        } else {
                            $data['data'] = $datos_sis['datos'];
                        }

                        $data['respuesta'] = $datos_sis['respuesta'];
                        $data['usoWs'] = 'S';
                        $data['contingencia'] = $datos_sis['contingencia'];
                        $data['descripcion'] = $datos_sis['descripcion'];
                        $data['codigo'] = $datos_sis['codigo'];
                        $data['pri_cgarantia'] = 0;
                        return ($data);
                    }
                }
            } catch (\Throwable $th) {
                $data['response'] = null;
                $data['respuesta'] = -1;
                $data['usoWs'] = 'S';
                $data['contingencia'] = 'N';
                $data['descripcion'] = '';
                $data['codigo'] = -1;
                $data['pri_cgarantia'] = 0;
                return ($data);
            }
        }
    }


    public function obtenerDatosReniecCompletos(Request $request)
    {
        $this->setInstitucionYUsoWs();
        $data = [
            'datos' => null,
            'respuesta' => -1,
            'usoWs' => $this->usoWs[0]->RENIEC == 'S' ? 'S' : 'N',
            'codigo' => -1,
            'mensajeReniec' => '',
        ];

        if ($this->usoWs[0]->RENIEC !== 'S') {
            $data['mensajeReniec'] = 'SERVICIO DE RENIEC INACTIVO';
            return $data;
        }

        try {
            $WsReniec = new \App\Ws\Reniec;
            $datos_reniec = $WsReniec->ws_consulta_reniecDatosCompletos($request);

            if ($datos_reniec['respuesta'] == -1) {
                $data['codigo'] = $datos_reniec['codigo'];
                $data['mensajeReniec'] = $datos_reniec['mensajeReniec'];
            } else {
                $data['datos'] = $datos_reniec['datos'];
                $data['respuesta'] = 1;
                $data['codigo'] = $datos_reniec['codigo'];
                $data['mensajeReniec'] = $datos_reniec['descripcion'];
            }
        } catch (\Throwable $th) {
            $data['mensajeReniec'] = $th->getMessage();
            $data['usoWs'] = 'N';
        }

        return $data;
    }


    // public function obtenerDatosReniecBasicos(Request $request)
    // {
    //     $this->setInstitucionYUsoWs();
    //     if ($this->usoWs[0]->RENIEC == 'S') {
    //         try {
    //             $WsReniec = new \App\Ws\Reniec;
    //             $datos_reniec = $WsReniec->ws_consulta_reniecDatosBasicos($request->dni);
    //             if ($datos_reniec['respuesta'] == -1) {
    //                 $data['datos'] = null;
    //                 $data['respuesta'] = -1;
    //                 $data['usoWs'] = 'S';
    //                 $data['descripcion'] = $datos_reniec['descripcion'];
    //                 $data['codigo'] = $datos_reniec['codigo'];
    //                 return ($data);
    //             } else {

    //                 $data['datos'] = $datos_reniec['datos'];
    //                 $data['respuesta'] = 1;
    //                 $data['usoWs'] = 'S';
    //                 $data['descripcion'] = $datos_reniec['descripcion'];
    //                 $data['codigo'] = $datos_reniec['codigo'];
    //                 return ($data);
    //             }
    //         } catch (\Throwable $th) {
    //             $data['datos'] = null;
    //             $data['respuesta'] = -1;
    //             $data['descripcion'] = $th->getMessage();
    //             $data['codigo'] = -1;
    //             return ($data);
    //         }
    //     } else {
    //         $data['datos'] = null;
    //         $data['respuesta'] = -1;
    //         $data['usoWs'] = 'N';
    //         $data['descripcion'] = 'SERVICIO DE RENIEC INACTIVO';
    //         $data['codigo'] = -1;
    //         return ($data);
    //     }
    // }


    // public function validar_existencia_carpetas($dni)
    // {
    //     $carpetaPrincipal = 'patient';
    //     $carpeta = public_path($carpetaPrincipal);
    //     if (!File::exists($carpeta)) {
    //         File::makeDirectory($carpeta, 0777, true, true);
    //     }

    //     $carpetaPaciente = public_path($carpetaPrincipal . '/' . $dni);
    //     if (!File::exists($carpetaPaciente)) {
    //         File::makeDirectory($carpetaPaciente, 0777, true, true);
    //     }

    //     $signature = public_path($carpetaPrincipal . '/' . $dni . '/signature');
    //     if (!File::exists($signature)) {
    //         File::makeDirectory($signature, 0777, true, true);
    //     }

    //     $photo = public_path($carpetaPrincipal . '/' . $dni . '/photo');
    //     if (!File::exists($photo)) {
    //         File::makeDirectory($photo, 0777, true, true);
    //     }
    // }

    // public function guardar_imagenes($dni, $imagenFirmaBase64, $tipo)
    // {
    //     $carpetaPrincipal = 'patient';
    //     if ($imagenFirmaBase64) {
    //         $signatureData = base64_decode($imagenFirmaBase64);
    //         $signaturePath = public_path($carpetaPrincipal . '/' . $dni . '/' . $tipo . '/' . $dni . '.jpg');
    //         File::put($signaturePath, $signatureData);
    //     }
    // } 
}
