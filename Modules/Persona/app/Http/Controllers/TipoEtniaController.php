<?php

namespace Modules\Persona\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Persona\DataTables\TipoEtniaDataTable;
use Modules\Persona\Http\Requests\TipoEtniaRequest;
use Modules\Persona\Http\Resources\TipoEtniaResource;
use Modules\Persona\Models\TipoEtnia;

class TipoEtniaController extends Controller
{
    use TipoEtniaDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Persona/TipoEtnia/TipoEtniaIndex');
    }

    public function show($id)
    {
        $record = TipoEtnia::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TipoEtniaResource($record);
    }

    public function store(TipoEtniaRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = TipoEtnia::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = TipoEtnia::create($data);
        }
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new TipoEtniaResource(TipoEtnia::query()->findOrFail($id));
        return obtener_respuesta_eliminar_record(
            'ESTÁ SEGURO DE ELIMINAR EL ITEM SELECCIONADO: ' . $record->desetni . '?',
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
            $record = TipoEtnia::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TipoEtniaResource(TipoEtnia::query()->findOrFail($id));
        return [
            'title' => 'ESTÁ SEGURO QUE SEA CAMBIAR EL ESTADO DEL REGISTRO SELECCIONADO: ' . $record->desetni . '?',
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
            $record = TipoEtnia::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->desetni . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
