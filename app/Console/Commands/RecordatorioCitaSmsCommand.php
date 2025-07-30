<?php

namespace App\Console\Commands;

use App\Console\Helpers\ProgressBarHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Api\Http\Controllers\Sms\SmsController;

class RecordatorioCitaSmsCommand extends Command
{
    protected $signature = 'sms:recordatorio.cita';
    protected $description = 'Enviar Recordatorio';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Hora de inicio: ' . now());
        $this->info('Inicializando Proceso'); 

        $baseQuery = DB::table('Citas')
            ->join('Pacientes', 'Citas.IdPaciente', '=', 'Pacientes.IdPaciente')
            ->join('Medicos', 'Citas.IdMedico', '=', 'Medicos.IdMedico')
            ->join('Empleados', 'Medicos.IdEmpleado', '=', 'Empleados.IdEmpleado')
            ->join('Servicios', 'Citas.IdServicio', '=', 'Servicios.IdServicio')
            ->whereRaw("
                CAST(Citas.Fecha AS DATE) BETWEEN 
                CAST(GETDATE() AS DATE) AND 
                CAST(DATEADD(DAY, 1, GETDATE()) AS DATE)
            ")
            ->where(function ($query) {
                $query->where('Citas.RecordatorioSMS', false)
                    ->orWhereNull('Citas.RecordatorioSMS');
            })
            ->where('Citas.idEstadoColaCitas', 1)
            ->whereRaw("LEN(Pacientes.Telefono) = 9")
            ->whereRaw("Pacientes.Telefono LIKE '9%'")
            ->whereRaw("ISNUMERIC(Pacientes.Telefono) = 1")
            ->select(
                'Citas.IdCita',
                'Citas.Fecha',
                'Citas.HoraInicio',
                'Pacientes.Telefono',
                'Pacientes.PrimerNombre',
                'Empleados.ApellidoPaterno as MedidoApellidoPaterno',
                'Servicios.Nombre as ConsultorioNombre'
            )
            ->orderBy('Citas.Fecha')
            ->orderBy('Citas.HoraInicio');

        $total = $baseQuery->take(100)->count();
        $processed = 0;
        $progressHelper = new ProgressBarHelper();

        $baseQuery->take(100)->chunk(10, function ($citas) use (&$processed, $total, $progressHelper) {
            foreach ($citas as $cita) {
                $start = microtime(true);

                $fechaCita = Carbon::parse($cita->Fecha)->format('d/m/Y');
                $mensaje = "HNDM, Hola {$cita->PrimerNombre}, tu cita es el {$fechaCita} a las {$cita->HoraInicio}, en {$cita->ConsultorioNombre} con el Dr(a).{$cita->MedidoApellidoPaterno}";

                $respuesta = (new SmsController())->sendByFormParams([
                    'title' => 'Recordatorio de cita',
                    'body'  => $mensaje,
                    'phone' => $cita->Telefono,
                ]);

                $end = microtime(true);
                $duration = round($end - $start, 2);

                if ($respuesta['success']) {
                    $status = 'success';
                    $message = "IdCita: {$cita->IdCita} | Tiempo: {$duration}s";

                    DB::table('Citas')
                        ->where('IdCita', $cita->IdCita)
                        ->update(['RecordatorioSMS' => true]);
                } else {
                    $status = 'error';
                    $message = "{$respuesta['message']} | IdCita: {$cita->IdCita} | Tiempo: {$duration}s";
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
