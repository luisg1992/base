<?php

namespace Modules\ConsultaExterna\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\ConsultaExterna\DataTables\AtencionMedicaCEDataTable;
use Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones\AtencionMedicaRecetaController;
use Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones\AtencionMedicaDiagnosticoController;
use Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones\AtencionMedicaInterconsultaController;
use Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones\AtencionMedicaProcedimientoController;

class AtencionMedicaCEController extends Controller
{
    use AtencionMedicaCEDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/ConsultaExterna/AtencionMedicaCE/IndexAtencionMedicaCE');
    }

    public function obtenerServiciosProgramados(Request $request)
    {
        $fecha = formatear_fecha($request->input('Fecha')) ?? null;
        $opciones = $this->obtenerTurnosServiciosProgramados($fecha);
        return response()->json($opciones);
    }

    public function show($IdCuentaAtencion)
    {
        $datos = DB::select('EXEC WebS_Atenciones_DatosAtencion_Consultar ?', [$IdCuentaAtencion]);
        if (empty($datos)) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        $datosAtencion = (array)$datos[0];
        $AtencionMedicaDX = new AtencionMedicaDiagnosticoController();
        $diagnosticos =  $AtencionMedicaDX->obtenerDetalleDiagnostico($datosAtencion['IdAtencion']);

        $AtencionMedicaReceta = new AtencionMedicaRecetaController();
        $productos_farmacia = $AtencionMedicaReceta->obtenerDetalleReceta($datosAtencion['IdCuentaAtencion'], 5);
        $productos_apoyo_dx = $AtencionMedicaReceta->obtenerDetalleReceta($datosAtencion['IdCuentaAtencion'], null);

        $AtencionProcedimiento = new AtencionMedicaProcedimientoController();
        $procedimientos_cpt = $AtencionProcedimiento->obtenerDetalleProcedimiento($datosAtencion['IdCuentaAtencion'], 1);

        $AtencionInterconsulta = new AtencionMedicaInterconsultaController();
        $interconsultas = $AtencionInterconsulta->obtenerDetalleInterconsulta($datosAtencion['IdAtencion']);

        return response()->json([
            'success' => true,
            'data' => $datosAtencion,
            'diagnosticos' => $diagnosticos,
            'productos_farmacia' => $productos_farmacia,
            'productos_apoyo_dx' => $productos_apoyo_dx,
            'procedimientos_cpt' => $procedimientos_cpt,
            'interconsultas' => $interconsultas
        ]);
    }
}
