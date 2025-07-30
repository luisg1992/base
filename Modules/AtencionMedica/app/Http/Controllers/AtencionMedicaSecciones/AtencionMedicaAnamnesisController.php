<?php

namespace Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AtencionMedicaAnamnesisController extends Controller
{
    public function WebS_InsertarAnamnesisAtencion(Request $request)
    {
        // Validar campos obligatorios
        $validator = Validator::make($request->all(), [
            'IdAtencion' => 'required|integer',
            'IdPaciente' => 'required|integer',
            'NroHistoriaClinica' => 'nullable|string',
            'Antecedentes_Quirugicos' => 'nullable|string|max:1000',
            'Antecedentes_Patologicos' => 'nullable|string',
            'Antecedentes_Obstetricos' => 'nullable|string',
            'Antecedentes_Alergias' => 'nullable|string',
            'Antecedentes_Familiares' => 'nullable|string',
            'Antecedentes_Otros' => 'nullable|string',
            'Relato_Cronologico' => 'nullable|string',
            'Funciones_Biologicas' => 'nullable|string',
            'Tiempo_Enfermedad' => 'nullable|string',
            'Motivo_Consulta' => 'nullable|string',
            'Examen_General' => 'nullable|string',
            'Examen_Regional' => 'nullable|string',
            'CitaTratamiento' => 'nullable|string',
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
                'EXEC WebS_Atenciones_Anamnesis_Modificar 
                    @IdAtencion = ?, 
                    @IdPaciente = ?, 
                    @NroHistoriaClinica = ?, 
                    @Antecedentes_Quirugicos = ?, 
                    @Antecedentes_Patologicos = ?, 
                    @Antecedentes_Obstetricos = ?, 
                    @Antecedentes_Alergias = ?, 
                    @Antecedentes_Familiares = ?, 
                    @Antecedentes_Otros = ?, 
                    @Relato_Cronologico = ?, 
                    @Funciones_Biologicas = ?, 
                    @Tiempo_Enfermedad = ?, 
                    @Motivo_Consulta = ?, 
                    @Examen_General = ?, 
                    @Examen_Regional = ?, 
                    @CitaTratamiento = ?',
                [
                    $request->IdAtencion,
                    $request->IdPaciente,
                    $request->NroHistoriaClinica,
                    $request->Antecedentes_Quirugicos,
                    $request->Antecedentes_Patologicos,
                    $request->Antecedentes_Obstetricos,
                    $request->Antecedentes_Alergias,
                    $request->Antecedentes_Familiares,
                    $request->Antecedentes_Otros,
                    $request->Relato_Cronologico,
                    $request->Funciones_Biologicas,
                    $request->Tiempo_Enfermedad,
                    $request->Motivo_Consulta,
                    $request->Examen_General,
                    $request->Examen_Regional,
                    $request->CitaTratamiento
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
