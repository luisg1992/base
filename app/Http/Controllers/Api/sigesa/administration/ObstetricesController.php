<?php

namespace App\Http\Controllers\Api\sigesa\administration;

use DiDom\Document;
use ErrorException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;

class ObstetricesController extends Controller
{
    private $apiConfig;

    public function __construct()
    {
        $this->middleware('auth');
        $this->apiConfig = DB::select('EXEC WebS_ParametrosApiMostrarUrlToken')[0] ?? null;
    }

    public function searchByName(Request $request)
    {

        $ApePaterno = $request->input('ApePaterno');
        $ApeMaterno = $request->input('ApeMaterno');
        $Nombres = $request->input('Nombres');

        if (is_null($ApePaterno) && is_null($ApeMaterno) && is_null($Nombres)) {
            return [
                'success' => false,
                'message' => 'INGRESE LOS NOMBRES Y APELLIDOS DEL MÉDICO.',
                'tiporespuesta' => 0
            ];
        }

        $timeout = 10;

        // Datos en formato JSON
        $data = [
            "apellido_paterno" => $ApePaterno,
            "apellido_materno" => $ApeMaterno,
            "nombres" => $Nombres
        ];

        try {
            $response = Http::withToken($this->apiConfig->token)
                ->timeout($timeout)
                ->post("{$this->apiConfig->url}/api/obstetra", $data);

            if ($response->ok()) {
                $res = $response->json();
                $obstetra = [];

                if (isset($res['code']) && $res['code'] == 504) {
                    $res = [
                        'success' => false,
                        'message' => 'NO SE PUDO CONECTAR CON EL SERVICIO DE CONSULTA DEL COLEGIO OBSTETRAS DEL PERÚ.',
                        'tiporespuesta' => -1
                    ];
                } else {

                    if (isset($res['data']) && !empty($res['data'])) {
                        $obstetra = $res['data'][0];
                        $obstetra[] = [
                            'cep' => trim($obstetra['cop']),
                            'nombres' => trim($obstetra['nombres']),
                            'estado' => trim($obstetra['estado']),
                            'foto' => trim($obstetra['photo']),
                            'cdr' => trim($obstetra['colegio_regional']),
                        ];

                        if (count($obstetra) > 0) {
                            $res = [
                                'success' => true,
                                'data' => $obstetra,
                                'tiporespuesta' => 1
                            ];
                        } else {
                            $res = [
                                'success' => false,
                                'message' => 'NO SE ENCONTRÓ AL MÉDICO EN LOS REGISTROS DEL COLEGIO MÉDICO DEL PERÚ.',
                                'tiporespuesta' => 0
                            ];
                        }
                    } else {
                        $res = [
                            'success' => false,
                            'message' => 'NO SE ENCONTRÓ AL MÉDICO EN LOS REGISTROS DEL COLEGIO MÉDICO DEL PERÚ.',
                            'tiporespuesta' => 0
                        ];
                    }
                }
            } else {
                $res = [
                    'success' => false,
                    'message' => 'NO SE PUDO CONECTAR CON EL SERVICIO DE CONSULTA DEL COLEGIO MÉDICO DEL PERÚ.',
                    'tiporespuesta' => -1
                ];
            }
            $response->throw();
        } catch (ConnectionException $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'tiporespuesta' => -1
            ];
        } catch (RequestException $re) {
            $res = [
                'success' => false,
                'message' => $re->getMessage(),
                'tiporespuesta' => -1
            ];
        } catch (ErrorException $ee) {
            $res = [
                'success' => false,
                'message' => $ee->getMessage(),
                'tiporespuesta' => -1
            ];
        }

        return $res;
    }
}
