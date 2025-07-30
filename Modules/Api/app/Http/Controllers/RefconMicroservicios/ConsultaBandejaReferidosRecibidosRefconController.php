<?php

namespace Modules\Api\Http\Controllers\RefconMicroservicios;

use Exception;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class ConsultaBandejaReferidosRecibidosRefconController extends Controller
{
    public function obtenerRegistrosReferencias(Request $request)
    {
        $FechaInput = formatear_fecha($request->input('FechaFiltro'));
        $FechaFiltro = Carbon::parse($FechaInput)->format('Ymd');
        $data = [
            "codUnicoDestino" => "6206",
            "fechaFin" => $FechaFiltro,
            "fechaInicio" => $FechaFiltro,
            "pagina" => 1,
            "limite" => 50
        ];
        $registros = [];
        $error = false;
        $res = $this->consultar($data)->getData(true);
        if ($res['success']) {
            $registros = $res['datos']['datos'];
            $datos = $res['datos'];
            for ($i = 2; $i <= $datos['paginas']; $i++) {
                sleep(2);
                $data['pagina'] = $i;
                $res2 = $this->consultar($data)->getData(true);
                if ($res2['success']) {
                    $registros = array_merge($registros, $res2['datos']['datos']);
                } else {
                    $error = true;
                    break;
                }
            }
        } else {
            $error = true;
        }

        if ($error) {
            return [
                'success' => false,
                'message' => $res['message'] == 'No existe registros' ? 'LA FECHA SELECCIONADA NO CUENTA CON REFERENCIAS ACEPTADAS, PORF AVOR CONSULTE UNA FECHA DIFERENTE' : 'No existe registros',
            ];
        }

        return [
            'success' => true,
            'registros' => $registros
        ];
    }

    public function consultar($data)
    {
        try {
            $url = "https://servicios.minsa.gob.pe/mcs-servicios-refcon/servicio/v1.0.0/consultaBandejaReferidosRecibidos";
            $response = Http::withHeaders([
                'username' => config('services.mefMicro_Produccion.username'),
                'password' => config('services.mefMicro_Produccion.password'),
                'ipclient' => config('services.mefMicro_Produccion.ipclient'),
            ])->post($url, $data);

            $response->throw();

            $res = $response->json();
            if ($res['codigo'] === '0000') {
                return response()->json([
                    'success' => true,
                    'datos' => $res['datos']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $res['mensaje'],
                'data' => $response->json()
            ]);
        } catch (ConnectionException | RequestException | ErrorException | Exception $e) {
            return response()->json([
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
