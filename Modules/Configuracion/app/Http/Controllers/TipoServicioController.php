<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\TipoServicioDataTable;
use Modules\Configuracion\Http\Requests\TipoServicioRequest;
use Modules\Configuracion\Http\Resources\TipoServicioResource;
use Modules\Configuracion\Models\TipoServicio;

class TipoServicioController extends Controller
{
    use TipoServicioDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/TipoServicio/IndexTipoServicio');
    }

    public function show($id)
    {
        $record = TipoServicio::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TipoServicioResource($record);
    }

    public function store(TipoServicioRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = TipoServicio::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = TipoServicio::max('IdTipoServicio');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['IdTipoServicio'] = $nuevoId;

            $record = TipoServicio::create($data);
        }
        cache_configuracion_tipo_servicios_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new TipoServicioResource(TipoServicio::query()->findOrFail($id));
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
            $record = TipoServicio::query()->findOrFail($request->input('id'));
            $record->delete();
            cache_configuracion_tipo_servicios_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TipoServicioResource(TipoServicio::query()->findOrFail($id));
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
            $record = TipoServicio::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            cache_configuracion_tipo_servicios_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}