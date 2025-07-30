<?php

namespace Modules\Api\Http\Controllers\Refcon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use ErrorException;
use App\Helpers\HttpHelper;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Modules\Reporte\Exports\RefConReferenciasExport;
use Modules\Api\Http\Controllers\RefconReport\RefconReportLoginController;

class ListarPacienteReferidosController extends Controller
{

    public function RefConListarPacienteReferidosExportar(Request $request)
    {
        $respuesta = $this->RefConListarPacienteReferidos($request);

        if (!is_array($respuesta) || !$respuesta['success']) {
            abort(404, 'No se pudieron obtener los datos para exportar.');
        }

        $registros = $respuesta['data'];
        return  Excel::download(
            new  RefConReferenciasExport($registros),
            'referencias_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function RefConListarPacienteReferidos(Request $request)
    {
        $codigo_tipo_documento = $request->input('codigo_tipo_documento', '1');
        $numero_de_documento = $request->input('numero_de_documento', '');
        $nombre_del_paciente = $request->input('nombre_del_paciente', '');
        $numero_de_historia_clinica = $request->input('numero_de_historia_clinica', '');
        $codigo_de_especialidad = $request->input('codigo_de_especialidad', '');
        $estado = $request->input('estado', '-1');
        $fecha_desde = $request->input('FechaDesde');
        $fecha_hasta = $request->input('FechaHasta', $fecha_desde);

        $resLogin = (new RefconReportLoginController())->login();
        if (!$resLogin['success']) {
            return $resLogin;
        }

        $res = [];
        try {
            $formParams = [ 
                'idtipodoc' => $codigo_tipo_documento,
                'numdoc' => $numero_de_documento,
                'nrohis' => $numero_de_historia_clinica,
                'idestado' => $estado,
                'nomcomppac' => $nombre_del_paciente,
                'idespecialidad' => $codigo_de_especialidad,
                'fechaini' => $fecha_desde,
                'fechafin' => $fecha_hasta,
                'C' => 'REFERENCIA',
                'S' => 'INGRESANTESHIST',
                'idflag' => 'CP', 
                'page' => '1',
                'start' => '0',
                'limit' => 8000,
            ];

            $cookieJar = HttpHelper::restoreCookieJar($resLogin['cookies']);
            $response = Http::withOptions([
                'cookies' => $cookieJar,
            ])
                ->asForm()
                ->post('https://refcon.minsa.gob.pe/refconv02/his/referencia?', $formParams);


            if ($response->ok()) {
                $rawBody = $response->body();
                $encoding = mb_detect_encoding($rawBody, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
                if ($encoding !== 'UTF-8') {
                    $rawBody = mb_convert_encoding($rawBody, 'UTF-8', $encoding);
                }
                $data = json_decode($rawBody, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (isset($data['total']) && $data['total'] == 0) {
                        return [
                            'success' => false,
                            'data' => 'No se encontraron resultados',
                        ];
                    }
                    return [
                        'success' => true,
                        'data' => $data['items'] ?? [],
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Error al decodificar JSON: ' . json_last_error_msg(),
                    ];
                }
            }

            return [
                'success' => false,
                'code' => $response->status(),
                'message' => 'Error en la solicitud al servicio externo.',
            ];
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
