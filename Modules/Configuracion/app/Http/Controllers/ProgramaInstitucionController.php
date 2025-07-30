<?php

namespace Modules\Configuracion\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Configuracion\DataTables\ProgramaInstitucionDataTable;
use Modules\Configuracion\Http\Requests\ProgramaInstitucionRequest;
use Modules\Configuracion\Http\Resources\ProgramaInstitucionResource;
use Modules\Configuracion\Models\ProgramaInstitucion;

class ProgramaInstitucionController extends Controller
{
    use ProgramaInstitucionDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/ProgramasInstituciones/IndexProgramasInstituciones');
    }

    public function show($id)
    {
        $record = ProgramaInstitucion::query()->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new ProgramaInstitucionResource($record);
    }

    public function store(ProgramaInstitucionRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = ProgramaInstitucion::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = ProgramaInstitucion::max('IdInstitucion');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['IdInstitucion'] = $nuevoId;

            $record = ProgramaInstitucion::create($data);
        }
        cache_programas_instituciones_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new ProgramaInstitucionResource(ProgramaInstitucion::query()->findOrFail($id));
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
            $record = ProgramaInstitucion::query()->findOrFail($request->input('id'));
            $record->delete();
            cache_programas_instituciones_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new ProgramaInstitucionResource(ProgramaInstitucion::query()->findOrFail($id));
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
            $record = ProgramaInstitucion::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            cache_programas_instituciones_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
