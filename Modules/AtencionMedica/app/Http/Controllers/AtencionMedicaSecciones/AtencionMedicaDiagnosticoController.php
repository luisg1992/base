<?php

namespace Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AtencionMedicaDiagnosticoController extends Controller
{
    public function obtenerDetalleDiagnostico($IdAtencion)
    {
        return DB::select('EXEC WebS_Atenciones_Diagnostico_Consultar ?', [$IdAtencion]);
    }

    public function WebS_ListarDiagnosticosAtencion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdAtencion' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $validator->errors()
            ]);
        }

        $diagnosticos = $this->obtenerDetalleDiagnostico($request->IdAtencion);
        return response()->json([
            'success' => true,
            'codigo' => 'OK',
            'mensaje' => 'Diagnósticos obtenidos correctamente.',
            'data' => $diagnosticos
        ]);
    }

    /*REGISTRO DE DIAGNÓSTICO DE ATENCIONES*/
    public function WebS_InsertarDiagnosticoAtencion(Request $request)
    {
        // Validar campos obligatorios
        $validator = Validator::make($request->all(), [
            'IdAtencion' => 'required|integer',
            'IdDiagnostico' => 'required|integer',
            'IdClasificacionDx' => 'required|integer',
            'IdSubclasificacionDx' => 'required|integer',
            'GrupoHIS' => 'required|integer',
            'SubGrupoHIS' => 'required|integer',
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
                'EXEC WebS_Atenciones_Diagnostico_Modificar
                @IdAtencion = ?,
                @IdDiagnostico = ?,
                @IdClasificacionDx = ?,
                @IdSubclasificacionDx = ?,
                @labConfHIS = ?,
                @labConfHIS_2 = ?,
                @labConfHIS_3 = ?,
                @GrupoHIS = ?,
                @SubGrupoHIS = ?',
                [
                    (int)$request->IdAtencion,
                    $request->IdDiagnostico,
                    $request->IdClasificacionDx,
                    $request->IdSubclasificacionDx,
                    $request->labConfHIS,
                    $request->labConfHIS_2,
                    $request->labConfHIS_3,
                    $request->GrupoHIS,
                    $request->SubGrupoHIS
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


    public function WebS_EliminarDiagnosticoAtencion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdAtencion' => 'required|integer',
            'IdDiagnostico' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $validator->errors()
            ]);
        }

        try {
            // Ejecutar procedimiento almacenado
            $resultado = DB::select(
                'EXEC WebS_Atenciones_Diagnostico_Eliminar @IdAtencion = ?, @IdDiagnostico = ?',
                [
                    (int)$request->IdAtencion,
                    (int)$request->IdDiagnostico
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
                'codigo' => 'EXCEPTION',
                'mensaje' => $e->getMessage(),
                'data' => null
            ]);
        }
    }
}
