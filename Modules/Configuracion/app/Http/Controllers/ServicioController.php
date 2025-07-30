<?php

namespace Modules\Configuracion\Http\Controllers;

use App\Core\Services\StoredProcedureService;
use Exception;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\Http\Requests\ServicioRequest;
use Modules\Configuracion\Models\Especialidad;
use Modules\Configuracion\Models\FactPuntoCarga;
use Modules\Configuracion\Models\Servicio;
use Modules\Configuracion\DataTables\ServicioDataTable;
use Modules\Configuracion\Http\Resources\ServicioResource;
use Modules\Configuracion\Models\ServicioAtencionSimultanea;

class ServicioController extends Controller
{
    use ServicioDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/Servicio/IndexServicio');
    }

    public function show($id)
    {
        $record = Servicio::query()->with('factPuntoCarga')->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new ServicioResource($record);
    }

    public function store(ServicioRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();

            if ($data['TipoEdad'] === 1) {
                $minimaEdad = $data['MinimaEdad'] * 365;
                $maximaEdad = $data['maximaEdad'] * 365;
            } elseif ($data['TipoEdad'] === 2) {
                $minimaEdad = $data['MinimaEdad'] * 30;
                $maximaEdad = $data['maximaEdad'] * 30;
            } elseif ($data['TipoEdad'] === 3) {
                $minimaEdad = $data['MinimaEdad'];
                $maximaEdad = $data['maximaEdad'];
            } else {
                $minimaEdad = 0;
                $maximaEdad = 0;
            }

            $data['MinimaEdad'] = $minimaEdad;
            $data['maximaEdad'] = $maximaEdad;
            $data['terapiaGhoraInicio'] = ($data['terapiaGhoraInicio'] === null) ? '__:__' : $data['terapiaGhoraInicio'];
            $data['CostoCeroCE'] = ($data['CostoCeroCE']) ? 'S' : null;
            $data['UsaGalenHos'] = $data['UsaGalenHosEmergencia'] || $data['UsaGalenHos'];

            if (!empty($request->id)) {
                unset($data['Codigo']);
                $record = Servicio::query()->findOrFail($request->id);
                $record->update($data);
                $record->factPuntoCarga()->delete();
            } else {
                // Obtener el último número correlativo según tipo y especialidad
                $ultimoNumero = Servicio::query()
                    ->where('IdTipoServicio', $data['IdTipoServicio'])
                    ->where('IdEspecialidad', $data['IdEspecialidad'])
                    ->max('Numero');
                // Calcular el nuevo número
                $numero = $ultimoNumero ? $ultimoNumero + 1 : 1;
                $data['Numero'] = $numero;
                // Construir el código concatenado
                $codigoBase = $data['IdTipoServicio'] . $data['IdDepartamento'] . $data['IdEspecialidadPrimaria'] . $data['IdEspecialidad'];
                $data['Codigo'] = $codigoBase . str_pad($numero, 20 - strlen($codigoBase), '0', STR_PAD_LEFT);
                $record = Servicio::query()->create($data);
            }

            if ($data['TienePuntoDeCarga']) {
                $record->factPuntoCarga()->create([
                    'IdPuntoCarga' => 500 + (int)$record->IdServicio,
                    'Descripcion' => $data['Nombre'],
                    'TipoPunto' => null,
                    'IdUPS' => 5,
                    'idServicio' => $record->IdServicio,
                ]);
            }

            DB::commit();
            return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
        } catch (\Throwable $th) {
            DB::rollBack();
            return obtener_respuesta_error($th->getMessage());
        }
    }

    public function recordDestroy($id)
    {
        $record = new ServicioResource(Servicio::query()->findOrFail($id));
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
            $record = Servicio::query()->findOrFail($request->input('id'));
            $record->factPuntoCarga()->delete();
            $record->delete();
            DB::commit();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            DB::rollBack();
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new ServicioResource(Servicio::query()->findOrFail($id));
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
            $record = Servicio::query()->find($request->input('id'));
            $record->update([
                'idEstado' => !$record->idEstado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Nombre . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->idEstado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function filtrarCatalogoEmergencias(Request $request)
    {
        $parameters = [
            '',
            $request->input('buscar'),
            '2'
        ];
        $service_procedure = new StoredProcedureService();
        $records = $service_procedure->executeSinPaginacion('FactCatalogoServiciosSeleccionarPorCodigoOnombreTipoCatalogo', $parameters);

        return $records;
    }

    public function filtrarCatalogoEmergenciaPorCodigo($codigo)
    {
        $parameters = [
            $codigo
        ];
        $service_procedure = new StoredProcedureService();
        $records = collect($service_procedure->executeSinPaginacion('FactCatalogoServiciosSeleccionarPorId', $parameters))
            ->transform(function ($row) {
                return [
                    'IdProducto' => (int)$row->IdProducto,
                    'Nombre' => $row->Nombre,
                ];
            })->toArray();

        return $records;
    }

    public function listarUpsAdicionales($codigo)
    {
        $parameters = [
            ' idTipoServicio=1 and idservicio<>' . $codigo,
            ' order by nombre',
        ];
        $service_procedure = new StoredProcedureService();
        $records = $service_procedure->executeSinPaginacion('ServiciosSeleccionarPorFiltro', $parameters);

        return $records;
    }

    public function upsAdicionales($codigo)
    {
        $parameters = [
            $codigo
        ];
        $service_procedure = new StoredProcedureService();
        $records = $service_procedure->executeSinPaginacion('ServiciosAtenSimultaneaSeleccionarXidServicio', $parameters);

        return $records;
    }

    public function agregarUpsAdicional(Request $request)
    {
        try {
            ServicioAtencionSimultanea::query()->create([
                'idServicio' => $request->input('IdServicio'),
                'idServicioAtencionSimultanea' => $request->input('IdServicioAtencionSimultanea'),
            ]);
            return [
                'success' => true,
                'message' => 'Servicio adicional registrado exitosamente.',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function eliminarUpsAdicional(Request $request)
    {
        try {
            ServicioAtencionSimultanea::query()
                ->where('idServicio', $request->input('IdServicio'))
                ->where('idServicioAtencionSimultanea', $request->input('IdServicioAtencionSimultanea'))
                ->delete();
            return [
                'success' => true,
                'message' => 'Servicio adicional eliminado exitosamente.',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function duplicar(Request $request)
    {
        DB::beginTransaction();
        try {
            $codigo = $request->input('codigo');
            $original = Servicio::query()->findOrFail($codigo);

            $ultimoNumero = Servicio::query()
                ->where('IdTipoServicio', $original->IdTipoServicio)
                ->where('IdEspecialidad', $original->IdEspecialidad)
                ->max('Numero');

            $especialidad = Especialidad::query()->find($original->IdEspecialidad);

            $numero = $ultimoNumero ? $ultimoNumero + 1 : 1;
            $codigoBase = $original->IdTipoServicio . $especialidad->IdDepartamento . $especialidad->IdEspecialidadPrimaria . $original->IdEspecialidad;
            $codigoFinal = $codigoBase . str_pad($numero, 20 - strlen($codigoBase), '0', STR_PAD_LEFT);

            $duplicated = $original->replicate();
            $duplicated->Nombre = $original->Nombre . ' - Copia';
            $duplicated->Codigo = $codigoFinal;
            $duplicated->Numero = $numero;
            $duplicated->save();

            $factPuntoCarga = FactPuntoCarga::query()->where('idServicio', $codigo)->first();
            if($factPuntoCarga) {
                $factPuntoCargaDuplicated = $factPuntoCarga->replicate();
                $factPuntoCargaDuplicated->idServicio = $duplicated->IdServicio;
                $factPuntoCargaDuplicated->save();
            }
            DB::commit();
            return obtener_respuesta_exito('Especialidad duplicada con éxito');
        } catch (Exception $e) {
            DB::rollBack();
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
