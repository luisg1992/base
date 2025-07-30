<?php

namespace Modules\Api\Http\Controllers\RefconMicroservicios;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class RefconConsultaReferenciaController extends Controller
{
    /**
     * Consulta referencias en Refcon
     */
    public function consultaReferencia(Request $request)
    {
        /** 1) Validar la entrada del cliente */
        $payload = $request->validate([ 
            'tipodocumento'          => 'required|in:1,2,3,4,5,6,7,8,9,10',
            'numerodocumento'        => 'required|string|max:15', 
        ]);

        // Valores por defecto
        $payload = array_merge([
            'establecimientoDestino' => '6206',
            'pagina' => 1,
            'limite' => 100,
        ], $payload);

        /** 2) Preparar cabeceras */
        $headers = [
            'username'    => config('services.mefMicro_Produccion.username'),
            'password'    => config('services.mefMicro_Produccion.password'),
            'ipclient'    => config('services.mefMicro_Produccion.ipclient'),
            'Content-Type' => 'application/json',
        ];

        /** 3) Consumir el microservicio */
        try { 
            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->post(
                    config('services.mefMicro_Produccion.base_url') . '/consultaReferencia',
                    $payload
                );

            /** 4) Manejar respuesta HTTP no exitosa */
            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error consultando Refcon',
                    'status'  => $response->status(),
                    'errors'  => $response->json(),
                ], $response->status());
            }

            /** 5) Todo OK */
            return response()->json([
                'success' => true,
                'data' => $response->json()
            ]);
        } catch (ConnectionException $e) {
            // No se pudo establecer conexión
            return response()->json([
                'success' => false,
                'message' => 'Servicio Refcon inaccesible',
            ], 503);
        } catch (RequestException $e) {
            // Timeout, DNS, TLS, etc.
            return response()->json([
                'success' => false,
                'message' => 'Fallo en la solicitud a Refcon',
                'error'   => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            // Errores inesperados
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Error interno al procesar la referencia',
            ], 500);
        }
    }
}
