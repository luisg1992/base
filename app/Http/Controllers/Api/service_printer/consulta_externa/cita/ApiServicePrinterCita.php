<?php

namespace App\Http\Controllers\Api\service_printer\consulta_externa\cita;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\ConsultaExterna\Http\Controllers\CitasController;

class ApiServicePrinterCita extends Controller
{
    public function service_printer_cita(Request $request)
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
        $Ticket = DB::select("EXEC WebS_TerminalImpresoraFormato '" . $request->servicio_printer . "', '" . $Formato . "', 1");
        if (empty($Ticket) || !isset($Ticket[0])) {
            return response()->json([
                'respuesta' => false,
                'data' => [
                    'message' => '.:: NO SE PUDO UBICAR LA IMPRESORA ' . $request->servicio_printer . ', NO SE ENCUENTRA CONFIGURADA EL EL SISTEMA.'
                ]
            ], 400);
        }

        // Inicializamos la variable para el estado del ticket
        $codigo_Cita_Medica_Ticket = 'ERROR';
        $nombreArchivo_Cita_Medica_Ticket = $IdCita . '_CMT.pdf';

        // Creamos los datos como un array
        $Parametros = [
            'IdCita' => $IdCita,
            'Formato' => $Formato,
            'Usuario' => $Usuario,
            'TerminalLogin' => $request->servicio_printer
        ];

        // Instanciamos el controlador que contiene la función
        $citasController = new CitasController();
        $response = $citasController->CitasBuscarIdCitaFormatoPDF(new Request($Parametros)); 
        $responseJson = $response->getContent();
        $responseData = json_decode($responseJson, true);

        if ($responseData['success']) {
            $codigo_Cita_Medica_Ticket = 'OK'; 
            $Cita_Medica_Ticket = array(
                'codigo' => $codigo_Cita_Medica_Ticket,
                'ImpresionPorDefecto' => $Ticket[0]->PorDefecto,
                'NombreImpresora' => $Ticket[0]->Impresora,
                'Formato' => $Ticket[0]->Formato,
                'nombre_pdf' => $nombreArchivo_Cita_Medica_Ticket,
                'type' => 'pdf',
                'pdf' => $responseData['pdf_base64']
            );

            // Devolvemos la respuesta con el ticket generado
            return response()->json([
                'respuesta' => true,
                'data' => [
                    'Cita_Medica_Ticket' => $Cita_Medica_Ticket
                ]
            ], 202);
        }

        return response()->json([
            'respuesta' => false,
            'data' => [
                'message' => $responseData['mensaje'],
            ]
        ], 400);
    }
}
