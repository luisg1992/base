<?php

namespace Modules\Emergencia\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Emergencia\Models\AtencionDiagnostico;
use Modules\Emergencia\Models\TriajeEmergencia;
use Modules\Emergencia\DataTables\TriajeEmergenciaDataTable;
use Modules\Emergencia\Http\Requests\TriajeEmergenciaRequest;
use Modules\Emergencia\Http\Resources\TriajeEmergenciaResource;
use Modules\Persona\Models\Medico;
use Modules\Persona\Models\Paciente;
use Throwable;

class TriajeEmergenciaController extends Controller
{
    use TriajeEmergenciaDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Emergencia/TriajeEmergencia/TriajeEmergenciaIndex');
    }

    public function show($id)
    {
        $record = TriajeEmergencia::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TriajeEmergenciaResource($record);
    }

    public function store(TriajeEmergenciaRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $user = Auth::user();

            $data['TriajeFecha'] = now()->format('Y-m-d H:i:s');
            $data['TriajeHora'] = now()->format('Y-m-d H:i:s');
            $data['EstablecimientoSalud'] = !empty($data['EstablecimientoSalud'])
                ? substr($data['EstablecimientoSalud'], -5)
                : null;

            // Obtener ID del paciente
            $paciente = Paciente::query()->find($request->IdPaciente);
            $paciente->update([
                'Telefono' => $request->Telefono,
                'Email' => $request->Email,
            ]);
            Cache::forget("paciente_{$request->IdTipoDocTriaje}_{$request->NroDocTriaje}");

            // Obtener ID del médico desde el empleado autenticado
            $medico = Medico::where('IdEmpleado', $user->IdEmpleado)->first();
            $data['IdMedicoTriaje'] = $medico?->IdMedico;
            $data['IdEmpleado'] = $user->IdEmpleado ?? null;
            $data['Estacion'] = $user->TerminalLogin ?? null;

            if (!empty($request->id)) {
                $record = TriajeEmergencia::query()->findOrFail($request->id);
                $codigo = str_pad($record->IdTriajeEmergencia, 10, '0', STR_PAD_LEFT);
                $codigo = substr($codigo, -10);
                $data['CodigoTriaje'] = 'T' . $codigo;
                unset($data['HoraInicio'], $data['HoraTermino']);
                $record->update($data);
            } else {
                $nextId = DB::table('TriajeEmergencia')->max('IdTriajeEmergencia') + 1;
                $codigo = str_pad($nextId, 10, '0', STR_PAD_LEFT);
                $codigo = substr($codigo, -10);
                $data['CodigoTriaje'] = 'T' . $codigo;

                $record = TriajeEmergencia::query()->create($data);
            }

            AtencionDiagnostico::query()->where('IdTriajeEmergencia', $record->IdTriajeEmergencia)->delete();

            if($request->diagnosticos) {
                foreach ($request->diagnosticos as $diagnostico) {
                    DB::select(
                        'EXEC WebS_TriajeEmergenciaHospitalizacion_Insertar_Diagnostico ' .
                        '@IdPaciente = ?, ' .
                        '@IdTriajeEmergencia = ?, ' .
                        '@IdDiagnostico = ?, ' .
                        '@IdUsuario = ? ',
                        [
                            $request->IdPaciente,
                            $record->IdTriajeEmergencia,
                            $diagnostico['IdDiagnostico'],
                            Auth::user()->IdEmpleado,
                        ]);
                }
            }

            DB::commit();
            return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
        }catch (Throwable $e) {
            DB::rollBack();
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordDestroy($id)
    {
        $record = new TriajeEmergenciaResource(TriajeEmergencia::query()->findOrFail($id));
        return obtener_respuesta_eliminar_record(
            'ESTÁ SEGURO DE ELIMINAR EL ITEM SELECCIONADO: ' . $record->CodigoTriaje . '?',
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

        DB::beginTransaction();
        try {
            AtencionDiagnostico::query()->where('IdTriajeEmergencia', $request->input('id'))->delete();
            $record = TriajeEmergencia::query()->findOrFail($request->input('id'));
            $record->delete();
            DB::commit();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            DB::rollBack();
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TriajeEmergenciaResource(TriajeEmergencia::query()->findOrFail($id));
        return [
            'title' => 'ESTÁ SEGURO QUE SEA CAMBIAR EL ESTADO DEL REGISTRO SELECCIONADO: ' . $record->CodigoTriaje . '?',
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
            $record = TriajeEmergencia::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->CodigoTriaje . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function validarTriajeEmergenciaPaciente($pacienteId)
    {
        $record = TriajeEmergencia::query()
            ->where('IdPaciente', $pacienteId)
            ->where('Estado', 1)
            ->whereDate('TriajeFecha', '=', Carbon::now()->format('Y-m-d'))
            ->whereNull('IdAtencion')
            ->first();

        if ($record) {
            return [
                'success' => false,
                'mensaje' => 'EL PACIENTE CUENTA CON TRIAJE DE EMERGENCIA: ' . trim($record->CodigoTriaje),
                'id' => $record->IdTriajeEmergencia,
            ];
        }
        return [
            'success' => true,
            'mensaje' => 'NO SE ENCONTRO NINGUN TRIAJE DE EMERGENCIA EN EL DIA DE HOY',
        ];
    }

    public function validarTriajeEmergenciaPacientePorDocumento(Request $request)
    {
        $IdDocIdentidad = $request->IdDocIdentidad;
        $NroDocumento = $request->NroDocumento;
        $paciente = Paciente::query()
            ->where('IdDocIdentidad', $IdDocIdentidad)
            ->where('NroDocumento', $NroDocumento)
            ->first();
        if(!$paciente) {
            return [
                'success' => true,
                'mensaje' => 'NO SE ENCONTRO EL PACIENTE EN LA BASE DE DATOS',
            ];
        }

        return $this->validarTriajeEmergenciaPaciente( $paciente->IdPaciente);
//        return [
//            'success' => true,
//            'id' => $paciente->IdPaciente,
//        ];
    }
}
