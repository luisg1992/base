<?php

namespace Modules\Api\Http\Controllers\Refcon;

use App\Helpers\HttpHelper;
use App\Http\Controllers\Controller;
use ErrorException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Modules\Api\Http\Controllers\RefconReport\RefconReportLoginController;

class RecibirPacienteRefcon extends Controller
{
    public function recibirPacienteRefcon(Request $request)
    {
        $IdReferenciaRefCon = $request->input('IdReferenciaRefCon');
        $res_login = (new RefconReportLoginController())->login();
        if ($res_login['success']) {
            $res = [];
            try {
                $form_params = [
                    'C' => 'REFERENCIA',
                    'S' => 'CONDICIONREF',
                    'idreferencia' => $IdReferenciaRefCon,
                    'idcondicion' => 'E',
                    'caditems' => '',
                    'fgllegada' => 'S',
                ];

                $cookieJar = HttpHelper::restoreCookieJar($res_login['cookies']);
                $response = Http::withOptions([
                    'cookies' => $cookieJar,
                ])  
                    ->get('https://refcon.minsa.gob.pe/refconv02/his/referencia', $form_params);

                if ($response->ok()) {
                    return [
                        'success' => true,
                        'message' => 'Referencia actualizada correctamente',
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
