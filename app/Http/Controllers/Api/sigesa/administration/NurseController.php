<?php

namespace App\Http\Controllers\Api\sigesa\administration;
 
use ErrorException;
use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;

class NurseController extends Controller
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
                'message' => 'INGRESE LOS NOMBRES Y APELLIDOS DEL ENFERMERO(A).',
                'tiporespuesta' => 0
            ];
        }

        $NombreCompleto = trim($ApePaterno . ' ' . $ApeMaterno . ' ' . $Nombres);
        $timeout = 10;
        $data = [
            "nombres" => $NombreCompleto
        ];
        try {
            // Llamada HTTP POST
            $response = Http::withToken($this->apiConfig->token)
                ->timeout($timeout)
                ->post("{$this->apiConfig->url}/api/enfermera", $data);

            if ($response->ok()) {
                $res = $response->json();

                $enfermero = [];

                if (isset($res['data']) && !empty($res['data'])) {
                    $enfermero = $res['data'][0];
                    $enfermeroArray = [
                        'cep' => trim($enfermero['cep']),
                        'nombres' => trim($enfermero['nombres']),
                        'estado' => trim($enfermero['estado']),
                        'foto' => trim($enfermero['foto']),
                        'cdr' => trim($enfermero['consejo_regional']),
                    ];

                    if (count($enfermeroArray) > 0) {
                        $res = [
                            'success' => true,
                            'data' => $enfermeroArray,
                            'tiporespuesta' => 1
                        ];
                    } else {
                        $res = [
                            'success' => false,
                            'message' => 'NO SE ENCONTRÓ AL ENFERMERO(A) EN LOS REGISTROS DEL COLEGIO DE ENFERMEROS DEL PERÚ.',
                            'tiporespuesta' => 0
                        ];
                    }
                } else {
                    $res = [
                        'success' => false,
                        'message' => 'NO SE ENCONTRÓ AL ENFERMERO(A) EN LOS REGISTROS DEL COLEGIO DE ENFERMEROS DEL PERÚ.',
                        'tiporespuesta' => 0
                    ];
                }
            } else {
                $res = [
                    'success' => false,
                    'message' => 'NO SE PUDO CONECTAR CON EL SERVICIO DE CONSULTA DEL COLEGIO DE ENFERMEROS DEL PERÚ.',
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
