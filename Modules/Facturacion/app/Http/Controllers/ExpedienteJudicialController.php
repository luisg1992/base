<?php

namespace Modules\Facturacion\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Facturacion\DataTables\ExpedienteJudicialDataTable;
use Modules\Facturacion\Http\Requests\ExpedienteJudicialRequest;
use Modules\Facturacion\Http\Resources\ExpedienteJudicialResource;
use Modules\Facturacion\Models\ExpedienteJudicial;

class ExpedienteJudicialController extends Controller
{
    use ExpedienteJudicialDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Facturacion/ExpedientesJudiciales/ExpedientesJudicialesIndex');
    }

    public function show($id)
    {
        $record = ExpedienteJudicial::query()->with('paciente')->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new ExpedienteJudicialResource($record);
    }

    public function store(ExpedienteJudicialRequest $request)
    {
        //dd($request->all());
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = ExpedienteJudicial::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = ExpedienteJudicial::max('IdProgramaExpedienteJudicial');

            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;

            $data['IdProgramaExpedienteJudicial'] = $nuevoId;
            //DD(CartaGarantia::create($data));
            $record = ExpedienteJudicial::create($data);
            //DD($record);
        }
        //cache_configuracion_cita_anulada_motivo_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new ExpedienteJudicialResource(ExpedienteJudicial::query()->findOrFail($id));
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
            $record = ExpedienteJudicial::query()->findOrFail($request->input('id'));
            $record->delete();
            cache_configuracion_cita_anulada_motivo_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new ExpedienteJudicialResource(ExpedienteJudicial::query()->findOrFail($id));
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
            $record = ExpedienteJudicial::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            cache_configuracion_cita_anulada_motivo_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
