<?php

namespace Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AtencionMedicaInterconsultaController extends Controller
{

    public function obtenerDetalleInterconsulta($IdCuentaAtencion)
    {
        return DB::select('EXEC WebS_Atenciones_Interconsulta_Consultar ?', [$IdCuentaAtencion]);
    }

    public function WebS_Atenciones_Interconsulta_Insertar(Request $request)
    {
        // Validar campos obligatorios
        $validator = Validator::make($request->all(), [
            'IdAtencion' => 'required|integer',
            'IdEspecialidad' => 'required|integer',
            'Motivo' => 'required|string|max:500',
            'IdDiagnostico' => 'required|integer',
            'IdDiagnostico2' => 'nullable|integer',
            'IdDiagnostico3' => 'nullable|integer',
            'IdPaciente' => 'required|integer',
            'IdSolicitud' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $validator->errors()
            ]);
        }

        try {
            $resultado = DB::select(
                'EXEC WebS_Atenciones_Interconsulta_Insertar 
                    @IdAtencion = ?, 
                    @IdEspecialidad = ?, 
                    @Motivo = ?, 
                    @IdDiagnostico = ?, 
                    @IdDiagnostico2 = ?, 
                    @IdDiagnostico3 = ?, 
                    @IdPaciente = ?, 
                    @IdSolicitud = ?, 
                    @IdUsuarioAuditoria = ?',
                [
                    $request->IdAtencion,
                    $request->IdEspecialidad,
                    $request->Motivo,
                    $request->IdDiagnostico,
                    $request->IdDiagnostico2,
                    $request->IdDiagnostico3,
                    $request->IdPaciente,
                    $request->IdSolicitud,
                    Auth::user()->IdEmpleado
                ]
            );

            if (!empty($resultado)) {
                if ($resultado[0]->Codigo == 'OK') {
                    return response()->json([
                        'success' => true,
                        'codigo' => $resultado[0]->Codigo,
                        'mensaje' => $resultado[0]->Mensaje,
                        'data' => $request->all()
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'codigo' => $resultado[0]->Codigo,
                        'mensaje' => $resultado[0]->Mensaje,
                        'data' => null
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => 'ERROR',
                    'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
                    'data' => null
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'EXCEPTION',
                'mensaje' => $e->getMessage(),
                'data' => null
            ]);
        }
    }

    public function WebS_Atenciones_Interconsulta_Eliminar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdSolicitudEspecialidad' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $validator->errors()
            ]);
        }

        try {
            $resultado = DB::select(
                'EXEC WebS_Atenciones_Interconsulta_Eliminar @IdSolicitudEspecialidad = ?',
                [
                    $request->IdSolicitudEspecialidad
                ]
            );

            if (!empty($resultado)) {
                if ($resultado[0]->Codigo == 'OK') {
                    return response()->json([
                        'success' => true,
                        'codigo' => $resultado[0]->Codigo,
                        'mensaje' => $resultado[0]->Mensaje,
                        'data' => $request->all()
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'codigo' => $resultado[0]->Codigo,
                        'mensaje' => $resultado[0]->Mensaje,
                        'data' => null
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'codigo' => 'ERROR',
                    'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
                    'data' => null
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'codigo' => 'EXCEPTION',
                'mensaje' => $e->getMessage(),
                'data' => null
            ]);
        }
    }
}
