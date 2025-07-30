<?php

namespace Modules\Persona\Http\Controllers;

use App\Core\Services\StoredProcedureService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ws\WsController;
use Illuminate\Support\Facades\DB;
use Modules\Persona\Models\Paciente;

class PersonaController extends Controller
{
    public function PacienteBuscarTipoAndDocumento(Request $request)
    {
        if ($request->tipodocumento == -1 ) {
            $paciente = Paciente::buscarPorNroHistoriaClinica($request->numerodocumento);
        } else {
            $paciente = Paciente::buscarPorDocumentoYTipo($request->numerodocumento, $request->tipodocumento);
        }
        if ($paciente) {
            return response()->json([
                'success' => true,
                'mensaje' => 'PERSONA ENCONTRADA EN LA BASE DE DATOS.',
                'data' => $paciente
            ]);
        }

        if ($request->tipodocumento == 1) {
            // Si no se encontró, consultamos a RENIEC
            $request->merge(['tipopersona' => 0]);
            $wsController = new WsController();
            $reniecResponse = $wsController->obtenerDatosReniecCompletos($request);
            if (isset($reniecResponse['respuesta']) && $reniecResponse['respuesta'] == 1) {
                $paciente = Paciente::buscarPorDocumentoYTipo($request->numerodocumento, $request->tipodocumento);

                if ($paciente) {
                    return response()->json([
                        'success' => true,
                        'mensaje' => 'PERSONA ENCONTRADA EN LA BASE DE DATOS.',
                        'data' => $paciente
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'mensaje' => 'EL PACIENTE NO PUDO SER REPLICADO DE RENIEC.',
                    'data' => null
                ]);
            }

            return response()->json([
                'success' => false,
                'mensaje' => 'ERROR DE SERVICIO DE CONSULTA RENIEC.',
                'response' => $reniecResponse,
                'data' => null
            ]);
        } else if ($request->tipodocumento > 1) {
            return response()->json([
                'success' => false,
                'mensaje' => 'EL PACIENTE NO PUDO SER ENCONTRADO, VERIFIQUE EL DOCUMENTO O NÚMERO DE HISTORIA INGRESADO.',
                'data' => null
            ]);
        } else if ($request->tipodocumento == 0) {
            return response()->json([
                'success' => false,
                'mensaje' => 'EL NÚMERO DE HISTORIA CLÍNICA NO EXISTE',
                'data' => null
            ]);
        }
    }

    public function PacienteBuscarPorNumeroCuenta(Request $request)
    {
        $data = DB::select('EXEC usp_AtencionesSelecionarPorCuenta_20240701 @idCuentaAtencion = ?',
            [$request->input('IdCuentaAtencion')]
        );


        $datanew = DB::select(
            'EXEC ServiciosSeleccionarPorId @IdServicio = ?',
            [$data[0]->IdServicioIngreso]
        );
        $data[0]->Servicio = $datanew[0];

        return response()->json([
            'success' => true,
            'mensaje' => 'PERSONA ENCONTRADA EN LA BASE DE DATOS',
            'data' =>  $data[0]
        ]);

    }
}
