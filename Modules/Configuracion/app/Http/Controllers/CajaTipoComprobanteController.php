<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\CajaTipoComprobanteDataTable;
use Modules\Configuracion\Http\Requests\CajaTipoComprobanteRequest;
use Modules\Configuracion\Http\Resources\CajaTipoComprobanteResource;
use Modules\Configuracion\Models\CajaTipoComprobante;


class CajaTipoComprobanteController extends Controller
{
    use CajaTipoComprobanteDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/CajaTipoComprobante/IndexCajaTipoComprobante');
    }

    public function show($id)
    {
        $record = CajaTipoComprobante::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new CajaTipoComprobanteResource($record);
    }

    public function store(CajaTipoComprobanteRequest $request)
    {
        $data = $request->validated();
        $id= $request->id;
        if (!empty($id)) {
            $record = CajaTipoComprobante::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = CajaTipoComprobante::max('IdTipoComprobante');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['IdTipoComprobante'] = $nuevoId;

            $record = CajaTipoComprobante::create($data);
        }
        $mensaje = ($id) ? 'El registro fue actualizado' : 'El registro fue registrado';

        return obtener_respuesta_exito($mensaje, $record);
    }

    public function recordDestroy($id)
    {
        $record = new CajaTipoComprobanteResource(CajaTipoComprobante::query()->findOrFail($id));
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
            $record = CajaTipoComprobante::query()->findOrFail($request->input('id'));
            $record->delete();
            return obtener_respuesta_exito('El registro fue eliminado de forma exitosa.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new CajaTipoComprobanteResource(CajaTipoComprobante::query()->findOrFail($id));
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
            $record = CajaTipoComprobante::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('El estado de ' . $record->Descripcion . ' fue actualizado de forma exitosa a ' . (($record->Estado == 1) ? 'Activo' : 'Inactivo'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
