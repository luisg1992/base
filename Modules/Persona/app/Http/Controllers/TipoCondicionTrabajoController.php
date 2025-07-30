<?php

namespace Modules\Persona\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Persona\DataTables\TipoCondicionTrabajoDataTable;
use Modules\Persona\Models\TipoCondicionTrabajo;
use Modules\Persona\Http\Requests\TipoCondicionTrabajoRequest;
use Modules\Persona\Http\Resources\TipoCondicionTrabajoResource;

class TipoCondicionTrabajoController extends Controller
{
    use TipoCondicionTrabajoDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Persona/TipoCondicionTrabajo/IndexTipoCondicionTrabajo');
    }

    public function show($id)
    {
        $record = TipoCondicionTrabajo::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TipoCondicionTrabajoResource($record);
    }

    public function store(TipoCondicionTrabajoRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = TipoCondicionTrabajo::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = TipoCondicionTrabajo::create($data);
        }
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new TipoCondicionTrabajoResource(TipoCondicionTrabajo::query()->findOrFail($id));
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
            $record = TipoCondicionTrabajo::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TipoCondicionTrabajoResource(TipoCondicionTrabajo::query()->findOrFail($id));
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
            $record = TipoCondicionTrabajo::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
