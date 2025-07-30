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

class PsychologistController extends Controller
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
                'message' => 'INGRESE LOS NOMBRES Y APELLIDOS DEL PSICÓLOGO(A).',
                'tiporespuesta' => 0
            ];
        }

        $NombreCompleto = trim($ApePaterno . ' ' . $ApeMaterno . ' ' . $Nombres);
        $data = ["nombres" => $NombreCompleto];
        $timeout = 10;

        try {
            $response = Http::withToken($this->apiConfig->token)
                ->timeout($timeout)
                ->post("{$this->apiConfig->url}/api/psicologo", $data);

            if ($response->ok()) {
                $res = $response->json();

                $doctor = [];

                if (isset($res['data']) && !empty($res['data'])) {
                    $odontologo = $res['data'][0];
                    $doctor[] = [
                        'cpsp' => trim($odontologo['cpsp']),
                        'nombres' => trim($odontologo['nombres']),
                        'cdr' => trim($odontologo['cdr']),
                        'estado' => trim(empty($odontologo['estado']) ? 'HABILITADO' : $odontologo['estado'])
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
                            'message' => 'NO SE ENCONTRÓ AL PSICÓLOGO(A) EN LOS REGISTROS DEL COLEGIO DE PSICÓLOGOS DEL PERÚ.',
                            'tiporespuesta' => 0
                        ];
                    }
                } else {
                    $res = [
                        'success' => false,
                        'message' => 'NO SE ENCONTRÓ AL PSICÓLOGO(A) EN LOS REGISTROS DEL COLEGIO DE PSICÓLOGOS DEL PERÚ.',
                        'tiporespuesta' => 0
                    ];
                }
            } else {
                $res = [
                    'success' => false,
                    'message' => 'NO SE PUDO CONECTAR CON EL SERVICIO DE CONSULTA DEL COLEGIO DE PSICÓLOGOS DEL PERÚ.',
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
