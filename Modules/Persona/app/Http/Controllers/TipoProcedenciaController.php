<?php

namespace Modules\Persona\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Persona\DataTables\TipoProcedenciaDataTable;
use Modules\Persona\Models\TipoProcedencia;
use Modules\Persona\Http\Requests\TipoProcedenciaRequest;
use Modules\Persona\Http\Resources\TipoProcedenciaResource;

class TipoProcedenciaController extends Controller
{
    use TipoProcedenciaDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Persona/TipoProcedencia/TipoProcedenciaIndex');
    }

    public function show($id)
    {
        $record = TipoProcedencia::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TipoProcedenciaResource($record);
    }

    public function store(TipoProcedenciaRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = TipoProcedencia::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = TipoProcedencia::create($data);
        }
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new TipoProcedenciaResource(TipoProcedencia::query()->findOrFail($id));
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
            $record = TipoProcedencia::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TipoProcedenciaResource(TipoProcedencia::query()->findOrFail($id));
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
            $record = TipoProcedencia::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
