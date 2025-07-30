<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Configuracion\DataTables\DiagnosticoDataTable;
use Modules\Configuracion\Http\Requests\DiagnosticoRequest;
use Modules\Configuracion\Http\Resources\DiagnosticoResource;
use Modules\Configuracion\Models\Diagnostico;

class DiagnosticoController extends Controller
{
    use DiagnosticoDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/Diagnostico/IndexDiagnostico');
    }

    public function show($id)
    {
        $record = Diagnostico::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new DiagnosticoResource($record);
    }
    public function store(DiagnosticoRequest $request)
    {

        $data = $request->validated();

        // Campos derivados
        $data['CodigoCIE10'] = $data['CodigoCIE10'] ?? null;
        $data['codigoCIEsinPto'] = $data['CodigoCIE10'] ?? null;


        if (!empty($request->id)) {
            $record = Diagnostico::findOrFail($request->id);
            $record->update($data);
        } else {
            $record = Diagnostico::create($data);
        }

        return obtener_respuesta_exito('Datos guardados correctamente', $record);
    }


    public function recordDestroy($id)
    {
        $record = new DiagnosticoResource(Diagnostico::query()->findOrFail($id));
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
            $record = Diagnostico::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new DiagnosticoResource(Diagnostico::query()->findOrFail($id));
        return [
            'title' => 'ESTÁ SEGURO QUE SEA CAMBIAR EL EsActivo DEL REGISTRO SELECCIONADO: ' . $record->Descripcion . '?',
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
            $record = Diagnostico::query()->find($request->input('id'));
            $record->update([
                'EsActivo' => !$record->EsActivo,
            ]);

            return obtener_respuesta_exito('EL Estado DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->EsActivo == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
