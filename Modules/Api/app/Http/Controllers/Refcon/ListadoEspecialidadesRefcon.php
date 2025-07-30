<?php

namespace Modules\Api\Http\Controllers\Refcon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class ListadoEspecialidadesRefcon extends Controller
{
    public function listadoEspecialidades(Request $request)
    {
        $url = "https://pidesalud.minsa.gob.pe/mcs-sihce-refcon/refcon/integracion/v1.0/referencia/listadoEspecialidades"; 
        try {
            $response = Http::withHeaders([
                'username' => config('services.establecimiento.codigo'),
            ])->timeout(config('services.establecimiento.timeout', 10))
                ->get($url);

            if ($response->ok()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'code' => $response->status(),
                'message' => 'Error al obtener especialidades.',
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }
}
