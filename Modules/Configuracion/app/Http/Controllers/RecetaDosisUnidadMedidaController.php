<?php

namespace Modules\Configuracion\Http\Controllers;

use App\Cache\Configuracion\RecetaDosisCache;
use App\Cache\Configuracion\RecetaDosisUnidadMedidaCache;
use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\RecetaDosisUnidadMedidaDataTable;
use Modules\Configuracion\Http\Requests\RecetaDosisUnidadMedidaRequest;
use Modules\Configuracion\Http\Resources\RecetaDosisUnidadMedidaResource;
use Modules\Configuracion\Models\RecetaDosisUnidadMedida;

class RecetaDosisUnidadMedidaController extends Controller
{
    use RecetaDosisUnidadMedidaDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/RecetaDosisUnidadMedida/IndexRecetaDosisUnidadMedida');
    }

    public function show($id)
    {
        $record = RecetaDosisUnidadMedida::query()->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new RecetaDosisUnidadMedidaResource($record);
    }

    public function store(RecetaDosisUnidadMedidaRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = RecetaDosisUnidadMedida::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = RecetaDosisUnidadMedida::max('IdRecetaDosisUnidadMedida');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['IdRecetaDosisUnidadMedida'] = $nuevoId;

            $record = RecetaDosisUnidadMedida::create($data);
        }
        RecetaDosisUnidadMedidaCache::clearCache();//cache_receta_dosis_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new RecetaDosisUnidadMedidaResource(RecetaDosisUnidadMedida::query()->findOrFail($id));
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
            $record = RecetaDosisUnidadMedida::query()->findOrFail($request->input('id'));
            $record->delete();
            RecetaDosisUnidadMedidaCache::clearCache();//cache_receta_dosis_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new RecetaDosisUnidadMedidaResource(RecetaDosisUnidadMedida::query()->findOrFail($id));
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
            $record = RecetaDosisUnidadMedida::query()->find($request->input('id'));
            /* $record->update([
                'IdEstado' => !$record->IdEstado,
            ]); */

            RecetaDosisUnidadMedidaCache::clearCache();//cache_receta_dosis_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
