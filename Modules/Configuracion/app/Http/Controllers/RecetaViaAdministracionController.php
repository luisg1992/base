<?php

namespace Modules\Configuracion\Http\Controllers;

use App\Cache\Configuracion\RecetaViaAdministracionCache;
use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\RecetaViaAdministracionDataTable;
use Modules\Configuracion\Http\Requests\RecetaViaAdministracionRequest;
use Modules\Configuracion\Http\Resources\RecetaViaAdministracionResource;
use Modules\Configuracion\Models\RecetaViaAdministracion;

class RecetaViaAdministracionController extends Controller
{
    use RecetaViaAdministracionDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/RecetaViaAdministracion/IndexRecetaViaAdministracion');
    }

    public function show($id)
    {
        $record = RecetaViaAdministracion::query()->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new RecetaViaAdministracionResource($record);
    }

    public function store(RecetaViaAdministracionRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = RecetaViaAdministracion::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = RecetaViaAdministracion::max('IdViaAdministracion');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['IdViaAdministracion'] = $nuevoId;
            $record = RecetaViaAdministracion::create($data);
        }
        RecetaViaAdministracionCache::clearCache();//cache_configuracion_cita_anulada_motivo_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new RecetaViaAdministracionResource(RecetaViaAdministracion::query()->findOrFail($id));
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
            $record = RecetaViaAdministracion::query()->findOrFail($request->input('id'));
            $record->delete();
            RecetaViaAdministracionCache::clearCache();//cache_configuracion_cita_anulada_motivo_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new RecetaViaAdministracionResource(RecetaViaAdministracion::query()->findOrFail($id));
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
            $record = RecetaViaAdministracion::query()->find($request->input('id'));
            $record->update([
                'IdEstado' => !$record->IdEstado,
            ]);

            RecetaViaAdministracionCache::clearCache();//cache_configuracion_cita_anulada_motivo_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
