<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\sigesa\HISMINSAController; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Modules\Api\Http\Controllers\Refcon\ApiRefconController;

class ApiHisMinsaController extends Controller
{ 

    public function webListarPendientesEnvioHis(Request $request)
    {
        try {
            $Turno = -100; 
            if ($request->Turno  == '-' || $request->Turno  == '') {
                $Turno = -100;
            } else {
                $Turno = $request->Turno ;
            } 
            $FechaFiltroCarbon = Carbon::createFromFormat('d/m/Y', $request->Fecha);
            $FechaFiltro = $FechaFiltroCarbon->format('Y-m-d');
            $ListarPendientesEnvioHis = "EXEC webListarPendientesEnvioHis " . Auth::user()->IdEmpleado . ",'" . $FechaFiltro . "','" . $Turno . "'";
            $HisMinsaJSONItem = DB::select($ListarPendientesEnvioHis);
            $res = json_encode($HisMinsaJSONItem);
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'codRespuesta' => -1,
                'message' => $ee->getMessage(),
            ];
        }
        return $res;
    }

    public function generarTrama_saveHisMinsa(Request $request)
    {
        $executeHisMinsaJSONItem = "EXEC webEnvioTramaHis " . $request->IdCuentaAtencion;
        $HisMinsaJSONItem = DB::select($executeHisMinsaJSONItem);
        $HisMinsaJSONCabecera = null;
        if ($HisMinsaJSONItem) {
            $HisMinsaJSONCabecera = $HisMinsaJSONItem[0];
        }
        $items = [];
        if ($HisMinsaJSONItem) {
            $val = 0;
            foreach ($HisMinsaJSONItem as $item) {
                $labs1[] = null;
                $labs2[] = null;
                $labs3[] = null;
                $HisMinsaJSONCabeceraFOR = $HisMinsaJSONItem[$val++];

                $vali_codigolab1 = str_replace(' ', '', $HisMinsaJSONCabeceraFOR->codigolab1);
                $vali_valor1 = str_replace(' ', '', $HisMinsaJSONCabeceraFOR->valor1);

                if ((strlen($vali_codigolab1) == 0) || $vali_codigolab1 == 47) {
                    $vali_codigolab1 = 0;
                }

                if ((strlen($vali_valor1) == 0) || $vali_valor1 == '' || $vali_valor1 == '---') {
                    $vali_valor1 = 0;
                }
                $labs1 = array(
                    "codigo" => $vali_codigolab1,
                    "valor" => $vali_valor1
                );

                $vali_codigolab2 = str_replace(' ', '', $HisMinsaJSONCabeceraFOR->codigolab2);
                $vali_valor2 = str_replace(' ', '', $HisMinsaJSONCabeceraFOR->valor2);
                if ((strlen($vali_codigolab2) == 0) || $vali_codigolab2 == 47) {
                    $vali_codigolab2 = 0;
                }

                if ((strlen($vali_valor2) == 0) || $vali_valor2 == '' || $vali_valor2 == '---') {
                    $vali_valor2 = 0;
                }
                $labs2 = array(
                    "codigo" => $vali_codigolab2,
                    "valor" => $vali_valor2
                );

                $vali_codigolab3 = str_replace(' ', '', $HisMinsaJSONCabeceraFOR->codigolab3);
                $vali_valor3 =  str_replace(' ', '', $HisMinsaJSONCabeceraFOR->valor3);
                if ((strlen($vali_codigolab3) == 0) || $vali_codigolab3 == 47) {
                    $vali_codigolab3 = 0;
                }

                if ((strlen($vali_valor3) == 0) || $vali_valor3 == '' || $vali_valor3 == '---') {
                    $vali_valor3 = 0;
                }
                $labs3 = array(
                    "codigo" => $vali_codigolab3,
                    "valor" => $vali_valor3
                );

                $items[] = array(
                    "labs" => array($labs1, $labs2, $labs3),
                    "tipodiagnostico" => trim($item->tipodiagnostico),
                    "codigo" => trim($item->codigo),
                    "tipoitem" => trim($item->tipoitem)
                );
            }
        } else {
            $items[] = array();
        }

        $array = array(
            "cita" => array(
                "numeroafiliacion" => $HisMinsaJSONCabecera->numeroafiliacion,
                "fechaatencion" => $HisMinsaJSONCabecera->fechaatencion,
                "estadoregistro" => $HisMinsaJSONCabecera->estadoregistro,
                "items" => $items,
                "idups" => $HisMinsaJSONCabecera->idups,
                "idestablecimiento" => $HisMinsaJSONCabecera->idestablecimiento,
                "diaedad" => ($HisMinsaJSONCabecera->diaedad !== null) ? intval($HisMinsaJSONCabecera->diaedad) : 0,
                "edadregistro" => $HisMinsaJSONCabecera->edadregistro,
                "idturno" => trim($HisMinsaJSONCabecera->idturno),
                "idtipedadregistro" => trim($HisMinsaJSONCabecera->idtipedadregistro),
                "fgdiag" => ($HisMinsaJSONCabecera->fgdiag !== null) ? intval($HisMinsaJSONCabecera->fgdiag) : 0,
                "mesedad" => ($HisMinsaJSONCabecera->mesedad !== null) ? intval($HisMinsaJSONCabecera->mesedad) : 0,
                "componente" => $HisMinsaJSONCabecera->componente,
                "idfinanciador" => $HisMinsaJSONCabecera->idfinanciador,
                "annioedad" => ($HisMinsaJSONCabecera->annioedad !== null) ? intval($HisMinsaJSONCabecera->annioedad) : 0,
                "examenfisico" => array(
                    "peso" => $HisMinsaJSONCabecera->peso,
                    "talla" => $HisMinsaJSONCabecera->talla,
                    "hemoglobina" => $HisMinsaJSONCabecera->hemoglobina,
                    "perimetrocefalico" => $HisMinsaJSONCabecera->perimetrocefalico,
                    "perimetroabdominal" => $HisMinsaJSONCabecera->perimetroabdominal,
                ),
            ),

            "personal_registra" => array(
                "nrodocumento" => trim($HisMinsaJSONCabecera->nrodocumento),
                "apematerno" => $HisMinsaJSONCabecera->apematerno,
                "idpais" => $HisMinsaJSONCabecera->idpais,
                "idprofesion" => $HisMinsaJSONCabecera->idprofesion,
                "fechanacimiento" => $HisMinsaJSONCabecera->fechanacimiento,
                "nombres" => $HisMinsaJSONCabecera->nombres,
                "idtipodoc" => $HisMinsaJSONCabecera->idtipodoc,
                "apepaterno" => $HisMinsaJSONCabecera->apepaterno,
                "idsexo" => $HisMinsaJSONCabecera->idsexo,
                "idcondicion" => $HisMinsaJSONCabecera->idcondicion
            ),

            "personal_atiende" => array(
                "nrodocumento" => trim($HisMinsaJSONCabecera->nrodocumentoatiende),
                "apematerno" => $HisMinsaJSONCabecera->apematernoatiende,
                "idpais" => $HisMinsaJSONCabecera->idpaisatiende,
                "idprofesion" => $HisMinsaJSONCabecera->idprofesionatiende,
                "fechanacimiento" => $HisMinsaJSONCabecera->fechanacimientoatiende,
                "nombres" => $HisMinsaJSONCabecera->nombresatiende,
                "idtipodoc" => $HisMinsaJSONCabecera->idtipodocatiende,
                "apepaterno" => $HisMinsaJSONCabecera->apepaternoatiende,
                "idsexo" => $HisMinsaJSONCabecera->idsexoatiende,
                "idcondicion" => $HisMinsaJSONCabecera->idcondicionatiende
            ),

            "paciente" => array(
                "nrodocumento" => trim($HisMinsaJSONCabecera->nrodocumentopcte),
                "apematerno" => $HisMinsaJSONCabecera->apematernopcte,
                "idflag" => $HisMinsaJSONCabecera->idflag,
                "nombres" => $HisMinsaJSONCabecera->nombrespcte,
                "nrohistoriaclinica" => trim($HisMinsaJSONCabecera->nrohistoriaclinicapcte),
                "idtipodoc" => $HisMinsaJSONCabecera->idtipodocpcte,
                "apepaterno" => $HisMinsaJSONCabecera->apepaternopcte,
                "idetnia" => $HisMinsaJSONCabecera->idetniapcte,
                "fechanacimiento" => $HisMinsaJSONCabecera->fechanacimientopcte,
                "idestablecimiento" => $HisMinsaJSONCabecera->idestablecimientopcte,
                "idpais" => $HisMinsaJSONCabecera->idpaispcte,
                "idsexo" => $HisMinsaJSONCabecera->idsexopcte
            ),
        );

        $executeActualizarEnvioHis = 'EXEC webActualizarEnvioHis ' . $request->IdCuentaAtencion;
        DB::update($executeActualizarEnvioHis);
        return json_encode($array);
    }

    // public function hisminsa_paquete_actualizar(Request $request)
    // {
    //     $url = '';
    //     try {
    //         $form = ["IdCuentaAtencion" => $request->IdCuentaAtencion];
    //         $JSONresponse = $this->generarTrama_saveHisMinsa(request()->merge($form));

    //         $JSONresponseForm = ["JSONresponse" => json_decode($JSONresponse, true)];
    //         $refcon = new HISMINSAController();
    //         $response = $refcon->send(request()->merge($JSONresponseForm));
    //         if ($response["success"]) {
    //             $executeActualizarRespuestaHi = 'EXEC webActualizarRespuestaHis ' . $request->IdCuentaAtencion . ', ' . $response['idCita'] . ', "NULL"';
    //             DB::update($executeActualizarRespuestaHi);

    //             //REALIZAREMOS EL ENVIO DE LAS TRAMAS DE REFCON PARA INFORMAR DE ASISTENCIA DEL PACIENTE  
    //             //REALIZAREMOS EL ENVIO DE LAS TRAMAS DE REFCON PARA INFORMAR DE ASISTENCIA DEL PACIENTE

    //             $res = [
    //                 'success' => true,
    //                 'codRespuesta' => 1,
    //                 'URLRespuesta' =>  $url,
    //                 'estado' =>  $response['estado'],
    //                 'message' =>  $response['descripcion'],
    //                 'idCita' =>  $response['idCita'],
    //                 // 'dataRecibirReferencia' =>  $res
    //             ];
    //         } else {
    //             //$executeActualizarRespuestaHi = 'EXEC webActualizarRespuestaHis ' . $request->IdCuentaAtencion . ', ' . $response['code'] . ', ' . $response['message'];
    //             $res = [
    //                 'success' => true,
    //                 'codRespuesta' => -1,
    //                 'URLRespuesta' =>  $url,
    //                 'estado' =>  'ERROR',
    //                 'idCita' => $response['code'],
    //                 'message' =>  $response['message']
    //             ];
    //         }
    //     } catch (ConnectionException $e) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'URLRespuesta' =>  $url,
    //             'try' =>  'ConnectionException',
    //             'message' => $e->getMessage(),
    //             'estado' =>  'ERROR',
    //             'idCita' => '',
    //         ];
    //     } catch (RequestException $re) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'URLRespuesta' =>  $url,
    //             'try' =>  'RequestException',
    //             'message' => $re->getMessage(),
    //             'estado' =>  'ERROR',
    //             'idCita' => '',
    //         ];
    //     } catch (ErrorException $ee) {
    //         $res = [
    //             'success' => false,
    //             'codRespuesta' => -2,
    //             'URLRespuesta' =>  $url,
    //             'try' =>  'ErrorException',
    //             'message' => $ee->getMessage(),
    //             'estado' =>  'ERROR',
    //             'idCita' => '',
    //         ];
    //     }
    //     return $res;
    // }
}
