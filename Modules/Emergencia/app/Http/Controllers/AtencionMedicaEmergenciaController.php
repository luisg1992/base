<?php

namespace Modules\Emergencia\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Emergencia\DataTables\AtencionMedicaEmergenciaDataTable;

class AtencionMedicaEmergenciaController extends Controller
{
    use AtencionMedicaEmergenciaDataTable;

    public function index(): Response
    {
        return Inertia::render('Modulos/Emergencia/AtencionMedicaEmergencia/AtencionMedicaEmergenciaIndex');
    }

    public function show($IdCuentaAtencion)
    {
        $datos = DB::select('EXEC WebS_Atenciones_DatosAtencion_Consultar ?', [$IdCuentaAtencion]);
        if (empty($datos)) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        $datosAtencion = (array)$datos[0]; 

        return response()->json([
            'success' => true,
            'data' => $datosAtencion 
        ]);
    }
}
