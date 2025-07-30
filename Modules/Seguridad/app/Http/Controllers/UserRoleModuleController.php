<?php

namespace Modules\Seguridad\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Modules\Core\Models\Modulo;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\ModuloAccion;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Throwable;

class UserRoleModuleController extends Controller
{
    public function getRecords($userId)
    {
        $user = User::query()->find($userId);
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
//        $role = Role::query()->find($id);
//        $permissions = $role?->permissions?->pluck('name')?->toArray() ?? [];
        $query = Modulo::query()
            ->with('acciones')
            ->whereNull('ModuloPadreId');
        $records = $query->orderBy('Orden')->get();
        $_records = $records->map(function ($m) use ($permissions) {
            return $this->mapModulo($m, $permissions, [$m->ModuloId]);
        })
            ->filter()    // <-- elimina nulls
            ->values()
            ->toArray();
        $_selected = $this->extraerPermisosConKey($_records);

        return [
            'records' => $_records,
            'selected_key' => $_selected,
        ];
    }

    private function getLevels($parent_id, $permissions = [], $path = [])
    {
        $query = Modulo::query()
            ->with('acciones')
            ->where('ModuloPadreId', $parent_id);

        if (!empty($ids)) {
            $query->whereIn('ModuloId', $ids);
        }

        return $query->orderBy('Orden')->get()->map(function ($m) use ($permissions, $path) {
            return $this->mapModulo($m, $permissions, array_merge($path, [$m->ModuloId]));
        })->toArray();
    }

    private function mapModulo($modulo, $permissions = null, $path = [])
    {
        // Obtener hijos primero (recursivamente)
        $children = $this->getLevels($modulo->ModuloId, $permissions, $path);

        // Si no tiene hijos, obtenemos las acciones (como ya hacías)
        if (empty($children)) {
            $acciones = ModuloAccion::query()
                ->where('ModuloId', $modulo->ModuloId)
                ->get()
                ->transform(function ($a) use ($path, $modulo, $permissions) {
                    $moduloAccionId = $a->ModuloAccionId . '-a';
                    $moduloAccionValor = $modulo->Valor . '.' . $a->Valor;
                    $hasPermission = in_array($moduloAccionValor, $permissions);
                    return [
                        'label' => $a->Nombre,
                        'description' => $a->Descripcion,
                        'children' => [],
                        'key' => implode('-', array_merge($path, [$moduloAccionId])),
                        'has_permission' => $hasPermission,
                        'p' => [
                            'checked' => $hasPermission,
                            'partialChecked' => false,
                        ]
                    ];
                })
                // *** Solo dejar acciones con permiso ***
                ->filter(function ($accion) {
                    return $accion['has_permission'];
                })
                ->values()
                ->toArray();

            // *** Solo asignar children si hay acciones con permisos ***
            $children = $acciones;
        } else {
            // *** Filtrar hijos para dejar solo los que tienen permiso (o hijos con permisos) ***
            $children = collect($children)
                ->filter() // Elimina nulls
                ->filter(function ($child) {
                    return $child['has_permission'] || !empty($child['children']);
                })
                ->values()
                ->toArray();
        }

        // Determinar si este módulo tiene permiso directo
        $moduloPermisoValor = $modulo->Valor;
        $hasPermission = in_array($moduloPermisoValor, $permissions);

        // Recalcular estado de checked/partialChecked
        $checkedState = $this->calcularCheckedEstado($children);

        // *** Si no tiene permiso ni hijos con permisos, NO mostrar este módulo ***
        if (!$hasPermission && empty($children)) {
            return null;
        }

        // Construir el array del módulo normalmente
        return [
            'ModuloId' => $modulo->ModuloId,
            'ModuloPadreId' => $modulo->ModuloPadreId,
            'label' => $modulo->Etiqueta,
            'Subtitulo' => $modulo->Subtitulo,
            'Descripcion' => $modulo->Descripcion,
            'Icono' => $modulo->Icono,
            'Url' => $modulo->Url,
            'Orden' => $modulo->Orden,
            'Estado' => $modulo->Estado ? 'activo' : 'inactivo',
            'EsAccesoDirecto' => $modulo->EsAccesoDirecto,
            'EstaBloqueado' => $modulo->EstaBloqueado,
            'Valor' => $modulo->Valor,
            'children' => $children,
            // 'acciones' => $modulo->acciones, // Ya no es necesario en el frontend si solo filtras por permisos
            'has_children' => count($children) > 0,
            'key' => implode('-', $path),
            'has_permission' => $hasPermission,
            'p' => [
                'checked' => $hasPermission || $checkedState['checked'],
                'partialChecked' => $checkedState['partialChecked'],
            ]
        ];
    }


    private function extraerPermisosConKey(array $modulos): array
    {
        $resultado = [];

        foreach ($modulos as $modulo) {
            if (!empty($modulo['has_permission'])) {
                $resultado[$modulo['key']] = $modulo['p'];
            }

            // Revisa si tiene hijos
            if (!empty($modulo['children'])) {
                $resultado += $this->extraerPermisosConKey($modulo['children']);
            }
        }

        return $resultado;
    }

    private function calcularCheckedEstado(array $children): array
    {
        $checkedCount = 0;
        $totalCount = 0;

        foreach ($children as $child) {
            $totalCount++;

            if (!empty($child['p']['partialChecked'])) {
                // Si algún hijo está parcialmente seleccionado, este también debe estarlo
                return [
                    'checked' => false,
                    'partialChecked' => true
                ];
            }

            if (!empty($child['p']['checked'])) {
                $checkedCount++;
            }

            // En caso de hijos anidados, analizamos recursivamente
            if (!empty($child['children'])) {
                $estado = $this->calcularCheckedEstado($child['children']);
                if ($estado['partialChecked']) {
                    return [
                        'checked' => false,
                        'partialChecked' => true
                    ];
                }

                if ($estado['checked']) {
                    $checkedCount++;
                }
            }
        }

        // Todos seleccionados
        if ($checkedCount === $totalCount && $totalCount > 0) {
            return [
                'checked' => true,
                'partialChecked' => false
            ];
        }

        // Algunos seleccionados
        if ($checkedCount > 0 && $checkedCount < $totalCount) {
            return [
                'checked' => false,
                'partialChecked' => true
            ];
        }

        // Ninguno seleccionado
        return [
            'checked' => false,
            'partialChecked' => false
        ];
    }

    public function record($id)
    {
        $role = Role::query()->find($id);

        return [
            'id' => $role->id,
            'name' => $role->name,
        ];
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $modules = $request->input('modules');
            $role = Role::query()->firstOrNew(['id' => $id]);
            $role->fill($request->except('modules'));
            $role->save();
            $permissionIds = [];

            foreach (array_keys($modules) as $index) {
                $indexArray = explode('-', $index);
                $last = end($indexArray);
//                dd($last);
                if ($last === 'a') {
                    $actionId = prev($indexArray);
                    $moduleAction = ModuloAccion::query()
                        ->with('modulo')
                        ->find($actionId);
                    if ($moduleAction && $moduleAction->modulo) {
                        $permName = $moduleAction->modulo->Valor . '.' . $moduleAction->Valor;
                        $permission = Permission::query()->where('name', $permName)->first();
                        if (!$permission) {
                            $permission = Permission::create(['name' => $permName]);
                        }
                    }
                } else {
                    $moduleId = $last;
                    $module = Modulo::query()
                        ->find($moduleId);
                    if ($module) {
                        $permName = $module->Valor;
                        $permission = Permission::query()->where('name', $permName)->first();
                        if (!$permission) {
                            $permission = Permission::create(['name' => $permName]);
                        }
                    }
                }

                if (!empty($permission)) {
                    $permissionIds[] = $permission->id;
                }
            }
            $role->syncPermissions($permissionIds);
            DB::commit();
            cache_seguridad_role_limpiar();
            return [
                'success' => true,
                'message' => 'Los datos fueron guardados correctamente.',
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

    }
}
