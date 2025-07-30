<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\Models\EspecialidadPrimaria;
use Modules\Configuracion\DataTables\EspecialidadPrimariaDataTable;
use Modules\Configuracion\Http\Requests\EspecialidadPrimariaRequest;
use Modules\Configuracion\Http\Resources\EspecialidadPrimariaResource;

class EspecialidadPrimariaController extends Controller
{
    use EspecialidadPrimariaDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/EspecialidadPrimaria/IndexEspecialidadPrimaria');
    }

    public function show($id)
    {
        $record = EspecialidadPrimaria::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new EspecialidadPrimariaResource($record);
    }

    public function store(EspecialidadPrimariaRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = EspecialidadPrimaria::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $record = EspecialidadPrimaria::create($data);
        }
        cache_configuracion_especialidades_primarias_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }

    public function recordDestroy($id)
    {
        $record = new EspecialidadPrimariaResource(EspecialidadPrimaria::query()->findOrFail($id));
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
            $record = EspecialidadPrimaria::query()->findOrFail($request->input('id'));
            $record->delete();
            cache_configuracion_especialidades_primarias_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new EspecialidadPrimariaResource(EspecialidadPrimaria::query()->findOrFail($id));
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
            $record = EspecialidadPrimaria::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);
            cache_configuracion_especialidades_primarias_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Nombre . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
