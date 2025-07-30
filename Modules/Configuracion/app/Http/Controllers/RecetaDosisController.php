<?php

namespace Modules\Configuracion\Http\Controllers;

use App\Cache\Configuracion\RecetaDosisCache;
use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\RecetaDosisDataTable;
use Modules\Configuracion\DataTables\RecetaViaAdministracionDataTable;
use Modules\Configuracion\Http\Requests\RecetaDosisRequest;
use Modules\Configuracion\Http\Requests\RecetaViaAdministracionRequest;
use Modules\Configuracion\Http\Resources\RecetaDosisResource;
use Modules\Configuracion\Http\Resources\RecetaViaAdministracionResource;
use Modules\Configuracion\Models\RecetaDosis;
use Modules\Configuracion\Models\RecetaViaAdministracion;

class RecetaDosisController extends Controller
{
    use RecetaDosisDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/RecetaDosis/IndexRecetaDosis');
    }

    public function show($id)
    {
        $record = RecetaDosis::query()->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new RecetaDosisResource($record);
    }

    public function store(RecetaDosisRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = RecetaDosis::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = RecetaDosis::max('idDosis');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['idDosis'] = $nuevoId;

            $record = RecetaDosis::create($data);
        }
        RecetaDosisCache::clearCache();//cache_receta_dosis_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new RecetaDosisResource(RecetaDosis::query()->findOrFail($id));
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
            $record = RecetaDosis::query()->findOrFail($request->input('id'));
            $record->delete();
            RecetaDosisCache::clearCache();//cache_receta_dosis_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new RecetaDosisResource(RecetaDosis::query()->findOrFail($id));
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
            $record = RecetaDosis::query()->find($request->input('id'));
            $record->update([
                'IdEstado' => !$record->IdEstado,
            ]);

            RecetaDosisCache::clearCache();//cache_receta_dosis_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
