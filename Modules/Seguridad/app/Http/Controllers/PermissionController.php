<?php

namespace Modules\Seguridad\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Modules\Core\Models\Modulo;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Modules\Seguridad\DataTables\PermissionDataTable;
use Modules\Seguridad\Http\Requests\PermissionRequest;
use Modules\Seguridad\Http\Resources\PermissionResource;

class PermissionController extends Controller
{
    use PermissionDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Seguridad/Permisos/IndexPermission');
    }

    public function listarTablas()
    {
        $modulosHijos = Modulo::query()->get(['ModuloId', 'Etiqueta', 'descripcion', 'Subtitulo', 'ModuloPadreId']);
        return [
            'modulos_permisos' => $modulosHijos
        ];
    }

    public function show($id)
    {
        $record = Permission::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new PermissionResource($record);
    }

    public function store(PermissionRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = Permission::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = Permission::create($data);
        }
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new PermissionResource(Permission::query()->findOrFail($id));
        return obtener_respuesta_eliminar_record(
            'ESTÁ SEGURO DE ELIMINAR EL ITEM SELECCIONADO: ' . $record->nombre . '?',
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
            $record = Permission::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
