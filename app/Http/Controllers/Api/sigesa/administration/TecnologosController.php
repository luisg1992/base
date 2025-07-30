<?php

namespace App\Http\Controllers\Api\sigesa\administration;

use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

class TecnologosController extends Controller
{
    private $apiConfig;

    public function __construct()
    {
        $this->middleware('auth');
        $this->apiConfig = DB::select('EXEC WebS_ParametrosApiMostrarUrlToken')[0] ?? null;
    }

    public function searchByTecnologo(Request $request)
    {
        $timeout = 10;
        $ctm = $request->input('NumeroColegiatura');
        $data = ["ctm" => $ctm];

        try {
            $response = Http::withToken($this->apiConfig->token)
                ->timeout($timeout)
                ->post("{$this->apiConfig->url}/api/tecnologo/ctm", $data);

            if ($response->ok()) {
                $res = $response->json();
                $personal = [];

                if (isset($res['data']) && !empty($res['data'])) {
                    $tecnologo = $res['data'];

                    $personal[] = [
                        'success' => true,
                        'ctm' => trim($tecnologo['ctm']),
                        'nombres' => trim($tecnologo['nombres']) . ' ' . trim($tecnologo['apellidos']),
                        'especialidad' => trim($tecnologo['especialidad']),
                        'estado' => trim(empty($tecnologo['estado']) ? 'HABILITADO' : $tecnologo['estado']),
                        'cdr' => trim($tecnologo['consejo']),
                    ];

                    if (count($personal) > 0) {
                        $res = [
                            'success' => true,
                            'data' => $personal,
                            'tiporespuesta' => 1
                        ];
                    } else {
                        $res = [
                            'success' => false,
                            'message' => 'NO SE ENCONTRÓ AL TECNÓLOGO EN LOS REGISTROS DEL COLEGIO ODONTOLÓGICO DEL PERÚ.',
                            'tiporespuesta' => 0
                        ];
                    }
                } else {
                    $res = [
                        'success' => false,
                        'message' => 'NO SE ENCONTRÓ AL TECNÓLOGO EN LOS REGISTROS DEL COLEGIO ODONTOLÓGICO DEL PERÚ.',
                        'tiporespuesta' => 0
                    ];
                }
            } else {
                $res = [
                    'success' => false,
                    'message' => 'NO SE PUDO CONECTAR CON EL SERVICIO DE CONSULTA DEL COLEGIO DE TECNOLOGOS DEL PERÚ.',
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
