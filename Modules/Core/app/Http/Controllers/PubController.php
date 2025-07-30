<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Core\DataTables\PublicidadDataTable;
use Modules\Core\Http\Requests\PublicidadRequest;
use Modules\Core\Http\Resources\PublicidadResource;
use Modules\Core\Models\Parametro;
use Modules\Core\Models\Publicidad;
use Modules\Core\Models\PublicidadTipo;

class PubController extends Controller
{
    use PublicidadDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Modulos/Core/Pub/PubIndex');
    }

    public function visualizar(Request $request): Response
    {
        return Inertia::render('Modulos/Core/PubVisualizar/PubVisualizarIndex');
    }

    public function initTable()
    {
        $publicidadTipos = PublicidadTipo::query()
            ->get()
            ->transform(function ($row) {
                return [
                    'value' => $row->IdPublicidadTipo,
                    'label' => $row->Nombre,
                ];
            })
            ->toArray();

        return [
            'publicidadTipos' => $publicidadTipos,
        ];
    }

    public function show($id)
    {
        $record = Publicidad::query()->find($id);
        if (!$record) {
            return obtener_respuesta_error('EL REGISTRO NO FUE ENCONTRADO DENTRO DE NUESTRA BASE DE DATOS');
        }
        return new PublicidadResource($record);
    }

    public function store(PublicidadRequest $request)
    {
        try {
            $data = $request->all();
            if (!empty($request->id)) {
                $record = Publicidad::query()->findOrFail($request->id);
                $record->update($request->all());
            } else {
                $data['IdPublicidad'] = 1 + (int)Publicidad::query()->max('IdPublicidad') ?? 0;
                $record = Publicidad::query()->create($data);
            }

//            cache_core_parametros_limpiar();
            return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordDestroy($id)
    {
        $record = new PublicidadResource(Publicidad::query()->findOrFail($id));
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
            $record = Publicidad::query()->findOrFail($request->input('id'));
            $record->delete();
//            cache_core_parametros_limpiar();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function getConfiguracion()
    {
        $cantidadPorPantalla = Parametro::query()
            ->where('grupo', 'PUBLICIDAD')
            ->where('codigo', 'cantidadPorPantalla')
            ->first();

        $tiempoPorPantalla = Parametro::query()
            ->where('grupo', 'PUBLICIDAD')
            ->where('codigo', 'tiempoPorPantalla')
            ->first();

        $tiempoPorPantallaHorizontal = Parametro::query()
            ->where('grupo', 'PUBLICIDAD')
            ->where('codigo', 'tiempoPorPantallaHorizontal')
            ->first();

        $imagenPublicidad = Parametro::query()
            ->where('grupo', 'PUBLICIDAD')
            ->where('codigo', 'imagenPublicidad')
            ->first();

        $contenidoInferior = Parametro::query()
            ->where('grupo', 'PUBLICIDAD')
            ->where('codigo', 'contenidoInferior')
            ->first();

        $recordsVertical = Publicidad::query()
            ->with('tipo')
            ->where('PosicionVertical', true)
            ->where('Estado', true)
            ->inRandomOrder()
            ->get()->transform(function ($row) {
                return [
                    'Titulo' => $row->Titulo,
                    'Descripcion' => $row->Descripcion,
                    'TamanoLetra' => $row->TamanoLetra,
                    'ColorLetra' => $row->tipo->ColorLetra,
                    'ColorFondo' => $row->tipo->ColorFondo,
                ];
            })->toArray();

        $recordsHorizontal = Publicidad::query()
            ->with('tipo')
            ->where('PosicionVertical', false)
            ->where('Estado', true)
            ->inRandomOrder()
            ->get()->transform(function ($row) {
                return [
                    'Descripcion' => $row->Descripcion,
                    'TamanoLetra' => $row->TamanoLetra,
                    'ColorLetra' => $row->tipo->ColorLetra,
                    'ColorFondo' => $row->tipo->ColorFondo,
                ];
            })->toArray();

        return [
            'configuracion' => [
                'cantidadPorPantalla' => $cantidadPorPantalla->ValorInt,
                'tiempoPorPantalla' => $tiempoPorPantalla->ValorInt,
                'tiempoPorPantallaHorizontal' => $tiempoPorPantallaHorizontal->ValorInt,
                'imagenPublicidad' => $imagenPublicidad->ValorTexto,
                'contenidoInferior' => $contenidoInferior->ValorTexto
            ],
            'recordsVertical' => $recordsVertical,
            'recordsHorizontal' => $recordsHorizontal,
        ];
    }

    public function recordActive($id)
    {
        $record = new PublicidadResource(Publicidad::query()->findOrFail($id));
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
            $record = Publicidad::query()->find($request->input('id'));
            $record->update([
                'Estado' => !$record->Estado,
            ]);

            return obtener_respuesta_exito('EL ESTADO DE ' . $record->Nombre . ' FUE ACTUALIZADO DE FORMA EXITOSA A ' . (($record->Estado) ? 'ACTIVO' : 'INACTIVO'));
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
