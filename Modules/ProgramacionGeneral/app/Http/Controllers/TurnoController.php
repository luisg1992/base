<?php

namespace Modules\ProgramacionGeneral\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\ProgramacionGeneral\DataTables\TurnoDataTable;
use Modules\ProgramacionGeneral\Http\Requests\TurnoRequest;
use Modules\ProgramacionGeneral\Http\Resources\TurnoResource;
use Modules\ProgramacionGeneral\Models\Turno;

class TurnoController extends Controller
{
    use TurnoDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/ProgramacionGeneral/Turno/IndexTurno');
    }

    public function show($id)
    {
        $record = Turno::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TurnoResource($record);
    }

    public function store(TurnoRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = Turno::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = Turno::create($data);
        }
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new TurnoResource(Turno::query()->findOrFail($id));
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
            $record = Turno::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TurnoResource(Turno::query()->findOrFail($id));
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
            $record = Turno::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
