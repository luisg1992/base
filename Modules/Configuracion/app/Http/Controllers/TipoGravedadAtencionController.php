<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\TipoGravedadAtencionDataTable;
use Modules\Configuracion\Http\Requests\TipoGravedadAtencionRequest;
use Modules\Configuracion\Http\Resources\TipoGravedadAtencionResource;
use Modules\Configuracion\Models\TipoGravedadAtencion;

class TipoGravedadAtencionController extends Controller
{
    use TipoGravedadAtencionDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/TipoGravedadAtencion/TipoGravedadAtencionIndex');
    }

    public function show($id)
    {
        $record = TipoGravedadAtencion::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new TipoGravedadAtencionResource($record);
    }

    public function store(TipoGravedadAtencionRequest $request)
    {
        $data = $request->validated();

        if (!empty($request->id)) {
            $record = TipoGravedadAtencion::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = TipoGravedadAtencion::max('IdTipoGravedad');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['IdTipoGravedad'] = $nuevoId;

            $record = TipoGravedadAtencion::create($data);
        }
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }

    public function recordDestroy($id)
    {
        $record = new TipoGravedadAtencionResource(TipoGravedadAtencion::query()->findOrFail($id));
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
            $record = TipoGravedadAtencion::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new TipoGravedadAtencionResource(TipoGravedadAtencion::query()->findOrFail($id));
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
            $record = TipoGravedadAtencion::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
