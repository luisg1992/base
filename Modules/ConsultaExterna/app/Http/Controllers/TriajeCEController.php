<?php

namespace Modules\ConsultaExterna\Http\Controllers;

use App\Core\Services\StoredProcedureService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\ConsultaExterna\DataTables\AtencionMedicaCEDataTable;
use Modules\ConsultaExterna\DataTables\TriajeCEDataTable;

class TriajeCEController extends Controller
{
    use TriajeCEDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/ConsultaExterna/TriajeCE/IndexTriajeCE');
    }

    public function obtenerServiciosProgramados(Request $request)
    {
        $fecha = formatear_fecha($request->input('Fecha')) ?? null;
        $opciones = $this->obtenerTurnosServiciosProgramados($fecha);
        return response()->json($opciones);
    }

    public function show($IdCuentaAtencion)
    {
        $sp = new StoredProcedureService('sqlsrvExterna');
        $params = [
            $IdCuentaAtencion
        ];

        $datos = $sp->executeSinPaginacion('dbo.atencionesCESeleccionarPorId', $params);
        if (empty($datos)) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }

        $sp2 = new StoredProcedureService();
        $params = [
            $IdCuentaAtencion
        ];

        $datos2 = $sp2->executeSinPaginacion('dbo.AtencionesSeleccionarPorIdAtencion2', $params);
        if (empty($datos2)) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }

        $datos[0]->datos2 = $datos2[0];

        return response()->json([
            'success' => true,
            'data' => $datos[0] // Si solo esperas un registro, puedes devolver el primero
        ]);
    }
}
