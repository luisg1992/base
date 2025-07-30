<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\CitaAnuladaMotivoDataTable;
use Modules\Configuracion\Http\Requests\CitaAnuladaMotivoRequest;
use Modules\Configuracion\Models\CitaAnuladaMotivo;
use Modules\Configuracion\Http\Resources\CitaAnuladaMotivoResource;

class CitaAnuladaMotivoController extends Controller
{
    use CitaAnuladaMotivoDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/CitaAnuladaMotivo/IndexCitaAnuladaMotivo');
    }

    public function show($id)
    {
        $record = CitaAnuladaMotivo::query()->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new CitaAnuladaMotivoResource($record);
    }

    public function store(CitaAnuladaMotivoRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = CitaAnuladaMotivo::query()->findOrFail($request->id);
            $record->update($data);
        } else { 
            $record = CitaAnuladaMotivo::create($data);
        }
        cache_configuracion_cita_anulada_motivo_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new CitaAnuladaMotivoResource(CitaAnuladaMotivo::query()->findOrFail($id));
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
            $record = CitaAnuladaMotivo::query()->findOrFail($request->input('id'));
            $record->delete();
            cache_configuracion_cita_anulada_motivo_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new CitaAnuladaMotivoResource(CitaAnuladaMotivo::query()->findOrFail($id));
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
            $record = CitaAnuladaMotivo::query()->find($request->input('id'));
            $record->update([
                'IdEstado' => !$record->IdEstado,
            ]);

            cache_configuracion_cita_anulada_motivo_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
