<?php

namespace Modules\Configuracion\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Configuracion\DataTables\FuenteFinanciamientoDataTable;
use Modules\Configuracion\Http\Requests\FuenteFinanciamientoRequest;
use Modules\Configuracion\Http\Resources\FuenteFinanciamientoResource;
use Modules\Configuracion\Models\FuenteFinanciamiento;
use Modules\Configuracion\Models\FuenteFinanciamientoTarifas;

class FuenteFinanciamientoController extends Controller
{
    use FuenteFinanciamientoDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Configuracion/FuenteFinanciamiento/IndexFuenteFinanciamiento');
    }

    public function show($id)
    {
        $record = FuenteFinanciamiento::with('tarifas')->find($id);

        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }

        return new FuenteFinanciamientoResource($record);
    }


    public function store(FuenteFinanciamientoRequest $request)
    {
        $data = $request->validated();

        if (!empty($request->id)) {
            $record = FuenteFinanciamiento::findOrFail($request->id);
            $record->update($data);
            // Eliminar tarifas actuales
            FuenteFinanciamientoTarifas::where('idFuenteFinanciamiento', $record->IdFuenteFinanciamiento)->delete();

            // Insertar tarifas nuevas
            if (isset($data['fuentefinanciamiento']) && is_array($data['fuentefinanciamiento'])) {
                foreach ($data['fuentefinanciamiento'] as $item) {
                    if (!isset($item['IdTipoFinanciamiento'])) continue;

                    FuenteFinanciamientoTarifas::create([
                        'idFuenteFinanciamiento' => $record->IdFuenteFinanciamiento,
                        'idTipoFinanciamiento' => $item['IdTipoFinanciamiento'],
                    ]);
                }
            }
        } else {
            $lastId = FuenteFinanciamiento::max('IdFuenteFinanciamiento');
            $lastOrder = FuenteFinanciamiento::max('Orden');

            $data['IdFuenteFinanciamiento'] = $lastId ? $lastId + 1 : 1;
            $data['Orden'] = $lastOrder ? $lastOrder + 1 : 1;

            $record = FuenteFinanciamiento::create($data);

            if (isset($data['fuentefinanciamiento']) && is_array($data['fuentefinanciamiento'])) {
                foreach ($data['fuentefinanciamiento'] as $item) {
                    if (!isset($item['IdTipoFinanciamiento'])) {
                        continue; // O lanza un error si quieres
                    }

                    FuenteFinanciamientoTarifas::create([
                        'idFuenteFinanciamiento' => $record->IdFuenteFinanciamiento,
                        'idTipoFinanciamiento' => $item['IdTipoFinanciamiento'],
                    ]);
                }
            } else {
                return obtener_respuesta_error('Datos guardados correctamente', $record);
            }
        }
        // cache_configuracion_tipo_financiamiento_limpiar();
        return obtener_respuesta_exito('Datos guardados correctamente', $record);
    }
    public function recordDestroy($id)
    {
        $record = new FuenteFinanciamientoResource(FuenteFinanciamiento::query()->findOrFail($id));
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
            $record = FuenteFinanciamiento::query()->findOrFail($request->input('id'));
            //ELIMINAR REGISTRO FUENTE FINANCIAMIENTO DE LA TABLA TARIFA
            FuenteFinanciamientoTarifas::where('idFuenteFinanciamiento', $record->IdFuenteFinanciamiento)->delete();

            $record->delete();
            //cache_configuracion_tipo_financiamiento_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new FuenteFinanciamientoResource(FuenteFinanciamiento::query()->findOrFail($id));
        return [
            'title' => 'ESTÁ SEGURO QUE SEA CAMBIAR EL idEstado DEL REGISTRO SELECCIONADO: ' . $record->Descripcion . '?',
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
            $record = FuenteFinanciamiento::query()->find($request->input('id'));
            $record->update([
                'idEstado' => !$record->idEstado,
            ]);
            // cache_configuracion_tipo_financiamiento_limpiar();
            return obtener_respuesta_exito('EL Estado DE ' . $record->Descripcion . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->idEstado == 1) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}