<?php

namespace App\Console\Commands;

use App\Console\Helpers\ProgressBarHelper;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Api\Http\Controllers\RefconReport\RefconPreviewPDFController;

class ActualizarPdfCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refcon:descargar.pdf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Descargar referencia pdf';

    /**
     * Create a new command instance.
     */
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

        // ✅ Calcular el total global (los 65 registros a procesar)
        $total = 80; 
        DB::table('SIGH_STORAGE.dbo.AtencionesReferencia')
            ->where(function ($q) {
                $q->whereNull('ArchivoB64')
                    ->orWhereRaw("CAST(ArchivoB64 AS NVARCHAR(MAX)) = ''");
            })
            ->take(80)
            ->chunkById(10, function ($records) use (&$processed, $total, $progressHelper) {
                foreach ($records as $record) {
                    $row = DB::table('Referencias')
                        ->where('IdReferencia', $record->IdReferencia)
                        ->first();

                    if ($row) {
                        $request = Request::create('', 'POST', [
                            'idestablecimiento' => $row->CodigoEstablecimientoOrigen,
                            'idreferencia' => $row->IdReferenciaRefCon,
                            'estadoreferencia' => $row->EstadoReferencia,
                        ]);

                        try {
                            $start = microtime(true);
                            $res = (new RefconPreviewPDFController())->visualizarReferenciaRefcon($request);
                            $data = $res->getData(true);
                            $end = microtime(true);
                            $duration = round($end - $start, 2);

                            if ($data['success']) {
                                DB::table('SIGH_STORAGE.dbo.AtencionesReferencia')
                                    ->where('IdReferenciaRefCon', $record->IdReferenciaRefCon)
                                    ->update([
                                        'ArchivoB64' => $data['respuesta'],
                                    ]);
                                $status = 'success';
                                $message = "{$record->IdReferenciaRefCon} | Tiempo: {$duration}s";
                            } else {
                                $status = 'error';
                                $message = $data['message'] . " | Tiempo: {$duration}s";
                            }
                        } catch (\Throwable $e) {
                            $status = 'error';
                            $message = "Error en Id: {$record->IdAtencionReferencia} - {$e->getMessage()}";
                        }
                    }

                    $processed++;
                    $progressHelper->render($processed, $total, $status, $message);

                    sleep(6); // Controla cuántos por hora se procesan
                }
            }, 'IdAtencionReferencia');

        $this->info('Finalizando Proceso');
        $this->info('Hora de término: ' . now());
    }
}
