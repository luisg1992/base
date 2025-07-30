<?php

namespace App\Console\Commands;

use App\Console\Helpers\ProgressBarHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Api\Http\Controllers\Sms\SmsController;

class ListaEsperaCitaSmsCommand extends Command
{
    protected $signature = 'sms:lista.espera.cita';
    protected $description = 'Enviar Recordatorio de Lista de Espera de Cita';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Hora de inicio: ' . now());
        $this->info('Inicializando Proceso');

        $baseQuery = DB::table('WebS_CitasDemandaInsatisfecha as cd')
            ->join('Pacientes as p', 'cd.IdPaciente', '=', 'p.IdPaciente')
            ->join('Especialidades as esp', 'esp.IdEspecialidad', '=', 'cd.IdEspecialidad')
            ->where('cd.EnvioSMS', 0)
            ->whereRaw('LEN(cd.Celular) = 9')
            ->where('cd.Celular', 'like', '9%')
            ->whereRaw('ISNUMERIC(cd.Celular) = 1')
            ->select(
                'cd.IdDemaInsatisfecha as IdDemaInsatisfecha',
                'cd.Celular as Celular',
                'p.PrimerNombre as Paciente',
                'esp.Nombre as Especialidad',
                'cd.FechaRegistro as Fecha'
            )
            ->orderBy('cd.FechaRegistro');


        $total = $baseQuery->take(60)->count();
        $processed = 0;
        $progressHelper = new ProgressBarHelper();

        $baseQuery->take(60)->chunk(10, function ($citas_espera) use (&$processed, $total, $progressHelper) {
            foreach ($citas_espera as $cita) {
                $start = microtime(true);

                $fechaCita = Carbon::parse($cita->Fecha)->format('d/m/Y');
                $mensaje = "HNDM, Hola {$cita->Paciente}, hoy {$fechaCita} te hemos agregado a la lista de espera de citas en la especialidad de {$cita->Especialidad}.";

                $respuesta = (new SmsController())->sendByFormParams([
                    'title' => 'Lista de Espera - Cita',
                    'body'  => $mensaje,
                    'phone' => $cita->Celular,
                ]);

                $end = microtime(true);
                $duration = round($end - $start, 2);

                if ($respuesta['success']) {
                    $status = 'success';
                    $message = "IdDemaInsatisfecha: {$cita->IdDemaInsatisfecha} | Tiempo: {$duration}s";

                    DB::table('WebS_CitasDemandaInsatisfecha')
                        ->where('IdDemaInsatisfecha', $cita->IdDemaInsatisfecha)
                        ->update(['EnvioSMS' => true]);
                } else {
                    $status = 'error';
                    $message = "{$respuesta['message']} | IdDemaInsatisfecha: {$cita->IdDemaInsatisfecha} | Tiempo: {$duration}s";
                }

                sleep(6);
                $processed++;
                $progressHelper->render($processed, $total, $status, $message);
            }
        });

        $this->info('Finalizando Proceso');
        $this->info('Hora de término: ' . now());
    }
}
