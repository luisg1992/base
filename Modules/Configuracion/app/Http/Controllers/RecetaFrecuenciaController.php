<?php

namespace Modules\Configuracion\Http\Controllers;

use App\Cache\Configuracion\RecetaDosisCache;
use App\Cache\Configuracion\RecetaDosisUnidadMedidaCache;
use App\Cache\Configuracion\RecetaFrecuenciaCache;
use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\RecetaDosisUnidadMedidaDataTable;
use Modules\Configuracion\DataTables\RecetaFrecuenciaDataTable;
use Modules\Configuracion\Http\Requests\RecetaDosisUnidadMedidaRequest;
use Modules\Configuracion\Http\Requests\RecetaFrecuenciaRequest;
use Modules\Configuracion\Http\Resources\RecetaDosisUnidadMedidaResource;
use Modules\Configuracion\Http\Resources\RecetaFrecuenciaResource;
use Modules\Configuracion\Models\RecetaDosisUnidadMedida;
use Modules\Configuracion\Models\RecetaFrecuencia;

class RecetaFrecuenciaController extends Controller
{
    use RecetaFrecuenciaDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/RecetaFrecuencia/IndexRecetaFrecuencia');
    }

    public function show($id)
    {
        $record = RecetaFrecuencia::query()->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new RecetaFrecuenciaResource($record);
    }

    public function store(RecetaFrecuenciaRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = RecetaFrecuencia::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = RecetaFrecuencia::max('IdRecetaFrecuencia');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['IdRecetaFrecuencia'] = $nuevoId;

            $record = RecetaFrecuencia::create($data);
        }
        RecetaFrecuenciaCache::clearCache();//cache_receta_dosis_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new RecetaFrecuenciaResource(RecetaFrecuencia::query()->findOrFail($id));
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
            $record = RecetaFrecuencia::query()->findOrFail($request->input('id'));
            $record->delete();
            RecetaFrecuenciaCache::clearCache();//cache_receta_dosis_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new RecetaFrecuenciaResource(RecetaFrecuencia::query()->findOrFail($id));
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
            $record = RecetaFrecuencia::query()->find($request->input('id'));
            /* $record->update([
                'IdEstado' => !$record->IdEstado,
            ]); */

            RecetaFrecuenciaCache::clearCache();//cache_receta_dosis_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
