<?php

namespace Modules\Api\Http\Controllers\RefconReport;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Throwable;

class RefconReportController extends Controller
{

    public function obtenerPacienteRefcon(Request $request)
    {
        $codigoEstablecimiento = $request->input('codigo_establecimiento', '');
        $codigoTipoDocumento = $request->input('codigo_tipo_documento');
        $numeroDocumento = $request->input('numero_documento');

        $resLogin = (new RefconReportLoginController())->login();

        if (!$resLogin['success']) {
            return $resLogin;
        }

        try {
            $formParams = [
                //'_dc' => '1747842388247',
                'idtipodoc' => $codigoTipoDocumento,
                'numdoc' => $numeroDocumento,
                'nrohis' => '',
                'nomcomppac' => '',
                'idestablecimiento' => is_null($codigoEstablecimiento) ? '' : $codigoEstablecimiento,
                'C' => 'PACIENTE',
                'S' => 'GETLIST',
                'page' => '1',
                'start' => '0',
                'limit' => 25,
            ];

            $url = 'https://refcon.minsa.gob.pe/refconv02/his/paciente?' . http_build_query($formParams);

            $response = Http::withOptions([
                'cookies' => $resLogin['cookies'],
            ])
                ->connectTimeout(10)
                ->timeout(20)
                ->get($url);

            if ($response->ok()) {
                $utf8Body = mb_convert_encoding($response->body(), 'UTF-8', 'auto');
                $data = json_decode($utf8Body, true);
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
        } catch (ConnectionException | RequestException | Throwable $e) {
            return [
                'success' => false,
                'code' => $e->getCode(),
                'message' => 'Excepción: ' . $e->getMessage(),
            ];
        }
    }

    public function recibirPacienteRefcon(Request $request)
    {
        $codigoReferencia = $request->input('codigo_referencia');
        $resLogin = (new RefconReportLoginController())->login();
        if (!$resLogin['success']) {
            return $resLogin;
        }

        try {
            $formParams = [
                'C' => 'REFERENCIA',
                'S' => 'CONDICIONREF',
                'idreferencia' => $codigoReferencia,
                'idcondicion' => 'E',
                'caditems' => '',
                'fgllegada' => 'S',
            ];

            $response = Http::withOptions([
                'cookies' => $resLogin['cookies'],
            ])
                ->connectTimeout(5)
                ->timeout(10)
                ->asForm()
                ->post('https://refcon.minsa.gob.pe/refconv02/his/referencia', $formParams);

            if ($response->ok()) {
                return [
                    'success' => true,
                    'message' => 'Referencia actualizada correctamente',
                ];
            }

            return [
                'success' => false,
                'code' => $response->status(),
                'message' => 'Error en la solicitud HTTP.',
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }

    public function listarPacientesReferidos(Request $request)
    {
        $resLogin = (new RefconReportLoginController())->login();
        if (!$resLogin['success']) {
            return $resLogin;
        }

        $codigoTipoDocumento = $request->input('codigo_tipo_documento');
        $numeroDeDocumento = $request->input('numero_de_documento', '');
        $nombreDelPaciente = $request->input('nombre_del_paciente', '');
        $numeroDeHistoriaClinica = $request->input('numero_de_historia_clinica', '');
        $codigoDeEspecialidad = $request->input('codigo_de_especialidad', '');
        $estado = $request->input('estado', '-1');

        $fechaDesde = $request->input('fecha_desde')
            ? Carbon::createFromFormat('Y-m-d', $request->input('fecha_desde'))->format('d/m/Y')
            : Carbon::now()->subMonth()->format('d/m/Y');

        $fechaHasta = $request->input('fecha_hasta')
            ? Carbon::createFromFormat('Y-m-d', $request->input('fecha_hasta'))->format('d/m/Y')
            : now()->format('d/m/Y');

        try {
            $formParams = [
                'idtipodoc' => $codigoTipoDocumento,
                'numdoc' => $numeroDeDocumento,
                'nrohis' => $numeroDeHistoriaClinica,
                'idestado' => $estado,
                'nomcomppac' => $nombreDelPaciente,
                'idespecialidad' => $codigoDeEspecialidad,
                'fechaini' => $fechaDesde,
                'fechafin' => $fechaHasta,
                'C' => 'REFERENCIA',
                'S' => 'INGRESANTES',
                'idflag' => 'CP',
                'page' => '1',
                'start' => '0',
                'limit' => 100,
            ];

            $response = Http::withOptions([
                'cookies' => $resLogin['cookies'],
            ])
                ->connectTimeout(5)
                ->timeout(10)
                ->get('https://refcon.minsa.gob.pe/refconv02/his/referencia?' . http_build_query($formParams));

            if ($response->ok()) {
                $rawBody = $response->body();
                $encoding = mb_detect_encoding($rawBody, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
                if ($encoding !== 'UTF-8') {
                    $rawBody = mb_convert_encoding($rawBody, 'UTF-8', $encoding);
                }

                $data = json_decode($rawBody, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (!isset($data['total']) || $data['total'] == 0) {
                        return [
                            'success' => false,
                            'data' => 'No se encontraron resultados',
                        ];
                    }

                    return [
                        'success' => true,
                        'data' => $data,
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Error al decodificar JSON: ' . json_last_error_msg(),
                ];
            }

            return [
                'success' => false,
                'code' => $response->status(),
                'message' => 'Error en la solicitud HTTP.',
            ];
        } catch (ConnectionException | RequestException | Throwable $e) {
            return [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    } 
}
