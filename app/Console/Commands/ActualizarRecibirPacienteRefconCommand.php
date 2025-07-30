<?php

namespace App\Console\Commands;

use App\Console\Helpers\ProgressBarHelper;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Api\Http\Controllers\Refcon\RecibirPacienteRefcon;

class ActualizarRecibirPacienteRefconCommand extends Command
{
    protected $signature = 'refcon:portal.web.recibir.paciente';
    protected $description = 'Recibir Paciente Refcon Web';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Hora de inicio: ' . now());
        $this->info('Inicializando Proceso');

        $processed = 0;
        $progressHelper = new ProgressBarHelper();
        $total = 100;

        DB::table('SIGH_STORAGE.dbo.AtencionesReferencia as ar')
            ->join('SIGH.dbo.Referencias as ref', 'ref.IdReferencia', '=', 'ar.IdReferencia')
            ->join('SIGH.dbo.Atenciones as at', 'at.IdCuentaAtencion', '=', 'ar.IdcuentaAtencion')
            ->join('SIGH_EXTERNA.dbo.atencionesCE as atce', 'at.IdAtencion', '=', 'atce.idAtencion')
            ->join('Citas as c', 'c.IdCita', '=', 'ar.IdCita')
            ->where('ref.EstadoReferencia', '=', 'PACIENTE CITADO')
            ->whereNotNull('atce.CitaDiagMed')
            ->whereNull('ar.CitaErrorEnvioTramaRefcon')
            ->select(
                'ar.IdAtencionReferencia',
                'ar.IdcuentaAtencion',
                'ar.IdReferencia',
                'ar.IdCita',
                'at.IdAtencion',
                'ref.IdReferenciaRefCon'
            )
            ->take(100)
            ->chunkById(30, function ($records) use (&$processed, $total, $progressHelper) {
                foreach ($records as $record) {
                    try {
                        $start = microtime(true);

                        // Crear solicitud simulada
                        $request = Request::create('', 'POST', [
                            'IdReferenciaRefCon' => $record->IdReferenciaRefCon
                        ]);

                        // Llamar al controlador como servicio
                        $response = (new RecibirPacienteRefcon())->recibirPacienteRefcon($request);
                        $end = microtime(true);
                        $duration = round($end - $start, 2);

                        if ($response['success']) {
                            DB::table('SIGH_STORAGE.dbo.AtencionesReferencia')
                                ->where('IdReferenciaRefCon', $record->IdReferenciaRefCon)
                                ->update([
                                    'CitaEnviadaRefcon' => true,
                                    'CitaRecibePacienteRefcon' => true,
                                    'CitaErrorEnvioTramaRefcon' => null,
                                ]);

                            DB::table('SIGH.dbo.Referencias')
                                ->where('IdReferenciaRefCon', $record->IdReferenciaRefCon)
                                ->update([
                                    'CodigoEstado' => 5,
                                    'EstadoReferencia' => 'PACIENTE RECIBIDO'
                                ]);

                            $status = 'success';
                            $message = "IdReferencia: {$record->IdReferencia} | Tiempo: {$duration}s";
                        } else {
                            $status = 'error';
                            $message = $response['message'] . " | Tiempo: {$duration}s";
                        }
                    } catch (\Throwable $e) {
                        $status = 'error';
                        $message = "Error en Id: {$record->IdAtencionReferencia} - {$e->getMessage()}";
                    }

                    $processed++;
                    $progressHelper->render($processed, $total, $status, $message);

                    sleep(5); // Opcional: para evitar sobrecargar el servicio RefCon
                }
            }, 'IdAtencionReferencia');

        $this->info('Proceso finalizado: ' . now());
    }
}
