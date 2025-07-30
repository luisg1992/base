<?php

namespace Modules\Api\Http\Controllers\RefconReport;

use App\Helpers\HttpHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use ErrorException;

class RefconPreviewPDFController extends Controller
{
    public function visualizarReferenciaRefcon(Request $request)
    {
        $res_login = (new RefconReportLoginController())->login();
        if ($res_login['success']) {
            $res = [];
            try {
                $idestablecimiento = $request->input('idestablecimiento');
                $idreferencia = $request->input('idreferencia');
                $estadoreferencia = $request->input('estadoreferencia');

                if ($estadoreferencia === 'rechazado' || $estadoreferencia === 'RECHAZADO') {
                    $reportname = '../his/reports/referencia-rechazado';
                } elseif ($estadoreferencia === 'pendiente' || $estadoreferencia === 'PENDIENTE') {
                    $reportname = '../his/reports/referencia-pendiente';
                } else {
                    $reportname = '../his/reports/referencia';
                }

                $form_params = [
                    'outputtype' => 'PDF',
                    'reportname' => $reportname,
                    'paramstoreport' => '{"idreferencia":' . $idreferencia . ',"idestablecimiento":' . $idestablecimiento . '}',
                ];

                $cookieJar = HttpHelper::restoreCookieJar($res_login['cookies']);
                $response = Http::withOptions([
                    'cookies' => $cookieJar,
                ])
                    ->asForm()
                    ->post('https://refcon.minsa.gob.pe/refconv02/reports', $form_params);

                if ($response->ok()) {
                    $header = json_decode($response->header('X-JSON'), true);
                    $urlFile = $header['urlFile'];
                    $url_file_array = explode('-', $urlFile);
                    if (count($url_file_array) > 2 && $url_file_array[count($url_file_array) - 2] != '0') {
                        $urlFile = 'https://refcon.minsa.gob.pe/refconv02/reports?C=DL&f=' . $urlFile;
                        $client = new Client();
                        $responseCliente = $client->get($urlFile);
                        return response()->json([
                            'success' => true,
                            'respuesta' => base64_encode($responseCliente->getBody())
                        ]);
                    }
                    return [
                        'success' => false,
                        'message' => 'No fue posible obtener la url del reporte'
                    ];
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
