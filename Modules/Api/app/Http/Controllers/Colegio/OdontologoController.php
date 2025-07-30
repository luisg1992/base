<?php

namespace Modules\Api\Http\Controllers\Colegio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OdontologoController extends Controller
{
    public function consultarPorNombre(Request $request)
    {
        $apellidos = $request->input('apellidos');
        $nombres = $request->input('nombres');

        if (is_null($apellidos) && is_null($nombres)) {
            return [
                'success' => false,
                'message' => 'Se requiere al menos un campo: apellidos o nombres',
            ];
        }

        $datos = [
            'id1' => 2,
            'tipo_busqueda' => '2',
            'cop' => null,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
        ];

        return $this->realizarConsulta($datos);
    }

    public function consultarPorCop(Request $request)
    {
        $cop = $request->input('cop');
        if (is_null($cop)) {
            return [
                'success' => false,
                'message' => 'El campo cop es requerido',
            ];
        }

        $datos = [
            'id1' => 1,
            'tipo_busqueda' => '1',
            'cop' => $cop,
            'nombres' => null,
            'apellidos' => null,
        ];

        return $this->realizarConsulta($datos);
    }

    private function realizarConsulta(array $datos): array
    {
        $inicio = microtime(true);
        $url = 'https://buscador.col.org.pe/recursos/recursos_json.php';

        try {
            $respuesta = Http::timeout(10)
                ->asForm()
                ->post($url, ['data' => json_encode(['recursos' => $datos])]);

            if ($respuesta->ok()) {
                $resultado = $respuesta->json();
                if ($resultado['cargado'] === 1 && !empty($resultado['registros'])) {
                    $registros = array_map(function ($registro) {
                        return [
                            'cop' => $registro['cop'],
                            'nombres' => $registro['nombres'],
                            'apellidos' => $registro['apellidos'],
                            'estado' => $registro['estado'],
                        ];
                    }, $resultado['registros']);

                    return [
                        'success' => true,
                        'data' => $registros,
                        'time' => microtime(true) - $inicio,
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'No se encontraron resultados',
                    'time' => microtime(true) - $inicio,
                ];
            }

            return [
                'success' => false,
                'message' => 'Error en la solicitud HTTP',
                'time' => microtime(true) - $inicio,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'time' => microtime(true) - $inicio,
            ];
        }
    }
}
