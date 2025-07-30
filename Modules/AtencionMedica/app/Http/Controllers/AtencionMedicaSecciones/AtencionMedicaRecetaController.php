<?php

namespace Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Imagenologia\Models\RecetaCabecera;

class AtencionMedicaRecetaController extends Controller
{
    public function obtenerDetalleReceta($IdCuentaAtencion, $IdPuntoCarga)
    {
        return DB::select('EXEC WebS_RecetaBuscarPorCuenta ?, ?', [$IdCuentaAtencion, $IdPuntoCarga]);
    }

    /*REGISTRO DE RECETA DE ATENCIONES*/
    public function WebS_RecetaCabeceraAgregar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdCuentaAtencion' => 'required|integer',
            'IdPuntoCarga' => 'required|integer',
            'IdServicioIngreso' => 'required|integer',
            'IdMedicoIngreso' => 'required|integer'
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
                'EXEC WebS_RecetaCabeceraAgregar 
                    @IdCuentaAtencion = ?, 
                    @IdPuntoCarga = ?, 
                    @IdServicioReceta = ?, 
                    @IdMedicoReceta = ?, 
                    @IdUsuarioAuditoria = ?',
                [
                    $request->IdCuentaAtencion,
                    $request->IdPuntoCarga,
                    $request->IdServicioIngreso,
                    $request->IdMedicoIngreso,
                    Auth::user()->IdEmpleado
                ]
            );

            if (!empty($resultado)) {
                if ($resultado[0]->Codigo == 'OK') {
                    $IdReceta = $resultado[0]->IdReceta;

                    // Agregar IdReceta al request
                    $request->merge(['IdReceta' => $IdReceta]);
                    $detalleResponse = $this->WebS_RecetaDetalleAgregar($request);
                    $detalleData = $detalleResponse->getData();

                    // Validar resultado del detalle
                    if ($detalleData->success) {
                        return response()->json([
                            'success' => true,
                            'codigo' => 'OK',
                            'mensaje' => 'Cabecera y detalle registrados correctamente',
                            'IdReceta' => $IdReceta,
                            'data' => $request->all()
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'codigo' => $detalleData->codigo ?? 'ERROR',
                            'mensaje' => $detalleData->mensaje ?? 'Error al insertar el detalle',
                            'IdReceta' => null,
                            'data' => null
                        ]);
                    }
                } else {
                    $IdReceta = $resultado[0]->IdReceta;

                    // Agregar IdReceta al request
                    $request->merge(['IdReceta' => $IdReceta]);
                    $detalleResponse = $this->WebS_RecetaDetalleAgregar($request);
                    $detalleData = $detalleResponse->getData();

                    // Validar resultado del detalle
                    if ($detalleData->success) {
                        return response()->json([
                            'success' => true,
                            'codigo' => 'OK',
                            'mensaje' => 'Cabecera y detalle registrados correctamente',
                            'IdReceta' => $IdReceta,
                            'data' => $request->all()
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'codigo' => $detalleData->codigo ?? 'ERROR',
                            'mensaje' => $detalleData->mensaje ?? 'Error al insertar el detalle',
                            'IdReceta' => null,
                            'data' => null
                        ]);
                    }
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

    public function WebS_RecetaDetalleAgregar(Request $request)
    {  
        if (!$request->IdReceta) {
            $IdReceta = RecetaCabecera::obtenerIdRecetaPorDetalle(
                $request->IdCuentaAtencion,
                $request->IdPuntoCarga,
                $request->IdProducto,
                trim($request->CodigoDx)
            );

            if (!$IdReceta) {
                return response()->json([
                    'success' => false,
                    'codigo' => 'ERROR',
                    'mensaje' => 'No se encontró una receta para la cuenta y punto de carga proporcionados.',
                    'data' => null
                ]);
            }
            $request->merge(['IdReceta' => $IdReceta]);
        }

        $validator = Validator::make($request->all(), [
            'IdReceta' => 'required|integer',
            'IdProducto' => 'required|integer',
            'CantidadPedida' => 'required|min:1',
            'Precio' => 'required',
            'SaldoEnRegistroReceta' => 'required',
            'Observaciones' => 'nullable|string|max:300',
            'IdDiagnostico' => 'required|integer',
            'CodigoDx' => 'required|string|max:7',
            'Insertar' => 'required'
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
                'EXEC WebS_RecetaDetalleAgregar 
                    @IdReceta = ?, 
                    @IdProducto = ?, 
                    @CantidadPedida = ?, 
                    @Duracion = ?, 
                    @Precio = ?, 
                    @SaldoEnRegistroReceta = ?, 
                    @IdDosisRecetada = ?, 
                    @Observaciones = ?, 
                    @IdViaAdministracion = ?, 
                    @IdRecetaDosisUnidadMedida = ?, 
                    @IdRecetaFrecuencia = ?, 
                    @CodigoDx = ?, 
                    @IdDiagnostico = ?,
                    @IdUsuarioAuditoria = ?, 
                    @Insertar = ?',
                [
                    $request->IdReceta,
                    $request->IdProducto,
                    $request->CantidadPedida,
                    $request->Duracion ?? 0,
                    $request->Precio ?? 0,
                    $request->SaldoEnRegistroReceta,
                    $request->IdDosis,
                    $request->Observaciones,
                    $request->IdViaAdministracion,
                    $request->IdRecetaDosisUnidadMedida,
                    $request->IdFrecuencia,
                    $request->CodigoDx,
                    $request->IdDiagnostico,
                    Auth::user()->IdEmpleado,
                    $request->Insertar
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
                        'codigo' => 'ERROR',
                        'mensaje' => 'NO SE OBTUVO RESPUESTA DEL SERVIDOR PRINCIPAL',
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

    public function WebS_RecetaDetalle_Eliminar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdCuentaAtencion' => 'required|integer',
            'IdProducto' => 'required|integer',
            'IdServicioIngreso' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $validator->errors()
            ]);
        }

        $IdReceta = null;
        if (!$request->IdReceta) {
            $detalle = RecetaCabecera::where('IdCuentaAtencion', $request->IdCuentaAtencion)
                ->where('IdPuntoCarga', $request->IdPuntoCarga)
                ->where('idServicioReceta', $request->IdServicioIngreso)
                ->where('idEstado', 1)
                ->first();
            $IdReceta =  $detalle->IdReceta;
        } else {
            $IdReceta =  $request->IdReceta;
        }

        $resultado = DB::select(
            'EXEC WebS_RecetaDetalle_Eliminar @IdReceta = ?, @IdProducto = ?',
            [
                $IdReceta,
                $request->IdProducto
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
    }
}
