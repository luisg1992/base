<?php

namespace Modules\Seguridad\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Seguridad\DataTables\UserDataTable;
use Modules\Seguridad\Http\Requests\UserRequest;
use Modules\Seguridad\Http\Resources\UserResource;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    use UserDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Seguridad/Usuarios/UserIndex');
    }

    public function show($id)
    {
        $record = User::query()
            ->with('empleado', 'empleado.roles')
            ->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new UserResource($record);
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($request->has('id') && $request->input('id') != null) {
                $record = User::query()->find($request->input('id'));
                $estado = $record->estado;
            } else {
                $record = new User();
                $estado = 1;
            }

            $record->name = $request->input('name');
            $record->email = $request->input('email');
            $record->estado = $estado;
            if ($request->input('password')) {
                $record->password = bcrypt($request->input('password'));
            }

            $record->syncRoles($request->input('roles'));
            $record->save();

            $modules = $request->input('modules');
            $selectedPermissions = (new RoleModuleController())->getPermissionIds($modules);

            $rolePermissions = $record->roles
                ->flatMap(function ($role) {
                    return $role->permissions->pluck('id');
                })
                ->unique()
                ->toArray();
            // Permisos directos
            $directPermissions = $record->permissions->pluck('id')->toArray();

            // Permisos denegados
            $deniedPermissions = $record->deniedPermissions()->pluck('permissions.id as id')->toArray();

            $shouldBeDirect = array_diff($selectedPermissions, $rolePermissions);
            $shouldBeDenied = array_diff($rolePermissions, $selectedPermissions);
            $directToRemove = array_diff($directPermissions, $shouldBeDirect);
            $deniedToRemove = array_diff($deniedPermissions, $shouldBeDenied);

            // Quitar permisos directos que ya no van
            if (count($directToRemove)) {
                $permissions = Permission::whereIn('id', $directToRemove)->get();
                $record->revokePermissionTo($permissions);
            }

            // Asignar nuevos permisos directos
            if (count($shouldBeDirect)) {
                $record->givePermissionTo($shouldBeDirect);
            }

            // Quitar denegados que ya no aplican
            if (count($deniedToRemove)) {
                $record->deniedPermissions()->detach(
                    Permission::query()->whereIn('id', $deniedToRemove)->pluck('id')
                );
            }

            // Asignar nuevos denegados
            if (count($shouldBeDenied)) {
                $record->deniedPermissions()->syncWithoutDetaching(
                    Permission::query()->whereIn('id', $shouldBeDenied)->pluck('id')
                );
            }

            cache_persona_empleados_usuario_roles_limpiar();
            DB::commit();
            return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
        } catch (Exception $e) {
            DB::rollBack();
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordDestroy($id)
    {
        $record = new UserResource(User::query()->findOrFail($id));
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
            $record = User::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new UserResource(User::query()->findOrFail($id));
        return [
            'title' => 'ESTÁ SEGURO QUE SEA CAMBIAR EL ESTADO DEL REGISTRO SELECCIONADO: ' . $record->nombre . '?',
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
            $record = User::query()->find($request->input('id'));
            $record->update([
                'estado' => !$record->estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->nombre . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->estado) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
