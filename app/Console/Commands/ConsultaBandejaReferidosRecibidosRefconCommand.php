<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\WebSBandejaReferido;
use App\Console\Helpers\ProgressBarHelper;
use Modules\Api\Http\Controllers\RefconMicroservicios\ConsultaBandejaReferidosRecibidosRefconController;

class ConsultaBandejaReferidosRecibidosRefconCommand extends Command
{
    protected $signature = 'refcon:bandeja.referidos.recibido';
    protected $description = 'Consultar bandeja referidos recibidos desde Refcon';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Inicio del proceso: ' . now());

        // Intentar obtener fechas directamente de REFERENCIAS
        $fechas = DB::table('REFERENCIAS as r')
            ->leftJoin('FechasReferenciaProcesadas as f', 'r.FechaEnvio', '=', 'f.FechaEnvio')
            ->select('r.FechaEnvio')
            ->where('r.EstadoReferencia', 'ACEPTADO')
            ->whereNull('r.TieneTrama') // Solo las que no han sido procesadas aún
            ->whereNull('f.FechaEnvio') // Que no estén en la tabla de procesadas
            ->groupBy('r.FechaEnvio')
            ->orderBy('r.FechaEnvio', 'ASC') // Ordenar de más antigua a reciente
            ->limit(20)
            ->pluck('r.FechaEnvio')
            ->map(fn($fecha) => Carbon::parse($fecha));

        // Si no hay fechas pendientes en REFERENCIAS, generamos la fecha de hoy
        if ($fechas->isEmpty()) {
            $this->warn('No se encontraron fechas para procesar. Se usará la fecha de hoy.');
            $fechas = collect([Carbon::now()->format('Y-m-d')]);
        }


        $total = $fechas->count();
        $progressHelper = new ProgressBarHelper();
        $processed = 0;

        $controller = new ConsultaBandejaReferidosRecibidosRefconController();

        foreach ($fechas as $fecha) {
            $fechaFormato = $fecha->format('Ymd');
            $this->info("Consultando registros para {$fechaFormato}");

            $request = new Request(['FechaFiltro' => $fechaFormato]);
            $res = $controller->obtenerRegistrosReferencias($request);

            if ($res['success']) {
                foreach ($res['registros'] as $row) {
                    $paciente = $row['data']['paciente'];
                    $datosReferencia = $row['data']['datos_referencia'];

                    try {
                        WebSBandejaReferido::query()->updateOrCreate([
                            'NumeroDocumento' => $paciente['numero_documento'] ?? null,
                            'NumeroReferencia' => $datosReferencia['numero_referencia'] ?? null,
                            'IdReferencia' => $datosReferencia['id_referencia'] ?? null,
                        ], [
                            'TipoDocumento' => $paciente['tipo_documento'] ?? null,
                            'Nombres' => $paciente['nombres'] ?? null,
                            'PrimerApellido' => $paciente['primer_apellido'] ?? null,
                            'SegundoApellido' => $paciente['segundo_apellido'] ?? null,
                            'Sexo' => $paciente['sexo'] ?? null,
                            'Direccion' => $paciente['direccion'] ?? null,
                            'Ubigeo' => $paciente['ubigeo'] ?? null,
                            'Celular' => $paciente['celular'] ?? null,
                            'Especialidad' => $datosReferencia['especialidad'] ?? null,
                            'Condicion' => $datosReferencia['condicion'] ?? null,
                            'FechaReferencia' => !empty($datosReferencia['fecha_referencia']) ? Carbon::createFromFormat('d/m/Y', $datosReferencia['fecha_referencia']) : null,
                            'HoraReferencia' => $datosReferencia['hora_referencia'] ?? null,
                            'TipoTransporte' => $datosReferencia['tipo_transporte'] ?? null,
                            'ServicioOrigen' => $datosReferencia['servicio_origen'] ?? null,
                            'CodigoEstablecimientoOrigen' => $datosReferencia['codigo_establecimiento_origen'] ?? null,
                            'EstablecimientoOrigen' => $datosReferencia['establecimiento_origen'] ?? null,
                            'ServicioDestino' => $datosReferencia['servicio_destino'] ?? ($datosReferencia['servicio_detino'] ?? null), // fallback por si viene mal escrito
                            'FechaEnvio' => !empty($datosReferencia['fecha_envio']) ? Carbon::createFromFormat('d/m/Y', $datosReferencia['fecha_envio']) : null,
                            'ResumeAnamnesis' => $datosReferencia['resume_anamnesis'] ?? null,
                            'ResumeExfisico' => $datosReferencia['resume_exfisico'] ?? null,
                            'MotivoReferencia' => $datosReferencia['motivo_referencia'] ?? null,
                            'FechaAceptacion' => !empty($datosReferencia['fecha_aceptacion']) ? Carbon::createFromFormat('d/m/Y', $datosReferencia['fecha_aceptacion']) : null,
                            'Trama' => $row['data'] ?? null
                        ]);

                        $status = 'success';
                        $message = "Fecha: {$fecha->format('Y-m-d')}";
                    } catch (\Throwable $e) {
                        $status = 'error';
                        $message = "Error: {$e->getMessage()} | Fecha: {$fecha->format('Y-m-d')}";
                        $progressHelper->render(++$processed, $total, $status, $message);
                        continue;
                    }
                }

                $status = 'success';
                $message = "Fecha: {$fecha->format('Y-m-d')}";
            } else {
                $status = 'error';
                $message = "{$res['message']} | Fecha: {$fecha->format('Y-m-d')}";
            }

            DB::table('Referencias')
                ->whereDate('FechaEnvio', $fecha->format('Y-m-d'))
                ->where('EstadoReferencia', 'ACEPTADO')
                ->update(['TieneTrama' => 'S']);

            if (!$fecha->isToday()) {
                DB::table('FechasReferenciaProcesadas')->updateOrInsert(
                    ['FechaEnvio' => $fecha->format('Y-m-d')],
                    ['Observacion' => 'Procesada correctamente']
                );
            }

            $processed++;
            $progressHelper->render($processed, $total, $status, $message);
        }

        $this->info('Proceso terminado: ' . now());
    }
}
