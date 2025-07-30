<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\Http\Requests\ImpresoraRequest;
use Modules\Configuracion\Http\Resources\ImpresoraResource;
use Modules\Configuracion\Models\Impresora;

class ImpresoraController extends Controller
{

    public function listarTablas(Request $request)
    {
        $impresoras = Impresora::query()
            ->where('IdTerminales', $request->IdTerminales)
            ->get(['ImpresorasId', 'IdTerminales', 'Nombre', 'Estado', 'PorDefecto', 'Formato']);
        return [
            'terminales' => $impresoras
        ];
    }

    public function show($id)
    {
        $record = Impresora::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new ImpresoraResource($record);
    }

    public function store(ImpresoraRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = Impresora::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = Impresora::create($data);
        }
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new ImpresoraResource(Impresora::query()->findOrFail($id));
        return obtener_respuesta_eliminar_record(
            'ESTÁ SEGURO DE ELIMINAR EL ITEM SELECCIONADO: ' . $record->Nombre . '?',
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
            $record = Impresora::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new ImpresoraResource(Impresora::query()->findOrFail($id));
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
            $record = Impresora::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
                'PorDefecto' => 0
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Nombre . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
