<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\Models\Especialidad;
use Modules\Configuracion\Models\EspecialidadCE;
use Modules\Configuracion\DataTables\EspecialidadDataTable;
use Modules\Configuracion\Http\Requests\EspecialidadRequest;
use Modules\Configuracion\Http\Resources\EspecialidadResource;

class EspecialidadController extends Controller
{
    use EspecialidadDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/Especialidad/IndexEspecialidad');
    }

    public function show($id)
    {
        $record = Especialidad::with('especialidadCE')->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new EspecialidadResource($record);
    }

    public function store(EspecialidadRequest $request)
    {
        if (!empty($request->id)) {
            $record = Especialidad::query()->where('IdEspecialidad', $request->id)->first();
            $record->Nombre = $request['Nombre'];
            $record->IdDepartamento = $request['IdDepartamento'];
            $record->IdEspecialidadPrimaria = $request['IdEspecialidadPrimaria'];
            $record->TiempoPromedioAtencion = $request['TiempoPromedioAtencion'];
            $record->save();
        } else {
            $record = Especialidad::query()->create([
                'Nombre' => $request['Nombre'],
                'IdDepartamento' => $request['IdDepartamento'],
                'IdEspecialidadPrimaria' => $request['IdEspecialidadPrimaria'],
                'TiempoPromedioAtencion' => $request['TiempoPromedioAtencion'],
                'IdEstado' => $request['IdEstado']
            ]);
        }

        if (!empty($request['IdProductoConsulta']) || !empty($request['IdProductoInterconsulta'])) {
            EspecialidadCE::query()->updateOrCreate([
                'IdEspecialidad' => $record->IdEspecialidad
            ], [
                'TiempoPromedioAtencion' => $request['TiempoPromedioAtencion'],
                'IdProductoConsulta' => $request['IdProductoConsulta'],
                'IdProductoInterconsulta' => $request['IdProductoInterconsulta'],
            ]);
        }

        cache_configuracion_especialidades_limpiar();
        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }

    public function recordDestroy($id)
    {
        $record = new EspecialidadResource(Especialidad::query()->findOrFail($id));
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

        DB::beginTransaction();
        try {
            EspecialidadCE::query()->where('IdEspecialidad', $request->input('id'))->delete();
            $record = Especialidad::query()->findOrFail($request->input('id'));
            $record->delete();
            DB::commit();
            cache_configuracion_especialidades_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            DB::rollBack();
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new EspecialidadResource(Especialidad::query()->findOrFail($id));
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
            $record = Especialidad::query()->find($request->input('id'));
            $record->update([
                'IdEstado' => !$record->IdEstado,
            ]);
            cache_configuracion_especialidades_limpiar();
            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Nombre . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->IdEstado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function duplicar(Request $request)
    {
        DB::beginTransaction();
        try {
            $codigo = $request->input('codigo');
            $original = Especialidad::query()->findOrFail($codigo);
            $especialidadCE = EspecialidadCE::query()->where('IdEspecialidad', $original->IdEspecialidad)->first();
            $tiempoPromedioAtencion = $especialidadCE ? $especialidadCE->TiempoPromedioAtencion : null;
            $idProductoConsulta = $especialidadCE ? $especialidadCE->IdProductoConsulta : null;
            $idProductoInterconsulta = $especialidadCE ? $especialidadCE->IdProductoInterconsulta : null;

            $duplicated = $original->replicate();
            $duplicated->Nombre = $original->Nombre . ' - Copia';
            $duplicated->save();

            EspecialidadCE::query()->create([
                'IdEspecialidad' => $duplicated->IdEspecialidad,
                'TiempoPromedioAtencion' => $tiempoPromedioAtencion,
                'IdProductoConsulta' => $idProductoConsulta,
                'IdProductoInterconsulta' => $idProductoInterconsulta,
            ]);
            DB::commit();
            cache_configuracion_especialidades_limpiar();
            return obtener_respuesta_exito('Especialidad duplicada con éxito');
        } catch (Exception $e) {
            DB::rollBack();
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
