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

class DoctorController extends Controller
{
    private $apiConfig;

    public function __construct()
    { 
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
                ->post("{$this->apiConfig->url}/api/doctor", $data);

            if ($response->ok()) {
                $res = $response->json();

                $doctor = [];

                if (isset($res['code']) && $res['code'] == 504) {
                    $res = [
                        'success' => false,
                        'message' => 'NO SE PUDO CONECTAR CON EL SERVICIO DE CONSULTA DEL COLEGIO MÉDICO DEL PERÚ.',
                        'tiporespuesta' => -1
                    ];
                } else {

                    if (isset($res['data']) && !empty($res['data'])) {
                        $medico = $res['data'][0]; // Accede al primer elemento del array 'data'

                        if (!empty($medico['cmp']) && isset($medico['detalle_adicional']['estado']) && !empty($medico['detalle_adicional']['estado'])) {
                            $doctor[] = [
                                'cmp' => trim($medico['cmp']),
                                'apellido_paterno' => trim($medico['apellido_paterno']),
                                'apellido_materno' => trim($medico['apellido_materno']),
                                'nombres' => trim($medico['nombres']),
                                'detalle_adicional' => [
                                    'estado' => trim($medico['detalle_adicional']['estado']),
                                    'consejo_regional' => trim($medico['detalle_adicional']['consejo_regional']),
                                    'informacion_adicional' => trim($medico['detalle_adicional']['informacion_adicional']),
                                    'foto' => $medico['detalle_adicional']['foto'],
                                    'url' => $medico['detalle_adicional']['url'],
                                    'especialidades' => isset($medico['detalle_adicional']['especialidades']) ? $medico['detalle_adicional']['especialidades'] : [],
                                ]
                            ];

                            if (count($doctor) > 0) {
                                $res = [
                                    'success' => true,
                                    'data' => $doctor,
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
                                'message' => 'HA OCURRIDO UN ERROR AL CONSULTAR AL COLEGIO MÉDICO DEL PERÚ. POR FAVOR, VUELVA A INTENTAR.',
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
