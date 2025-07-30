<?php

namespace Modules\Seguridad\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Modules\Core\Models\Modulo;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Modules\Seguridad\DataTables\RolDataTable;
use Modules\Seguridad\Http\Requests\RoleRequest;
use Modules\Seguridad\Http\Resources\RoleResource;

class RolController extends Controller
{
    use RolDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Seguridad/Rol/IndexRole');
    }

    public function listarTablas()
    {
        $lastChildren = Modulo::query()
            ->with('permissions')
            ->doesntHave('hijos')->get();

        return $lastChildren->map(function ($modulo) {
            return [
                'id' => $modulo->id,
                'name' => $modulo->getFullLabel(),
                'descripcion' => $modulo->descripcion,
                'orden' => $modulo->orden,
                'permissions' => $modulo->permissions
                    ->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'descripcion' => $permission->descripcion,
                            'name' => $permission->name,
                            'value' => false
                        ];
                    })
            ];
        })->sortBy('orden')->values();
    }

    public function show($id)
    {
        $record = Role::query()->with('permissions')->findOrFail($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new RoleResource($record);
    }

    public function store(RoleRequest $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $permisos = $request->input('permisos');

            $record = Role::query()->firstOrNew(['id' => $id]);
            $record->fill($request->all());
            $record->save();

            $record->syncPermissions($permisos);
            DB::commit();
            cache_seguridad_role_limpiar();
            return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function recordDestroy($id)
    {
        $record = new RoleResource(Role::query()->findOrFail($id));
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
            $record = Role::query()->findOrFail($request->input('id'));
            $record->delete();

            cache_seguridad_role_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
