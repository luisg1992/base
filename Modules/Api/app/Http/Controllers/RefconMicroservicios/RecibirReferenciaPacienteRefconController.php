<?php

namespace Modules\Api\Http\Controllers\RefconMicroservicios;

use ErrorException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Modules\Persona\Models\Empleado;
use Modules\ProgramacionGeneral\Models\ProgramacionMedica;
use Throwable;

class RecibirReferenciaPacienteRefconController extends Controller
{
    protected $data;

    public function obtenerRegistros()
    {
        $this->data = [];
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
                'ref.IdReferenciaRefCon',
                'c.Fecha as CitaFecha',
                'c.HoraInicio as CitaHoraInicio',
                'c.IdProgramacion'
            )
            ->take(100)
            ->chunkById(20, function ($records) {

                foreach ($records as $record) {
                    $programacionMedica = ProgramacionMedica::query()
                        ->with('medico', 'medico.empleado', 'servicio', 'turno')
                        ->where('IdProgramacion', $record->IdProgramacion)
                        ->first();

                    $usuarioRegistro = Empleado::query()->find($programacionMedica->medico->IdEmpleado);

                    $this->data[] = [
                        'IdReferenciaRefCon' => $record->IdReferenciaRefCon,
                        'IdReferencia' => $record->IdReferencia,
                        'IdAtencionReferencia' => $record->IdAtencionReferencia,
                        'IdAtencion' => $record->IdAtencion,
                        'IdcuentaAtencion' => $record->IdcuentaAtencion,
                        'data' => [
                            'codigoRenipressDestino' => '6206',
                            'condicionPaciente' => 'E',
                            'fechaCita' => Carbon::parse($record->CitaFecha)->format('Y-m-d'),
                            'horaCita' => $record->CitaHoraInicio,
                            'idReferencia' => $record->IdReferenciaRefCon,
                            'llegoPaciente' => 'S',
                            'personalRegistra' => [
                                'apellidoMaterno' => $usuarioRegistro->ApellidoPaterno,
                                'apellidoPaterno' => $usuarioRegistro->ApellidoMaterno,
                                'fechaNacimiento' => Carbon::parse($usuarioRegistro->FechaNacimiento)->format('Ymd'),
                                'idcolegio' => '01', //$usuarioRegistro->idcolegio,
                                'idprofesion' => '01', //$usuarioRegistro->idprofesion,
                                'nombres' => $usuarioRegistro->Nombres,
                                'nroDocumento' => trim($usuarioRegistro->DNI),
                                'sexo' => match (true) {
                                    $usuarioRegistro->idSexo === 1 => 'M',
                                    $usuarioRegistro->idSexo === 2 => 'F',
                                    $usuarioRegistro->sexo === '1' => 'M',
                                    $usuarioRegistro->sexo === '2' => 'F',
                                    default => 'M', // por defecto
                                },
                                'tipoDocumento' => $usuarioRegistro->idTipoDocumento,
                            ]
                        ]
                    ];
                }
            }, 'IdAtencionReferencia');

        return $this->data;
    }

    public function enviar($data)
    {
        try {
            $url = "https://servicios.minsa.gob.pe/mcs-servicios-refcon/servicio/v1.0.0/recibirReferenciaPaciente";
            $response = Http::withHeaders([
                'username' => config('services.mefMicro_Produccion.username'),
                'password' => config('services.mefMicro_Produccion.password'),
                'ipclient' => config('services.mefMicro_Produccion.ipclient'),
            ])->post($url, $data);

            $response->throw();

            $res = $response->json();
            if ($res['codigo'] === '0000') {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $res['mensaje'],
                'data' => $response->json()
            ]);
        } catch (ConnectionException | RequestException | ErrorException | Exception $e) {
            return response()->json([
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
