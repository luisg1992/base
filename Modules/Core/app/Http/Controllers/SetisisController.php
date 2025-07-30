<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Api\ApiSetiSisController;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Api\Http\Controllers\Sms\SmsController;
use Modules\Core\DataTables\SetisisDataTable;
use Modules\Core\Http\Requests\SetisisRequest;
use Modules\Core\Http\Resources\SetisisResource;
use Modules\Core\Models\Setisis;
use Symfony\Component\Process\Process;
use Throwable;

class SetisisController extends Controller
{
    use SetisisDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Core/Setisis/SetisisIndex');
    }

    public function show($id)
    {
        $record = Setisis::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new SetisisResource($record);
    }

    public function store(SetisisRequest $request)
    {
        try {
            $data = $request->all();
            if (!empty($request->id)) {
                $record = Setisis::query()->findOrFail($request->id);
                $record->update($request->all());
            } else {
                $data['IdSetisis'] = 1 + (int)Setisis::query()->max('IdSetisis') ?? 0;
                $record = Setisis::query()->create($data);
            }

            return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordDestroy($id)
    {
        $record = new SetisisResource(Setisis::query()->findOrFail($id));
        return obtener_respuesta_eliminar_record(
            'ESTÁ SEGURO DE ELIMINAR EL ITEM SELECCIONADO: ' . $record->nombre . '?',
            true,
            $record
        );
    }

    public function destroy(Request $request)
    {
        $response = validacion_password($request);
        if (!$response['success']) {
            return $response;
        }

        try {
            $record = Setisis::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new SetisisResource(Setisis::query()->findOrFail($id));
        return [
            'title' => 'ESTÁ SEGURO QUE SEA CAMBIAR EL ESTADO DEL REGISTRO SELECCIONADO: ' . $record->Nombre . '?',
            'verify_password' => true,
            'record' => $record,
        ];
    }

    public function changeActive(Request $request)
    {
        $response = validacion_password($request);
        if (!$response['success']) {
            return $response;
        }

        try {
            $record = Setisis::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Nombre . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Throwable $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function generarPaquete(Request $request)
    {
        try {
            $fecha = Carbon::createFromFormat('d/m/Y', $request->input('fecha'));
            $connection = 'sqlsrvGalen';
            $procedures = [
                [
                    'procedure' => 'WebS_TramaFua_T1',
                    'filename' => 'ATENCION.TXT',
                    'cantidad' => 0,
                ],
                [
                    'procedure' => 'WebS_TramaFua_T5',
                    'filename' => 'ATENCIONSMI.TXT',
                    'cantidad' => 0,
                ],
                [
                    'procedure' => 'WebS_TramaFua_T2',
                    'filename' => 'ATENCIONDIA.TXT',
                    'cantidad' => 0,
                ],
                [
                    'procedure' => 'WebS_TramaFua_T4',
                    'filename' => 'ATENCIONMED.TXT',
                    'cantidad' => 0,
                ],
                [
                    'procedure' => 'WebS_TramaFua_T6',
                    'filename' => 'ATENCIONINS.TXT',
                    'cantidad' => 0,
                ],
                [
                    'procedure' => 'WebS_TramaFua_T7',
                    'filename' => 'ATENCIONPRO.TXT',
                    'cantidad' => 0,
                ],
                [
                    'procedure' => 'WebS_TramaFua_T9',
                    'filename' => 'ATENCIONSER.TXT',
                    'cantidad' => 0,
                ],
                [
                    'procedure' => 'WebS_TramaFua_T8',
                    'filename' => 'ATENCIONRN.TXT',
                    'cantidad' => 0,
                ],
                [
                    'procedure' => 'WebS_TramaFua_T10',
                    'filename' => 'ATENCIONTRA.TXT',
                    'cantidad' => 0,
                ],
                [
                    'procedure' => 'WebS_TramaFua_T11',
                    'filename' => 'ATENCIONVIA.TXT',
                    'cantidad' => 0,
                ],
                [
                    'procedure' => 'WebS_TramaFua_T12',
                    'filename' => 'ATENCIONOTRG.TXT',
                    'cantidad' => 0,
                ],
                [
                    'procedure' => 'WebS_TramaFua_T3',
                    'filename' => 'ATENCIONNOAFI.TXT',
                    'cantidad' => 0,
                ],
            ];

            $setisis = Setisis::query()
                ->where('Fecha', $fecha->format('d/m/Y'))
                ->orderByDesc('Numero')
                ->first();

            $count = 1;
            if ($setisis) {
                $count += $setisis->Numero;
            }

            $param = Carbon::parse($fecha)->format('d/m/Y');
            $param_array = explode('/', $param);
            $count_string = $param_array[0] . str_pad($count, 3, '0', STR_PAD_LEFT);
            $filename = '00006206' . $param_array[2] . $param_array[1] . $count_string;

            $folder = 'fua/' . $filename;
            if (Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->deleteDirectory($folder);
            }

            Storage::disk('public')->makeDirectory($folder);

            foreach ($procedures as $index => $t) {
                $sql = "EXEC {$t['procedure']} '{$param}'";
                $records = DB::connection($connection)->select($sql);
                $lines = [];
                $addLine = false;
                foreach ($records as $row) {
                    $values = (array)$row;
                    $firstColumn = reset($values);
                    if ($firstColumn !== null) {
                        $addLine = true;
                        $cleaned = array_map(function ($value) {
                            return is_null($value) ? '' : trim($value);
                        }, $values);
                        $lines[] = implode('|', $cleaned);
                    }
                }
                $procedures[$index]['cantidad'] = count($lines);
                $content = implode("\n", $lines);
                $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
                Storage::disk('public')->put($folder . '/' . $t['filename'], $content . ($addLine ? "\n" : ''));
            }

            $summary = [
                $param_array[2],
                $param_array[1],
                $count_string,
                $filename . '.zip',
                '0310',
                $procedures[0]['cantidad'],
                $procedures[1]['cantidad'],
                $procedures[2]['cantidad'],
                $procedures[3]['cantidad'],
                $procedures[4]['cantidad'],
                $procedures[5]['cantidad'],
                $procedures[6]['cantidad'],
                $procedures[7]['cantidad'],
                $procedures[8]['cantidad'],
                $procedures[9]['cantidad'],
                $procedures[10]['cantidad'],
                'SIS-GalenPLUS',
                'V6.2',
                'V0.1',
                '1',
                '06190091'
            ];

            $content = implode("\n", $summary);
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

            Storage::disk('public')->put($folder . '/RESUMEN.TXT', $content . "\n");

            $zip = $this->zipPassword($procedures, $folder, $filename);

            if ($zip['success']) {
                Setisis::query()->create([
                    'Fecha' => $fecha,
                    'Numero' => $count,
                    'PaqueteNumero' => $filename,
                    'UsuarioCreacion' => auth()->id(),
                    'FechaCreacion' => now(),
                    'UsuarioModificacion' => auth()->id(),
                    'FechaModificacion' => now(),
                    'Estado' => 1,
                ]);
                return [
                    'success' => true,
                    'message' => 'Paquete creado con exito',
                ];
            }
            return $zip;
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function zipPassword($procedures, $folder, $filename)
    {
        try {
            $sevenZip = '"C:\Program Files\7-Zip\7z.exe"';
            $files = [];

            foreach ($procedures as $t) {
                $files[] = Storage::disk('public')->path($folder . '/' . $t['filename']);
            }
            $files[] = Storage::disk('public')->path($folder . '/RESUMEN.TXT');

            $zip = Storage::disk('public')->path($folder . '/' . $filename . '.zip');
            $password = 'PilotoFUAE123';
            $filesArgument = implode(' ', array_map(fn($a) => "\"$a\"", $files));
            $command = "$sevenZip a -tzip -p$password -mem=AES256 \"$zip\" $filesArgument";
            $process = Process::fromShellCommandline($command);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException("Error al crear Zip: " . $process->getErrorOutput());
            }
            return [
                'success' => true,
                'message' => 'zip creado con exito',
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function enviarPaquete($id)
    {
        $api = (new ApiSetiSisController());
        $resLogin = $api->api_login();

        if ($resLogin['success']) {
            $token = $resLogin['data']['access_token'];
            $setisis = Setisis::query()->find($id);
            $zip = Storage::disk('public')->path('/fua/' . $setisis->PaqueteNumero . '/' . $setisis->PaqueteNumero . '.zip');
            $resEnvio = $api->api_enviar_paquete($token, $setisis->PaqueteNumero, base64_encode(file_get_contents($zip)));
            if (!$resEnvio['success']) {
                return [
                    'success' => false,
                    'message' => $resEnvio['message'],
                ];
            }
            return $resEnvio;
        }
        return $resLogin;
    }

    public function consultarPaquete($id)
    {
        $api = (new ApiSetiSisController());
        $resLogin = $api->api_login();

        if ($resLogin['success']) {
            $token = $resLogin['data']['access_token'];
            $setisis = Setisis::query()->find($id);
            $resConsulta = $api->api_consultar_paquete($token, $setisis->PaqueteNumero);
//            return $res_consulta;
//            dd($res_consulta);
            if ($resConsulta['success']) {
                $data = $resConsulta['data'];
                $setisis->update([
                    'Estado' => 3,
                    'Datos' => $data,
                    'FechaModificacion' => now(),
                    'UsuarioModificacion' => auth()->id(),
                ]);

//                $data2 = $data['data'];
//                $this->info('Estado :' . $data2['estado']);
//                foreach ($data2['observaciones'] as $obs) {
//                    foreach ($obs['errores'] as $err) {
//                        $this->info($obs['fua'] . ' - ' . $err);
//                    }
//                }
            }
            return $resConsulta;
        }
        return $resLogin;
    }
}
