<?php

namespace Modules\Configuracion\Http\Controllers;

use App\Cache\Configuracion\TiposSolicitudInterconsultaCache;
use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\TiposSolicitudInterconsultaDataTable;
use Modules\Configuracion\Http\Requests\TiposSolicitudInterconsultaRequest;
use Modules\Configuracion\Http\Resources\TiposSolicitudInterconsultaResource;
use Modules\Configuracion\Models\TiposSolicitudInterconsulta;

class TiposSolicitudInterconsultaController extends Controller
{
    use TiposSolicitudInterconsultaDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/TiposSolicitudInterconsulta/IndexTiposSolicitudInterconsulta');
    }

    public function show($id)
    {
        $record = TiposSolicitudInterconsulta::query()->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TiposSolicitudInterconsultaResource($record);
    }

    public function store(TiposSolicitudInterconsultaRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = TiposSolicitudInterconsulta::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = TiposSolicitudInterconsulta::create($data);
        }
        TiposSolicitudInterconsultaCache::clearCache();//cache_tipos_solicitud_interconsulta_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new TiposSolicitudInterconsultaResource(TiposSolicitudInterconsulta::query()->findOrFail($id));
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
            $record = TiposSolicitudInterconsulta::query()->findOrFail($request->input('id'));
            $record->delete();
            TiposSolicitudInterconsultaCache::clearCache();//cache_tipos_solicitud_interconsulta_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TiposSolicitudInterconsultaResource(TiposSolicitudInterconsulta::query()->findOrFail($id));
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
            $record = TiposSolicitudInterconsulta::query()->find($request->input('id'));
            $record->update([
                'IdEstado' => !$record->IdEstado,
            ]);

            TiposSolicitudInterconsultaCache::clearCache();//cache_tipos_solicitud_interconsulta_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
