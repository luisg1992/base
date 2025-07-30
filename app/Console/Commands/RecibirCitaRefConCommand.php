<?php

namespace App\Console\Commands;

use App\Console\Helpers\ProgressBarHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Api\Http\Controllers\RefconMicroservicios\GenerarEnvioPrimeraCitaReferenciaController;
use Modules\Api\Http\Controllers\RefconMicroservicios\RecibirCitaRefconController;

class RecibirCitaRefConCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refcon:recibir.cita';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recibir cita refcon';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Hora de inicio: ' . now());
        $this->info('Inicializando Proceso');
        
        $records = (new RecibirCitaRefconController())->obtenerRegistros();
        $total = count($records);
        $processed = 0;
        $progressHelper = new ProgressBarHelper();
        foreach ($records as $record) {
            $start = microtime(true); // INICIO de la iteración
            $resRefcon = (new RecibirCitaRefconController())->enviar($record['data']);
            $res = $resRefcon->getData(true);

            $end = microtime(true);   // FIN
            $duration = round($end - $start, 2); // Tiempo en segundos

            if ($res['success']) {
                DB::table('SIGH_STORAGE.dbo.AtencionesReferencia')
                    ->where('IdReferenciaRefCon', $record['IdReferenciaRefCon'])
                    ->update([
                        'CitaEnviadaRefcon' => true,
                        'CitaErrorEnvioTramaRefcon' => null,
                    ]);

                DB::table('SIGH.dbo.Referencias')
                    ->where('IdReferenciaRefCon', $record['IdReferenciaRefCon'])
                    ->update([
                        'CodigoEstado' => 7,
                        'EstadoReferencia' => 'PACIENTE CITADO'
                    ]);
                $status = 'success';
                $message = "IdReferencia: {$record['IdReferencia']} | Tiempo: {$duration}s";
                sleep(6);
            } else {
                DB::table('SIGH_STORAGE.dbo.AtencionesReferencia')
                    ->where('IdReferenciaRefCon', $record['IdReferenciaRefCon'])
                    ->update([
                        'CitaErrorEnvioTramaRefcon' => true,
                    ]);
                $status = 'error';
                $message = "{$res['message']} | IdReferencia: {$record['IdReferencia']} | Tiempo: {$duration}s";
            }
            $processed++;
            $progressHelper->render($processed, $total, $status, $message);
        }

        $this->info('Finalizando Proceso');
        $this->info('Hora de término: ' . now());
    }
}
