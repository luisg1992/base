<?php

namespace Modules\Core\Http\Controllers;

use App\Helpers\ModuloHelper;
use Exception;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Core\DataTables\ModuloDataTable;
use Modules\Core\Http\Requests\ModuloRequest;
use Modules\Core\Http\Resources\ModuloResource;
use Modules\Core\Models\Modulo;
use Spatie\Permission\Models\Permission;

class ModuloController extends Controller
{
    use ModuloDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Core/Modulo/IndexModulo');
    }

    public function listarTablas()
    {
        $modulosPadre = Modulo::get(['ModuloId', 'Etiqueta', 'Descripcion', 'Subtitulo']);
        return [
            'modulos' => $modulosPadre
        ];
    }

    public function show($id)
    {
        $record = Modulo::with('padre')->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new ModuloResource($record);
    }

    public function store(ModuloRequest $request)
    {
        $data = $request->all();
        if (!empty($request->id)) {
            $record = Modulo::query()->findOrFail($request->id);
            $record->update($data);
            $valorCompleto = $this->obtenerValorCompleto($request->id);
            $record->update([
                'Valor' => $valorCompleto,
            ]);
            Permission::findOrCreate($valorCompleto);
        } else {
            $orden = 1;
            $ModuloPadreId = $data['ModuloPadreId'];
            if ($ModuloPadreId) {
                $m = Modulo::query()
                    ->where('ModuloPadreId', $ModuloPadreId)
                    ->orderByDesc('Orden')
                    ->first();
                $orden = $m ? ($m->Orden + 1) : 1;
                $etiquetaPadre = $this->obtenerValorCompleto($ModuloPadreId) . '.';
            } else {
                $etiquetaPadre = '';
            }
            $data['Valor'] = $etiquetaPadre . Str::slug($data['Etiqueta'], '.');
            $data['Orden'] = $orden;

            $record = Modulo::query()->create($data);

            Permission::findOrCreate($record->Valor);

            foreach ($data['acciones'] as $accion) {
                if ($accion['Seleccionado']) {
                    $accionValor = Str::slug($accion['Nombre'], '.');
                    $record->acciones()->updateOrCreate([
                        'Nombre' => $accion['Nombre'],
                        'Valor' => $accionValor
                    ]);
                    Permission::findOrCreate("{$record->Valor}.{$accionValor}");
                }
            }
        }

        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }

    public function recordDestroy($id)
    {
        $record = new ModuloResource(Modulo::query()->findOrFail($id));
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
            $record = Modulo::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    // Nuevo método para obtener los módulos permitidos para el usuario autenticado
    public function getModulosPermitidosUsuario(Request $request)
    {
        $user = auth()->user();
        $permisos = $user->getAllPermissions()->pluck('name')->toArray();

        $modulosConPermisos = [];

        $allModules = Modulo::query()->get();

        foreach ($allModules as $m) {
            $permisoAcceso = $m->Valor . '.acceder';

            // Agrega el módulo solo si el usuario tiene el permiso exacto "modulo.acceder"
            if (in_array($permisoAcceso, $permisos)) {
                $modulosConPermisos[] = $m->ModuloId;
            }
        }

        return $this->getRecords($modulosConPermisos);
//
//        $user = auth()->user();
//        $permisos = $user->getAllPermissions()->pluck('name')->toArray();
//        $modulosConPermisos = [];
//        $allModules = Modulo::query()->get();
//
//        foreach ($allModules as $m) {
//            $valor = $m->Valor;
//            $tienePermiso = collect($permisos)->contains(function ($permiso) use ($valor) {
//                return str_starts_with($permiso, $valor . '.');
//            });
//            if ($tienePermiso || in_array($valor, $permisos)) {
//                $modulosConPermisos[] = $m->ModuloId;
//            }
//        }
//
//        return $this->getRecords($modulosConPermisos);
    }

    public function getRecords($ids = null)
    {
        $ids_all = [];

        if (!is_null($ids)) {
            $modulos = Modulo::query()->whereIn('ModuloId', $ids)->get();
            foreach ($modulos as $mod) {
                if ($mod->ModuloPadreId) {
                    $ids_all[] = $mod->ModuloPadreId;
                }
                $ids_all[] = $mod->ModuloId;
            }
            $ids_all = array_unique($ids_all);
        }

        $query = Modulo::query()
            ->with('acciones')
            ->whereNull('ModuloPadreId');
        if (!empty($ids_all)) {
            $query->whereIn('ModuloId', $ids_all);
        }

        $records = $query->orderBy('Orden')->get();

        return $records->map(function ($m) use ($ids_all) {
            return $this->mapModulo($m, $ids_all);
        })->toArray();
    }

    private function getLevels($parent_id, $ids = null)
    {
        $query = Modulo::query()
            ->with('acciones')
            ->where('ModuloPadreId', $parent_id);
        if (!empty($ids)) {
            $query->whereIn('ModuloId', $ids);
        }

        return $query->orderBy('Orden')->get()->map(function ($m) use ($ids) {
            return $this->mapModulo($m, $ids);
        })->toArray();
    }

    private function mapModulo($modulo, $ids = null)
    {
        $children = $this->getLevels($modulo->ModuloId, $ids);
        $has_children = count($children) > 0;

        $data = [
            'ModuloId' => $modulo->ModuloId,
            'ModuloPadreId' => $modulo->ModuloPadreId,
            'Etiqueta' => $modulo->Etiqueta,
            'Subtitulo' => $modulo->Subtitulo,
            'Descripcion' => $modulo->Descripcion,
            'label' => $modulo->Etiqueta,
            'Icono' => $modulo->Icono,
            'icon' => 'ti ti-' . $modulo->Icono,
            'Url' => $modulo->Url,
            'to' => $modulo->Url,
            'Orden' => $modulo->Orden,
            'Estado' => $modulo->Estado ? 'activo' : 'inactivo',
            'EsAccesoDirecto' => $modulo->EsAccesoDirecto,
            'EstaBloqueado' => $modulo->EstaBloqueado,
            'Valor' => $modulo->Valor,
            'children' => $children,
            'items' => $has_children ? $children : null,
            'acciones' => $modulo->acciones,
            'has_children' => count($children) > 0,
        ];

//        if ($modulo->Url) {
//            $data['to'] = $modulo->Url;
//        }

        return $data;
    }


    public function actualizarOrden(Request $request)
    {
        $modules = $request->input('modulos');
        foreach ($modules as $index => $mod) {
            Modulo::query()
                ->where('ModuloId', $mod['ModuloId'])
                ->update([
                    'ModuloPadreId' => null,
                    'Orden' => $index + 1,
                ]);
            $this->actualizarOrdenChildren($mod, $mod['ModuloId']);
        }

        return [
            'success' => true,
            'message' => 'Módulo actualizado con éxito',
            'data' => [
                'menu' => $this->getRecords(),
            ]
        ];
    }

    private function actualizarOrdenChildren($module, $parent_id)
    {
        foreach ($module['children'] as $i => $m) {
            Modulo::query()
                ->where('ModuloId', $m['ModuloId'])
                ->update([
                    'ModuloPadreId' => $parent_id,
                    'Orden' => $i + 1,
                ]);
            if (count($m['children']) > 0) {
                $this->actualizarOrdenChildren($m, $m['ModuloId']);
            }
        }
    }

    public function obtenerValorCompleto(int $moduloId): string
    {
        $modulo = Modulo::query()->find($moduloId);
        if (!$modulo) {
            throw new \Exception("El módulo con ID $moduloId no existe.");
        }
        $slug = Str::slug($modulo->Etiqueta, '.');
        $valorCompleto = $slug;
        $this->recorrerPadres($modulo, $valorCompleto);

        return $valorCompleto;
    }

    private function recorrerPadres(Modulo $modulo, string &$valorCompleto)
    {
        $moduloPadre = Modulo::query()->find($modulo->ModuloPadreId);
        if ($moduloPadre) {
            $slugPadre = Str::slug($moduloPadre->Etiqueta, '.');
            $valorCompleto = "$slugPadre.$valorCompleto";
            $this->recorrerPadres($moduloPadre, $valorCompleto);
        }
    }

    public function obtenerModuloActual(Request $request)
    {
        $path = $request->input('path');

        return ModuloHelper::obtenerPermisoBaseDesdeRuta($path);
    }
}
