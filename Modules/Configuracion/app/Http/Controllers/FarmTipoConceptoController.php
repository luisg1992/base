<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Configuracion\DataTables\FarmTipoConceptoDataTable;
use Modules\Configuracion\Http\Requests\FarmTipoConceptoRequest;
use Modules\Configuracion\Http\Resources\FarmTipoConceptoResource;
use Modules\Configuracion\Models\FarmTipoConcepto;


class FarmTipoConceptoController extends Controller
{
    use FarmTipoConceptoDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/FarmTipoConcepto/IndexFarmTipoConcepto');
    }

    public function show($id)
    {
        $record = FarmTipoConcepto::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new FarmTipoConceptoResource($record);
    }

    public function store(FarmTipoConceptoRequest $request)
    {
        $data = $request->validated();

        if (!empty($request->id)) {
            $record = FarmTipoConcepto::findOrFail($request->id);
            $record->update($data);
        } else {
            $record = FarmTipoConcepto::create($data);
        }
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }

    public function recordDestroy($id)
    {
        $record = new FarmTipoConceptoResource(FarmTipoConcepto::query()->findOrFail($id));
        return obtener_respuesta_eliminar_record(
            'ESTÁ SEGURO DE ELIMINAR EL ITEM SELECCIONADO: ' . $record->Concepto . '?',
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
            $record = FarmTipoConcepto::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new FarmTipoConceptoResource(FarmTipoConcepto::query()->findOrFail($id));
        return [
            'title' => 'ESTÁ SEGURO QUE SEA CAMBIAR EL ESTADO DEL REGISTRO SELECCIONADO: ' . $record->Concepto . '?',
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
            $record = FarmTipoConcepto::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Concepto . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}