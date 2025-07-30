<?php

namespace Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AtencionMedicaProcedimientoController extends Controller
{
    public function obtenerDetalleProcedimiento($IdCuentaAtencion, $IdPuntoCarga)
    {
        return DB::select('EXEC WebS_ProcedimientoBuscarPorCuenta ?, ?', [$IdCuentaAtencion, $IdPuntoCarga]);
    }

    public function WebS_InsertarOrdenServicio(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdPuntoCarga' => 'required|integer',
            'IdPaciente' => 'required|integer',
            'IdCuentaAtencion' => 'required|integer',
            'IdTipoFinanciamiento' => 'required|integer',
            'IdFuenteFinanciamiento' => 'required|integer',
            'IdServicioIngreso' => 'required|integer',
            'IdProducto' => 'required|integer',
            'Precio' => 'required',
            'Cantidad' => 'required|integer',
            'IdDiagnostico' => 'required|integer',
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
                'EXEC WebS_FactOrdenServicio_Insertar 
                    @IdPuntoCarga = ?, 
                    @IdPaciente = ?, 
                    @IdCuentaAtencion = ?, 
                    @IdTipoFinanciamiento = ?, 
                    @IdFuenteFinanciamiento = ?, 
                    @IdServicio = ?, 
                    @IdProducto = ?, 
                    @Precio = ?, 
                    @Cantidad = ?, 
                    @IdHisSituacio_1 = ?, 
                    @IdHisSituacio_2 = ?, 
                    @IdHisSituacio_3 = ?, 
                    @IdDiagnostico = ?, 
                    @IdUsuarioAuditoria = ?',
                [
                    $request->IdPuntoCarga,
                    $request->IdPaciente,
                    $request->IdCuentaAtencion,
                    $request->IdTipoFinanciamiento,
                    $request->IdFuenteFinanciamiento,
                    $request->IdServicioIngreso,
                    $request->IdProducto,
                    $request->Precio,
                    $request->Cantidad,
                    $request->Lab1,
                    $request->Lab2,
                    $request->Lab3,
                    $request->IdDiagnostico,
                    Auth::user()->IdEmpleado
                ]
            );
 
            if (!empty($resultado)) {
                if ($resultado[0]->Codigo == 'OK') {
                    $datosCombinados = array_merge(
                        $request->all(),
                        [
                            'IdOrden' => $resultado[0]->IdOrden,
                            'IdProducto' => $resultado[0]->IdProducto,
                        ]
                    );

                    return response()->json([
                        'success' => true,
                        'codigo' => $resultado[0]->Codigo,
                        'mensaje' => $resultado[0]->Mensaje,
                        'data' => $datosCombinados,
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
                'codigo' => 'ERROR',
                'mensaje' => $e->getMessage(),
                'data' => null
            ]);
        }
    }

    public function WebS_ModificarOrdenServicio(Request $request)
    {
        // Validar campos obligatorios
        $validator = Validator::make($request->all(), [
            'IdCuentaAtencion' => 'required|integer',
            'IdOrden' => 'required|integer',
            'IdProducto' => 'required|integer',
            'Precio' => 'required',
            'Cantidad' => 'required|integer',
            'IdDiagnostico' => 'required|integer',
            'IdHisSituacio_1' => 'nullable|integer',
            'IdHisSituacio_2' => 'nullable|integer',
            'IdHisSituacio_3' => 'nullable|integer',
            'IdTipoFinanciamiento' => 'required|integer',
            'IdFuenteFinanciamiento' => 'required|integer'
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
                'EXEC WebS_FactOrdenServicio_Modificar 
                    @IdCuentaAtencion = ?, 
                    @IdOrden = ?, 
                    @IdProducto = ?, 
                    @Precio = ?, 
                    @Cantidad = ?, 
                    @IdDiagnostico = ?, 
                    @IdHisSituacio_1 = ?, 
                    @IdHisSituacio_2 = ?, 
                    @IdHisSituacio_3 = ?, 
                    @IdTipoFinanciamiento = ?, 
                    @IdFuenteFinanciamiento = ?, 
                    @IdUsuarioAuditoria = ?',
                [
                    $request->IdCuentaAtencion,
                    $request->IdOrden,
                    $request->IdProducto,
                    $request->Precio,
                    $request->Cantidad,
                    $request->IdDiagnostico,
                    $request->Lab1,
                    $request->Lab2,
                    $request->Lab3,
                    $request->IdTipoFinanciamiento,
                    $request->IdFuenteFinanciamiento,
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

    public function WebS_EliminarOrdenServicio(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdOrden' => 'required|integer',
            'IdFuenteFinanciamiento' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'codigo' => 'VALIDATION_ERROR',
                'mensaje' => $validator->errors()
            ]);
        }

        try {
            $resultado = DB::select(
                'EXEC WebS_FactOrdenServicio_Eliminar @IdOrden = ?, @IdUsuarioAuditoria = ?, @IdFuenteFinanciamiento = ?',
                [
                    (int)$request->IdOrden,
                    (int)$request->IdUsuarioAuditoria,
                    (int)$request->IdFuenteFinanciamiento
                ]
            );

            // Validar resultado
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
                'codigo' => 'ERROR_EXCEPTION',
                'mensaje' => 'Error al ejecutar procedimiento: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }
}
