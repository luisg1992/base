<?php

namespace App\Http\Controllers\Api\service_printer\consulta_externa\cita;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\ConsultaExterna\Http\Controllers\CitasController;

class ApiServicePrinterCitaFua extends Controller
{
    public function service_printer_cita_fua(Request $request)
    {
        $valoresJson = $request->valores;
        if (is_string($valoresJson)) {
            $valoresArray = json_decode($valoresJson, true);
        } else {
            $valoresArray = $valoresJson;
        }

        // Verificamos si existe el campo 'IdCita'
        $IdCita = 0;
        $Formato = '';
        $Usuario = '';
        if (isset($valoresArray['IdCita']) && isset($valoresArray['Formato']) && isset($valoresArray['Usuario'])) {
            $IdCita = $valoresArray['IdCita'];
            $Formato = $valoresArray['Formato'];
            $Usuario = $valoresArray['Usuario'];
        } else {
            return response()->json([
                'respuesta' => false,
                'data' => [
                    'message' => '.:: NO SE PUDO OBTENER LOS PARAMETROS NECESARIOS PARA GENERAR EL PROCESO DE IMPRESIÓN ::.'
                ]
            ], 400);
        }

        // Realizamos la búsqueda de impresoras según el servicio
        $A4 = DB::select("EXEC WebS_TerminalImpresoraFormato '" . $request->servicio_printer . "', '" . $Formato . "', 1");
        if (empty($A4) || !isset($A4[0])) {
            return response()->json([
                'respuesta' => false,
                'data' => [
                    'message' => '.:: NO SE PUDO UBICAR LA IMPRESORA ' . $request->servicio_printer . ', NO SE ENCUENTRA CONFIGURADA EL EL SISTEMA.'
                ]
            ], 400);
        }

        // Inicializamos la variable para el estado del FUA
        $codigo_Cita_Medica_Fua_A4 = 'ERROR';
        $nombreArchivo_Cita_Medica_Fua_A4 = $IdCita . '_FUA.pdf';

        // Creamos los datos como un array
        $Parametros = [
            'IdCita' => $IdCita,
            'Formato' => $Formato,
            'Usuario' => $Usuario,
            'TerminalLogin' => $request->servicio_printer
        ];

        // Instanciamos el controlador que contiene la función
        $citasController = new CitasController();
        $response = $citasController->WebS_AdmisionCitasFormatoFua(new Request($Parametros));
        $responseJson = $response->getContent();
        $responseData = json_decode($responseJson, true);


        if ($responseData['success']) {
            $codigo_Cita_Medica_Fua_A4 = 'OK';
            $Cita_Medica_Fua = array(
                'codigo' => $codigo_Cita_Medica_Fua_A4,
                'ImpresionPorDefecto' => $A4[0]->PorDefecto,
                'NombreImpresora' => $A4[0]->Impresora,
                'Formato' => $A4[0]->Formato,
                'nombre_pdf' => $nombreArchivo_Cita_Medica_Fua_A4,
                'type' => 'pdf',
                'validate' => '1',
                'pdf' => $responseData['pdf_base64']
            );

            // Devolvemos la respuesta con el ticket generado
            return response()->json([
                'respuesta' => true,
                'data' => [
                    'Cita_Medica_Fua' => $Cita_Medica_Fua
                ]
            ], 202);
        } else {
            // Instanciamos el controlador que contiene la función 
            $responseValidacion = $citasController->WebS_GenerarFormatoFua(new Request($Parametros));
            $responseValidacionJson = $responseValidacion->getContent();
            $responseValidacionData = json_decode($responseValidacionJson, true);

            if ($responseValidacionData['success']) {
                $responsePDF = $citasController->WebS_AdmisionCitasFormatoFua(new Request($Parametros));
                $responsePDFJson = $responsePDF->getContent();
                $responsePDFData = json_decode($responsePDFJson, true);


                if ($responsePDFData['success']) {
                    $codigo_Cita_Medica_Fua_A4 = 'OK';
                    $Cita_Medica_Fua = array(
                        'codigo' => $codigo_Cita_Medica_Fua_A4,
                        'ImpresionPorDefecto' => $A4[0]->PorDefecto,
                        'NombreImpresora' => $A4[0]->Impresora,
                        'Formato' => $A4[0]->Formato,
                        'nombre_pdf' => $nombreArchivo_Cita_Medica_Fua_A4,
                        'type' => 'pdf',
                        'validate' => '2',
                        'pdf' => $responsePDFData['pdf_base64']
                    );

                    // Devolvemos la respuesta con el ticket generado
                    return response()->json([
                        'respuesta' => true,
                        'data' => [
                            'Cita_Medica_Fua' => $Cita_Medica_Fua
                        ]
                    ], 202);
                }
            }
        }

        return response()->json([
            'respuesta' => false,
            'data' => [
                'message' => $responseData['mensaje'],
            ]
        ], 400);
    }
}
