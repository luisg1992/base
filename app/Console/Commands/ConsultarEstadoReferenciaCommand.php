<?php

namespace App\Console\Commands;

use App\Console\Helpers\ProgressBarHelper;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Api\Http\Controllers\RefconMicroservicios\RefconEstadoRefconController;

class ConsultarEstadoReferenciaCommand extends Command
{
    protected $signature = 'refcon:consultar.estado';
    protected $description = 'Consultar estado de referencia desde Refcon';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Inicio del proceso: ' . now());

        $processed = 0;
        $progressHelper = new ProgressBarHelper();

        // Obtener el total de registros a procesar
        $total = 100;
        DB::table('SIGH_STORAGE.dbo.AtencionesReferencia')
            ->select([
                DB::raw('MIN(IdAtencionReferencia) as IdAtencionReferencia'),
                'IdReferenciaRefCon',
                DB::raw('MIN(IdReferencia) as IdReferencia')
            ])
            ->whereNotNull('CitaErrorEnvioTramaRefcon')
            ->whereNotNull('IdCita')
            ->groupBy('IdReferenciaRefCon')
            ->orderBy('IdAtencionReferencia')
            ->take(100)
            ->chunkById(20, function ($records) use (&$processed, $total, $progressHelper) {
                foreach ($records as $record) {
                    try {
                        $controller = new RefconEstadoRefconController();
                        $request = Request::create('', 'POST', [
                            'idReferencia' => $record->IdReferenciaRefCon
                        ]);

                        $response = $controller->estadoReferencia($request); 
                        if (($response['codigo_respuesta'] ?? null) === '0000') {
                            $estadoTexto = strtoupper(trim($response['referencia']['estado'] ?? ''));
                            $estado = $controller->obtenerEstadoReferencia($estadoTexto); 

                            if ($estado) { 
                                DB::table('SIGH.dbo.Referencias')
                                    ->where('IdReferenciaRefCon', $record->IdReferenciaRefCon)
                                    ->update([
                                        'CodigoEstado'     => $estado['CodigoEstado'],
                                        'EstadoReferencia' => $estado['EstadoReferencia'],
                                    ]);

                                DB::table('SIGH_STORAGE.dbo.AtencionesReferencia')
                                    ->where('IdReferenciaRefCon', $record->IdReferenciaRefCon)
                                    ->update([
                                        'CitaEnviadaRefcon'     => $estado['CitaEnviadaRefcon'],
                                        'CitaRecibePacienteRefcon' => $estado['CitaRecibePacienteRefcon'],
                                        'CitaErrorEnvioTramaRefcon' => $estado['CitaErrorEnvioTramaRefcon'],
                                    ]);

                                $status = 'success';
                                $message = "{$record->IdReferencia} actualizado con estado {$estado['EstadoReferencia']}";
                            } else {
                                $status = 'warning';
                                $message = "{$record->IdReferencia}  Estado no reconocido: {$estadoTexto}";
                            }
                        } else {
                            $codigo = $response['data']['codigo_respuesta'] ?? '---';
                            $mensaje = $response['data']['mensaje_respuesta'] ?? 'Sin mensaje';
                            $status = 'error';
                            $message = "RefCon respondió código {$codigo} - {$mensaje}";
                        }
                    } catch (\Throwable $e) {
                        $status = 'error';
                        $message = "Excepción en ID: {$record->IdReferencia} - {$e->getMessage()}";
                    }

                    $processed++;
                    $progressHelper->render($processed, $total, $status, $message);

                    sleep(5); // Controlar la velocidad de peticiones
                }
            }, 'IdReferencia');

        $this->info('Proceso terminado: ' . now());
    }
}
