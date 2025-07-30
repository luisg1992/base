<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\TipoMotivoTriajeEmergenciaDataTable;
use Modules\Configuracion\Http\Requests\TipoMotivoTriajeEmergenciaRequest;
use Modules\Configuracion\Http\Resources\TipoMotivoTriajeEmergenciaResource;
use Modules\Configuracion\Models\TipoMotivoTriajeEmergencia;

class TipoMotivoTriajeEmergenciaController extends Controller
{
    use TipoMotivoTriajeEmergenciaDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/TipoMotivoTriajeEmergencia/IndexTipoMotivoTriajeEmergencia');
    }

    public function show($id)
    {
        $record = TipoMotivoTriajeEmergencia::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TipoMotivoTriajeEmergenciaResource($record);
    }
    public function store(TipoMotivoTriajeEmergenciaRequest $request)
    {

        $data = $request->validated();

        if (!empty($request->id)) {
            $record = TipoMotivoTriajeEmergencia::findOrFail($request->id);
            $record->update($data);
        } else {
            $record = TipoMotivoTriajeEmergencia::create($data);
        }
        cache_triaje_emergencia_motivo_limpiar();
        return obtener_respuesta_exito('Datos guardados correctamente', $record);
    }


    public function recordDestroy($id)
    {
        $record = new TipoMotivoTriajeEmergenciaResource(TipoMotivoTriajeEmergencia::query()->findOrFail($id));
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
            $record = TipoMotivoTriajeEmergencia::query()->findOrFail($request->input('id'));
            $record->delete();
            cache_triaje_emergencia_motivo_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TipoMotivoTriajeEmergenciaResource(TipoMotivoTriajeEmergencia::query()->findOrFail($id));
        return [
            'title' => 'ESTÁ SEGURO QUE SEA CAMBIAR EL IdEstado DEL REGISTRO SELECCIONADO: ' . $record->Descripcion . '?',
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
            $record = TipoMotivoTriajeEmergencia::query()->find($request->input('id'));
            $record->update([
                'IdEstado' => !$record->IdEstado,
            ]);
            cache_triaje_emergencia_motivo_limpiar();
            return obtener_respuesta_exito('EL Estado DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->IdEstado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}