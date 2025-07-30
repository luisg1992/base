<?php

namespace Modules\Configuracion\Http\Controllers;

use App\Cache\Configuracion\RecetaClasificacionViaAdminCache;
use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\RecetaClasificacionViaAdminDataTable;
use Modules\Configuracion\DataTables\RecetaViaAdministracionDataTable;
use Modules\Configuracion\Http\Requests\RecetaClasificacionViaAdminRequest;
use Modules\Configuracion\Http\Requests\RecetaViaAdministracionRequest;
use Modules\Configuracion\Http\Resources\RecetaClasificacionViaAdminResource;
use Modules\Configuracion\Http\Resources\RecetaViaAdministracionResource;
use Modules\Configuracion\Models\RecetaClasificacionViaAdmin;
use Modules\Configuracion\Models\RecetaViaAdministracion;

class RecetaClasificacionViaAdminController extends Controller
{
    use RecetaClasificacionViaAdminDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/RecetaClasificacionViaAdmin/IndexRecetaClasificacionViaAdmin');
    }

    public function show($id)
    {
        $record = RecetaClasificacionViaAdmin::query()->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new RecetaClasificacionViaAdminResource($record);
    }

    public function store(RecetaClasificacionViaAdminRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = RecetaClasificacionViaAdmin::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = RecetaClasificacionViaAdmin::max('IdCategoria');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['IdCategoria'] = $nuevoId;

            $record = RecetaClasificacionViaAdmin::create($data);
        }
        RecetaClasificacionViaAdminCache::clearCache();//cache_receta_clasificacion_via_admin_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new RecetaClasificacionViaAdminResource(RecetaClasificacionViaAdmin::query()->findOrFail($id));
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
            $record = RecetaClasificacionViaAdmin::query()->findOrFail($request->input('id'));
            $record->delete();
            RecetaClasificacionViaAdminCache::clearCache();///cache_receta_clasificacion_via_admin_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new RecetaClasificacionViaAdminResource(RecetaClasificacionViaAdmin::query()->findOrFail($id));
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
            $record = RecetaClasificacionViaAdmin::query()->find($request->input('id'));
            $record->update([
                'IdEstado' => !$record->IdEstado,
            ]);

            RecetaClasificacionViaAdminCache::clearCache();//cache_receta_clasificacion_via_admin_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
