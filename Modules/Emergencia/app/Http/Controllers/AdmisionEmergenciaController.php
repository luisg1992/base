<?php

namespace Modules\Emergencia\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Emergencia\DataTables\AdmisionEmergenciaDataTable;
use Modules\Emergencia\Http\Requests\AdmisionEmergenciaRequest;
use Modules\Emergencia\Http\Resources\AdmisionEmergenciaResource;
use Modules\Emergencia\Models\Atencion;
use Modules\Emergencia\Models\TriajeEmergencia;

class AdmisionEmergenciaController extends Controller
{
    use AdmisionEmergenciaDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Emergencia/AdmisionEmergencia/AdmisionEmergenciaIndex');
    }

    public function show($id)
    {
//        WebS_AdmisionEmergenciaHospitalizacion_Consultar(
//            @IdCuentaAtencion Int

        $data = DB::select(
            'EXEC WebS_AdmisionEmergenciaHospitalizacion_Consultar @IdAtencion = ?',
            [$id]
        );

//        dd($data);

        //return response()->json(['data'=>$data[0];
        return new AdmisionEmergenciaResource($data[0]);

//        dd($data);

//        $record = Atencion::query()->find($id);
//        if (!$record) {
//            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
//        }
//        return $data new AdmisionEmergenciaResource($record);
    }

    public function store(AdmisionEmergenciaRequest $request)
    {
        $resultado = DB::select(
            'EXEC WebS_AdmisionEmergenciaHospitalizacion_Insertar ' .
            '@IdPaciente = ?, ' .
            '@IdUsuario = ?, ' .
            '@nombrePC = ?, ' .
            '@IdOrigenAtencion = ?, ' .
            '@IdServicioIngreso = ?, ' .
            '@IdFuenteFinanciamiento = ?, ' .
            '@IdTipoFinanciamiento = ?, ' .
            '@IdMedicoIngreso = ?, ' .
            '@IdTipoGravedad = ?, ' .
            '@IdCausaExternaMorbilidad = ?, ' .
            '@IdLugarEvento = ?, ' .
            '@IdTipoEvento = ?, ' .
            '@IdSeguridad = ?, ' .
            '@IdRelacionAgresorVictima = ?, ' .
            '@IdClaseAccidente = ?, ' .
            '@IdTipoVehiculo = ?, ' .
            '@IdTipoTransporte = ?, ' .
            '@IdUbicacionLesionado = ?, ' .
            '@IdPosicionLesionadoALAB = ?, ' .
            '@IdGrupoOcupacionalALAB = ?, ' .
            '@IdTipoAgenteAGAN = ?, ' .
            '@NombreAcompaniante = ?, ' .
            '@TelefonoAcompaniante = ?, ' .
            '@NumeroReferencia = ?, ' .
            '@FechaIngreso = ?, ' .
            '@HoraIngreso = ?, ' .
            '@RecienNacido = ? ',
            [
                $request->IdPaciente,
                Auth::user()->IdEmpleado,
                'PC',
                $request->IdOrigenAtencion,
                $request->IdServicioIngreso,
                $request->IdFuenteFinanciamiento,
                $request->IdTipoFinanciamiento,
                $request->IdMedicoIngreso,
                $request->IdTipoGravedad,
                $request->IdCausaExternaMorbilidad,
                $request->IdLugarEvento,
                $request->IdTipoEvento,
                $request->IdSeguridad,
                $request->IdRelacionAgresorVictima,
                $request->IdClaseAccidente,
                $request->IdTipoVehiculo,
                $request->IdTipoTransporte,
                $request->IdUbicacionLesionado,
                $request->IdPosicionLesionadoALAB,
                $request->IdGrupoOcupacionalALAB,
                $request->IdTipoAgenteAGAN,
                $request->NombreAcompaniante,
                $request->TelefonoAcompaniante,
                $request->NumeroReferencia,
                \Illuminate\Support\Carbon::parse($request->FechaIngreso)->format('Y-m-d'),
                $request->HoraIngreso,
                $request->RecienNacido,
            ]);

        if (!empty($resultado)) {
            if ($resultado[0]->Codigo == 'OK') {
                foreach ($request->diagnosticos as $diagnostico) {
                    DB::select(
                        'EXEC WebS_AdmisionEmergenciaHospitalizacion_Insertar_Diagnostico ' .
                        '@IdPaciente = ?, ' .
                        '@IdAtencion = ?, ' .
                        '@IdDiagnostico = ?, ' .
                        '@IdUsuario = ? ',
                        [
                            $request->IdPaciente,
                            $resultado[0]->IdAtencion,
                            $diagnostico['IdDiagnostico'],
                            Auth::user()->IdEmpleado,
                        ]);
                }

                Atencion::query()->where('IdAtencion', $resultado[0]->IdAtencion)->update([
                    'IdTriajeEmergencia' => $request->IdTriajeEmergencia,
                ]);

                TriajeEmergencia::query()->where('IdTriajeEmergencia', $request->IdTriajeEmergencia)->update([
                    'IdAtencion' => $resultado[0]->IdAtencion,
                ]);

                return response()->json([
                    'success' => true,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                    'IdAtencion' => $resultado[0]->IdAtencion,
                    'IdCuentaAtencion' => $resultado[0]->IdCuentaAtencion,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                    'IdAtencion' => $resultado[0]->IdAtencion ?? null,
                    'IdCuentaAtencion' => $resultado[0]->IdCuentaAtencion ?? null,
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
            ]);
        }
    }

    public function recordDestroy($id)
    {
        $record = new AdmisionEmergenciaResource(Atencion::query()->findOrFail($id));
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

        try {
            $record = Atencion::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new AdmisionEmergenciaResource(Atencion::query()->findOrFail($id));
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
            $record = Atencion::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->CodigoTriaje . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function storeNoIdentificado(Request $request)
    {
        $resultado = DB::select(
            'EXEC WebS_PacienteNoIdentificadoInsertar ' .
            '@IdSexo = ?, ' .
            '@IdEdadCategoria = ? ',
            [
                $request->IdSexo,
                $request->IdEdadCategoria,
            ]);

        if (!empty($resultado)) {
            if ($resultado[0]->Codigo == 'OK') {
                return response()->json([
                    'success' => true,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                    'IdPaciente' => $resultado[0]->IdPaciente,
                    'TipoDocumento' => (int)$resultado[0]->TipoDocumento,
                    'NumeroDocumento' => $resultado[0]->NumeroDocumento,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                    'IdPaciente' => $resultado[0]->IdPaciente ?? null,
                    'TipoDocumento' => $resultado[0]->TipoDocumento ?? null,
                    'NumeroDocumento' => $resultado[0]->NumeroDocumento ?? null,
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
            ]);
        }
    }

    public function buscarCuentasPendientes($IdPaciente)
    {
        $resultado = DB::select(
            'EXEC WebS_Validar_Cuentas_Pendientes ' .
            '@IdPaciente = ? ',
            [
                $IdPaciente,
            ]);

        if (!empty($resultado)) {
            if ($resultado[0]->Codigo == 'OK') {
                return response()->json([
                    'success' => true,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => $resultado[0]->Codigo,
                    'mensaje' => $resultado[0]->Mensaje,
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
            ]);
        }
    }
}
