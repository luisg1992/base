<?php

namespace Modules\Core\Http\Controllers;

use App\Cache\Core\ParametroCache;
use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Core\DataTables\ParametroDataTable;
use Modules\Core\Http\Requests\ParametroRequest;
use Modules\Core\Http\Resources\ParametroResource;
use Modules\Core\Models\Parametro;

class ParametroController extends Controller
{
    use ParametroDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Core/Parametro/ParametroIndex');
    }

    public function show($id)
    {
        $record = Parametro::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new ParametroResource($record);
    }

    public function store(ParametroRequest $request)
    {
        try {
            $data = $request->all();
            if (!empty($request->id)) {
                $record = Parametro::query()->findOrFail($request->id);
                $record->update($request->all());
            } else {
                $data['IdParametro'] = 1 + (int)Parametro::query()->max('IdParametro') ?? 0;
                $record = Parametro::query()->create($data);
            }

            cache_core_parametros_limpiar();
            return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordDestroy($id)
    {
        $record = new ParametroResource(Parametro::query()->findOrFail($id));
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
            $record = Parametro::query()->findOrFail($request->input('id'));
            $record->delete();
            cache_core_parametros_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function actualizarParametro(Request $request)
    {
        $codigo = $request->input('codigo');
        $parametro = Parametro::query()
            ->where('Codigo', $codigo)
            ->where('Grupo', 'CONSULTA')
            ->first();

        if ($parametro) {
            if ($request->has('valorTexto')) {
                $parametro->ValorTexto = $request->input('valorTexto');
                // dd($request->all());
            }
            if ($request->has('valorInt')) {
                $parametro->ValorInt = $request->input('valorInt');
            }
            if ($request->has('valorFloat')) {
                $parametro->ValorFloat = $request->input('valorFloat');
            }
            if ($codigo === 'REFCON_GALENOS') {
                Parametro::query()
                    ->where('Codigo', 'REFCON_ACCESO')
                    ->where('Grupo', 'CONSULTA')
                    ->update(['ValorTexto' => ($request->input('valorTexto') === 'S') ? 'N' : 'S']);
            }
            $parametro->save();

            ParametroCache::clearCache();
            return obtener_respuesta_exito('EL VALOR DEL PARAMETRO FUE ACTUALIZADO DE FORMA EXITOSA.', [
                'parametros' => $this->obtenerServiciosConsultar()
            ]);
        } else {
            return obtener_respuesta_error('EL PARAMETRO NO FUE ENCONTRADO.');
        }
    }

    public function obtenerServiciosConsultar()
    {
        $parametros = Parametro::query()
            ->where('Grupo', 'CONSULTA')
            ->get();

        $rows = [];
        foreach ($parametros as $row) {
            if ($row->Codigo === 'SIS_ACCESO') {
                $rows[] = [
                    'offLabel' => 'Activar SIS',
                    'onLabel' => 'Desactivar SIS',
                    'value' => 'SIS_ACCESO',
                    'checked' => $row->ValorTexto === 'S',
                ];
            }
            if ($row->Codigo === 'RENIEC_ACCESO') {
                $rows[] = [
                    'offLabel' => 'Activar RENIEC',
                    'onLabel' => 'Desactivar RENIEC',
                    'value' => 'RENIEC_ACCESO',
                    'checked' => $row->ValorTexto === 'S',
                ];
            }
            if ($row->Codigo === 'REFCON_ACCESO') {
                $rows[] = [
                    'offLabel' => 'Activar REFCON',
                    'onLabel' => 'Desactivar REFCON',
                    'value' => 'REFCON_ACCESO',
                    'checked' => $row->ValorTexto === 'S',
                ];
            }
            if ($row->Codigo === 'REFCON_GALENOS') {
                $rows[] = [
                    'offLabel' => 'Activar REFCON GALENOS',
                    'onLabel' => 'Desactivar REFCON GALENOS',
                    'value' => 'REFCON_GALENOS',
                    'checked' => $row->ValorTexto === 'S',
                ];
            }
        }

        return $rows;
    }

}
