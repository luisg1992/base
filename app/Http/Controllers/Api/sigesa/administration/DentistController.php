<?php

namespace App\Http\Controllers\Api\sigesa\administration;

use ErrorException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;

class DentistController extends Controller
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
                ->post("{$this->apiConfig->url}/api/odontologo", $data);

            if ($response->ok()) {
                $res = $response->json(); 
                $dentists = [];

                if (isset($res['data']) && !empty($res['data'])) {
                    $odontologo = $res['data'][0]; // Accede al primer elemento del array 'data'
                    
                    $dentists[] = [
                        'cop' => trim($odontologo['cop']),
                        'nombres' => trim($odontologo['nombres']), 
                        'foto' =>  trim($odontologo['photo']),
                        'estado' =>  trim($odontologo['estado']),
                        'region' =>  trim($odontologo['region'])
                    ];

                    if (count($dentists) > 0) {
                        $res = [
                            'success' => true,
                            'data' => $dentists,
                            'tiporespuesta' => 1
                        ];
                    } else {
                        $res = [
                            'success' => false,
                            'message' => 'NO SE ENCONTRÓ AL ODONTÓLOGO EN LOS REGISTROS DEL COLEGIO ODONTOLÓGICO DEL PERÚ.',
                            'tiporespuesta' => 0
                        ];
                    }
                } else {
                    $res = [
                        'success' => false,
                        'message' => 'NO SE ENCONTRÓ AL ODONTÓLOGO EN LOS REGISTROS DEL COLEGIO ODONTOLÓGICO DEL PERÚ.',
                        'tiporespuesta' => 0
                    ];
                }

                /*} else {
                    $res = [
                        'success' => false,
                        'message' => 'NO SE ENCONTRÓ AL MÉDICO EN LOS REGISTROS DEL COLEGIO ODONTOLÓGICO DEL PERÚ.'
                    ];
                }*/
            } else {
                $res = [
                    'success' => false,
                    'message' => 'NO SE PUDO CONECTAR CON EL SERVICIO DE CONSULTA DEL COLEGIO ODONTOLÓGICO DEL PERÚ.',
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
