<?php

namespace Modules\Configuracion\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Configuracion\DataTables\ProgramaTipoDocumentoDataTable;
use Modules\Configuracion\Http\Requests\ProgramaTipoDocumentoRequest;
use Modules\Configuracion\Http\Resources\ProgramaTipoDocumentoResource;
use Modules\Configuracion\Models\ProgramaTipoDocumento;

class ProgramaTipoDocumentoController extends Controller
{
    use ProgramaTipoDocumentoDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/ProgramasTiposDocumentos/IndexProgramasTiposDocumentos');
    }

    public function show($id)
    {
        $record = ProgramaTipoDocumento::query()->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new ProgramaTipoDocumentoResource($record);
    }

    public function store(ProgramaTipoDocumentoRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = ProgramaTipoDocumento::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = ProgramaTipoDocumento::max('IdTipoDocumento');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['IdTipoDocumento'] = $nuevoId;

            $record = ProgramaTipoDocumento::create($data);
        }
        cache_programas_tipos_documentos_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new ProgramaTipoDocumentoResource(ProgramaTipoDocumento::query()->findOrFail($id));
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
            $record = ProgramaTipoDocumento::query()->findOrFail($request->input('id'));
            $record->delete();
            cache_programas_tipos_documentos_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new ProgramaTipoDocumentoResource(ProgramaTipoDocumento::query()->findOrFail($id));
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
            $record = ProgramaTipoDocumento::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            cache_programas_tipos_documentos_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
