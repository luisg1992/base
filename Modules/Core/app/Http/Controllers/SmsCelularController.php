<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Api\Http\Controllers\Sms\SmsController;
use Modules\Core\DataTables\SmsCelularDataTable;
use Modules\Core\Http\Requests\SmsCelularRequest;
use Modules\Core\Http\Resources\SmsCelularResource;
use Modules\Core\Models\SmsCelular;

class SmsCelularController extends Controller
{
    use SmsCelularDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Core/SmsCelular/SmsCelularIndex');
    }

    public function show($id)
    {
        $record = SmsCelular::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new SmsCelularResource($record);
    }

    public function store(SmsCelularRequest $request)
    {
        try {
            $data = $request->all();
            if (!empty($request->id)) {
                $record = SmsCelular::query()->findOrFail($request->id);
                $record->update($request->all());
            } else {
                $data['IdSmsCelular'] = 1 + (int)SmsCelular::query()->max('IdSmsCelular') ?? 0;
                $record = SmsCelular::query()->create($data);
            }

            return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordDestroy($id)
    {
        $record = new SmsCelularResource(SmsCelular::query()->findOrFail($id));
        return obtener_respuesta_eliminar_record(
            'ESTÁ SEGURO DE ELIMINAR EL ITEM SELECCIONADO: ' . $record->nombre . '?',
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
            $record = SmsCelular::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new SmsCelularResource(SmsCelular::query()->findOrFail($id));
        return [
            'title' => 'ESTÁ SEGURO QUE SEA CAMBIAR EL ESTADO DEL REGISTRO SELECCIONADO: ' . $record->Nombre . '?',
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
            $record = SmsCelular::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Nombre . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function enviarSms($id)
    {
        $smsCelular = SmsCelular::query()->find($id);

        return (new SmsController())->sendByFormParams([
            'title' => 'Mensaje de prueba',
            'body' => 'Hola, este es un mensaje de prueba del usuario: ' . $smsCelular->Usuario,
            'phone' => '924899800',
        ], $id);
    }
}
