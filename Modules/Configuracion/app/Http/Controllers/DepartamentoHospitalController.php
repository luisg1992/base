<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\DepartamentoHospitalDataTable;
use Modules\Configuracion\Http\Requests\DepartamentoHospitalRequest;
use Modules\Configuracion\Http\Resources\DepartamentoHospitalResource;
use Modules\Configuracion\Models\DepartamentoHospital;

class DepartamentoHospitalController extends Controller
{
    use DepartamentoHospitalDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/DepartamentoHospital/IndexDepartamentoHospital');
    }

    public function show($id)
    {
        $record = DepartamentoHospital::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new DepartamentoHospitalResource($record);
    }

    public function store(DepartamentoHospitalRequest $request)
    {
        $data = $request->validated();
        if (!empty($request->id)) {
            $record = DepartamentoHospital::query()->findOrFail($request->id);
            $record->update($data);
        } else {
            $ultimoId = DepartamentoHospital::max('IdDepartamento');
            $nuevoId = $ultimoId ? $ultimoId + 1 : 1;
            $data['IdDepartamento'] = $nuevoId;
 
            $record = DepartamentoHospital::create($data);
        }
        cache_configuracion_departamentos_hospital_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }


    public function recordDestroy($id)
    {
        $record = new DepartamentoHospitalResource(DepartamentoHospital::query()->findOrFail($id));
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
            $record = DepartamentoHospital::query()->findOrFail($request->input('id'));
            $record->delete();
            cache_configuracion_departamentos_hospital_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new DepartamentoHospitalResource(DepartamentoHospital::query()->findOrFail($id));
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
            $record = DepartamentoHospital::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            cache_configuracion_departamentos_hospital_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Nombre . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
