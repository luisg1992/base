<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\TriajeEmergenciaEstadoIngresoDataTable;
use Modules\Configuracion\Http\Requests\TriajeEmergenciaEstadoIngresoRequest;
use Modules\Configuracion\Http\Resources\TriajeEmergenciaEstadoIngresoResource;
use Modules\Configuracion\Models\TriajeEmergenciaEstadoIngreso;

class TriajeEmergenciaEstadoIngresoController extends Controller
{
    use TriajeEmergenciaEstadoIngresoDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/TriajeEmergenciaEstadoIngreso/IndexTriajeEmergenciaEstadoIngreso');
    }

    public function show($id)
    {
        $record = TriajeEmergenciaEstadoIngreso::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TriajeEmergenciaEstadoIngresoResource($record);
    }

    public function store(TriajeEmergenciaEstadoIngresoRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = TriajeEmergenciaEstadoIngreso::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = TriajeEmergenciaEstadoIngreso::create($data);
        }
        cache_triaje_emergencia_estado_ingreso_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new TriajeEmergenciaEstadoIngresoResource(TriajeEmergenciaEstadoIngreso::query()->findOrFail($id));
        return obtener_respuesta_eliminar_record(
            'ESTÁ SEGURO DE ELIMINAR EL ITEM SELECCIONADO: ' . $record->Descripcion . '?',
            true,
            $record
        );
    }

    public function destroy(Request $request)
    {
        $response = validacion_password($request);
        if (!$response['success']) {
            return $response;
        }

        try {
            $record = TriajeEmergenciaEstadoIngreso::query()->findOrFail($request->input('id'));
            $record->delete();
            cache_triaje_emergencia_estado_ingreso_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TriajeEmergenciaEstadoIngresoResource(TriajeEmergenciaEstadoIngreso::query()->findOrFail($id));
        return [
            'title' => 'ESTÁ SEGURO QUE SEA CAMBIAR EL ESTADO DEL REGISTRO SELECCIONADO: ' . $record->Descripcion . '?',
            'verify_password' => true,
            'record' => $record,
        ];
    }

    public function changeActive(Request $request)
    {
        $response = validacion_password($request);
        if (!$response['success']) {
            return $response;
        }

        try {
            $record = TriajeEmergenciaEstadoIngreso::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);
            cache_triaje_emergencia_estado_ingreso_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
