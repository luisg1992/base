<?php

namespace Modules\Configuracion\Http\Controllers;

use App\Cache\Configuracion\TiposModalidadAsistenciaCache;
use App\Cache\TraitCache;
use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\TiposModalidadAsistenciaDataTable;
use Modules\Configuracion\Http\Requests\TiposModalidadAsistenciaRequest;
use Modules\Configuracion\Http\Resources\TiposModalidadAsistenciaResource;
use Modules\Configuracion\Models\TiposModalidadAsistencia;

class TiposModalidadAsistenciaController extends Controller
{
    use TiposModalidadAsistenciaDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/TiposModalidadAsistencia/IndexTiposModalidadAsistencia');
    }

    public function show($id)
    {
        $record = TiposModalidadAsistencia::query()->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TiposModalidadAsistenciaResource($record);
    }

    public function store(TiposModalidadAsistenciaRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = TiposModalidadAsistencia::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = TiposModalidadAsistencia::create($data);
        }
        TiposModalidadAsistenciaCache::clearCache();//cache_tipos_modalidad_asistencia_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new TiposModalidadAsistenciaResource(TiposModalidadAsistencia::query()->findOrFail($id));
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
            $record = TiposModalidadAsistencia::query()->findOrFail($request->input('id'));
            $record->delete();
            TiposModalidadAsistenciaCache::clearCache();//cache_tipos_modalidad_asistencia_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TiposModalidadAsistenciaResource(TiposModalidadAsistencia::query()->findOrFail($id));
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
            $record = TiposModalidadAsistencia::query()->find($request->input('id'));
            $record->update([
                'IdEstado' => !$record->IdEstado,
            ]);

            TiposModalidadAsistenciaCache::clearCache();//cache_tipos_modalidad_asistencia_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
