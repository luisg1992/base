<?php

namespace Modules\Api\Http\Controllers\RefconMicroservicios;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use ErrorException;
use Throwable;

class RefconEstadoRefconController extends Controller
{
    public function estadoReferencia(Request $request)
    {
        $idReferencia = $request->input('idReferencia'); 
        if (empty($idReferencia)) {
            return [
                'success' => false,
                'code'    => 422,
                'message' => 'El campo idReferencia es requerido.',
            ];
        }

        $url = "https://pidesalud.minsa.gob.pe/mcs-sihce-refcon/refcon/integracion/v1.0/referencia/estadoReferencia";
        try {
            $response = Http::withHeaders([
                'username' => config('services.establecimiento.codigo'),
            ])
                ->timeout(10)
                ->get("{$url}?idReferencia={$idReferencia}");

            if ($response->ok()) {
                return $response->json();
            } 
            $response->throw(); 
        } catch (ConnectionException|RequestException|ErrorException|Throwable $e) {
            return [
                'success' => false,
                'code'    => $e->getCode() ?: 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function obtenerEstadoReferencia(string $descripcion): ?array
    {
        $descripcion = strtoupper(trim($descripcion)); 
        return [
            'TODOS' => ['CodigoEstado' => '-1', 'EstadoReferencia' => 'TODOS', 'CitaEnviadaRefcon' => '0', 'CitaRecibePacienteRefcon' => '0', 'CitaErrorEnvioTramaRefcon' => null],
            'PENDIENTE' => ['CodigoEstado' => '2', 'EstadoReferencia' => 'PENDIENTE', 'CitaEnviadaRefcon' => '0', 'CitaRecibePacienteRefcon' => '0', 'CitaErrorEnvioTramaRefcon' => null],
            'ACEPTADO' => ['CodigoEstado' => '3', 'EstadoReferencia' => 'ACEPTADO', 'CitaEnviadaRefcon' => '0', 'CitaRecibePacienteRefcon' => '0', 'CitaErrorEnvioTramaRefcon' => null],
            'RECHAZADO' => ['CodigoEstado' => '4', 'EstadoReferencia' => 'RECHAZADO', 'CitaEnviadaRefcon' => '0', 'CitaRecibePacienteRefcon' => '0', 'CitaErrorEnvioTramaRefcon' => null],
            'OBSERVADO' => ['CodigoEstado' => '9', 'EstadoReferencia' => 'OBSERVADO', 'CitaEnviadaRefcon' => '0', 'CitaRecibePacienteRefcon' => '0', 'CitaErrorEnvioTramaRefcon' => null],
            'PACIENTE RECIBIDO' => ['CodigoEstado' => '5', 'EstadoReferencia' => 'PACIENTE RECIBIDO', 'CitaEnviadaRefcon' => '1', 'CitaRecibePacienteRefcon' => '1', 'CitaErrorEnvioTramaRefcon' => null],
            'PACIENTE CITADO' => ['CodigoEstado' => '7', 'EstadoReferencia' => 'PACIENTE CITADO', 'CitaEnviadaRefcon' => '1', 'CitaRecibePacienteRefcon' => '0', 'CitaErrorEnvioTramaRefcon' => null],
            'CONTRAREFERIDO' => ['CodigoEstado' => '8', 'EstadoReferencia' => 'CONTRAREFERIDO', 'CitaEnviadaRefcon' => '1', 'CitaRecibePacienteRefcon' => '1', 'CitaErrorEnvioTramaRefcon' => null],
            'DE BAJA' => ['CodigoEstado' => '0', 'EstadoReferencia' => 'DE BAJA', 'CitaEnviadaRefcon' => '0', 'CitaRecibePacienteRefcon' => '0', 'CitaErrorEnvioTramaRefcon' => null],
        ][$descripcion] ?? null;
    }
}
