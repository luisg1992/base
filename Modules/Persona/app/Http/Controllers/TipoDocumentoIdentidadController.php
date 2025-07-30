<?php

namespace Modules\Persona\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Persona\DataTables\TipoDocumentoIdentidadDataTable;
use Modules\Persona\Models\TipoDocumentoIdentidad;
use Modules\Persona\Http\Requests\TipoDocumentoIdentidadRequest;
use Modules\Persona\Http\Resources\TipoDocumentoIdentidadResource;

class TipoDocumentoIdentidadController extends Controller
{
    use TipoDocumentoIdentidadDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Persona/TipoDocumentoIdentidad/IndexTipoDocumentoIdentidad');
    }

    public function show($id)
    {
        $record = TipoDocumentoIdentidad::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TipoDocumentoIdentidadResource($record);
    }

    public function store(TipoDocumentoIdentidadRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = TipoDocumentoIdentidad::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = TipoDocumentoIdentidad::create($data);
        }
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new TipoDocumentoIdentidadResource(TipoDocumentoIdentidad::query()->findOrFail($id));
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
            $record = TipoDocumentoIdentidad::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TipoDocumentoIdentidadResource(TipoDocumentoIdentidad::query()->findOrFail($id));
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
            $record = TipoDocumentoIdentidad::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
