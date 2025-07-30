<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Core\DataTables\UsuarioRefConDataTable;
use Modules\Core\Http\Requests\UsuarioRefConRequest;
use Modules\Core\Http\Resources\UsuarioRefConResource;
use Modules\Core\Models\UsuarioRefCon;

class UsuarioRefConController extends Controller
{
    use UsuarioRefConDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Core/UsuariosRefCon/IndexUsuariosRefCon');
    }

    public function show($id)
    {
        $record = UsuarioRefCon::query()->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new UsuarioRefConResource($record);
    }

    public function store(UsuarioRefConRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = UsuarioRefCon::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = UsuarioRefCon::create($data);
        }
        cache_core_usuario_ref_con_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new UsuarioRefConResource(UsuarioRefCon::query()->findOrFail($id));
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
            $record = UsuarioRefCon::query()->findOrFail($request->input('id'));
            $record->delete();
            cache_core_usuario_ref_con_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new UsuarioRefConResource(UsuarioRefCon::query()->findOrFail($id));
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
            $record = UsuarioRefCon::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            cache_core_usuario_ref_con_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
