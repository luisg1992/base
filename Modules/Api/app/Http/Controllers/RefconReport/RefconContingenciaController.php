<?php

namespace Modules\Api\Http\Controllers\RefconReport;

use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use ErrorException;
use Exception;
use App\Helpers\HttpHelper;

class RefconContingenciaController extends Controller
{

    public function historicoPacientesReferidosContingencia(Request $request)
    {
        $codigo_tipo_documento = $request->input('codigo_tipo_documento');
        $numero_documento = $request->input('numero_documento');

        $res_login = (new RefconReportLoginController())->login();
        if ($res_login['success']) {
            $res = [];
            try {
                $form_params = [
                    'idtipodoc' => $codigo_tipo_documento,
                    'numdoc' => $numero_documento,
                    'nrohis' => '',
                    'idestado' => -1,
                    'nomcomppac' => '',
                    'idespecialidad' => '',
                    'fechaini' => '', //$date_start,
                    'fechafin' => '', //$date_end,
                    'C' => 'REFERENCIA',
                    'S' => 'INGRESANTESHIST',
                    'idflag' => 'CP',
                    //                    'iduser' => '10685',
                    'page' => '1',
                    'start' => '0',
                    'limit' => 25,
                ];

                $query_string = http_build_query($form_params);

                $cookieJar = HttpHelper::restoreCookieJar($res_login['cookies']);
                $response = Http::withOptions([
                    'cookies' => $cookieJar,
                ])->get('https://refcon.minsa.gob.pe/refconv02/his/referencia?' . $query_string);

                if ($response->ok()) {
                    $rawBody = $response->body();
                    $encoding = mb_detect_encoding($rawBody, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
                    if ($encoding !== 'UTF-8') {
                        $rawBody = mb_convert_encoding($rawBody, 'UTF-8', $encoding);
                    }
                    $data = json_decode($rawBody, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if ($data['total'] == 0) {
                            return [
                                'success' => false,
                                'data' => 'No se encontraron resultados',
                            ];
                        }
                        return [
                            'success' => true,
                            'data' => $data['items']
                        ];
                    } else {
                        throw new Exception("Error al decodificar JSON: " . json_last_error_msg());
                    }
                }
                $response->throw();
            } catch (ConnectionException $e) {
                $res = [
                    'success' => false,
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ];
            } catch (RequestException $re) {
                $res = [
                    'success' => false,
                    'code' => $re->getCode(),
                    'message' => $re->getMessage(),
                ];
            } catch (ErrorException $ee) {
                $res = [
                    'success' => false,
                    'code' => $ee->getCode(),
                    'message' => $ee->getMessage(),
                ];
            }
            return $res;
        }
        return $res_login;
    }
}
