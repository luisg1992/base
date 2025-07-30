<?php

namespace App\Http\Controllers\Api\sigesa;

use ErrorException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

class HISMINSAController extends Controller
{
    public function send(Request $request)
    {
        $res = [];
        try {
            $tipo_desarrollo = config('app.env'); 
            $type = 'dev';
            if ($tipo_desarrollo == 'production') {
                $type = 'prod';
            }
            $subdomain = $type === 'dev' ? 'dpidesalud.minsa.gob.pe:18080' : 'pidesalud.minsa.gob.pe:18061';
         
            $url = "http://{$subdomain}/mcs-sihce-hisminsa/integracion/v1.0/paquete/actualizar";
            $data =  $request->JSONresponse;

            $response = Http::post($url, $data);

            if ($response->ok()) {
                $res = $response->json();
                $res['success'] = true;
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
}
