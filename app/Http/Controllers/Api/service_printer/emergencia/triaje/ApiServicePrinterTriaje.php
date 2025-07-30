<?php

namespace App\Http\Controllers\Api\service_printer\emergencia\triaje;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Emergencia\TriajeEmergenciaController;

class ApiServicePrinterTriaje extends Controller
{
    public function service_printer_triaje(Request $request)
    {
        if ($request->user == config('services.sigesa_api.user') && $request->password == config('services.sigesa_api.password')) {
            $valoresJson = $request->valores;
            if (is_string($valoresJson)) {
                $valoresArray = json_decode($valoresJson, true);
            } else {
                $valoresArray = $valoresJson;
            }

            // Verificamos si existe el campo 'IdTriaje'
            $IdTriaje = 0;
            $Formato = '';
            if (isset($valoresArray['IdTriaje']) && isset($valoresArray['Formato'])) {
                $IdTriaje = $valoresArray['IdTriaje'];
                $Formato = $valoresArray['Formato'];
            } else {
                return response()->json([
                    'respuesta' => false,
                    'data' => [
                        'message' => '.:: NO SE PUDO OBTENER LOS PARAMETROS NECESARIOS PARA GENERAR EL PROCESO DE IMPRESIÓN ::.',
                        'codigo' => '400'
                    ]
                ], 400);
            }

            // Realizamos la búsqueda de impresoras según el servicio
            $Ticket = DB::select("EXEC TerminalImpresoraFormato '" . $request->servicio_printer . "', 'Ticket', 0, 1");
            if (empty($Ticket) || !isset($Ticket[0])) {
                return response()->json([
                    'respuesta' => false,
                    'data' => [
                        'message' => '.:: NO SE PUDO UBICAR LA IMPRESORA ::.',
                        'codigo' => '400'
                    ]
                ], 400);
            }


            // Inicializamos la variable para el estado del ticket
            $codigo_Triaje_Ticket = 'ERROR';
            $nombreArchivo_Triaje_Ticket = $IdTriaje . '_AMT.pdf';

            // Creamos los datos como un array
            $Parametros = [
                'IdTriaje' => $IdTriaje,
                'Formato' => $Formato
            ];

            // Resolvemos el controlador usando el contenedor de Laravel
            $triajesController = App::make(TriajeEmergenciaController::class);
            $response = $triajesController->TriajeGenerarFormatoPDF(new Request($Parametros));
            $responseJson = $response->getContent();
            $responseData = json_decode($responseJson, true);

            if ($responseData['success']) {
                $codigo_Triaje_Ticket = 'OK';
                // Armamos la respuesta con los datos del ticket
                $Triaje_Ticket = array(
                    'codigo' => $codigo_Triaje_Ticket,
                    'ImpresionPorDefecto' => $Ticket[0]->PorDefecto,
                    'NombreImpresora' => $Ticket[0]->Impresora,
                    'ImpresionEsComanda' => $Ticket[0]->EsComanda,
                    'Formato' => $Ticket[0]->Formato,
                    'nombre_pdf' => $nombreArchivo_Triaje_Ticket,
                    'type' => 'pdf',
                    'pdf' => $responseData['pdf_base64']
                );

                // Devolvemos la respuesta con el ticket generado
                return response()->json([
                    'respuesta' => true,
                    'data' => [
                        'Triaje_Ticket' => $Triaje_Ticket
                    ]
                ], 202);
            }

            return response()->json([
                'respuesta' => false,
                'data' => [
                    'message' => $responseData['mensaje'],
                    'codigo' => '400'
                ]
            ], 400);

        } else {
            return response()->json([
                'respuesta' => false,
                'data' => [
                    'message' => '.:: NO TIENE PERMISOS SUFICIENTES ::.',
                    'codigo' => '400'
                ]
            ], 400);
        }
    }
}
