<?php

namespace Modules\Farmacia\Http\Controllers;

use App\Core\Services\StoredProcedureService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Farmacia\DataTables\NotaIngresoAlmacenDataTable;
use Modules\Farmacia\Http\Requests\NotaIngresoFarmaciaRequest;
use Modules\Farmacia\Http\Resources\NotaIngresoAlmacenResource;
use Modules\Farmacia\Models\NotaIngresoFarmacia;
use Modules\Farmacia\Http\Resources\NotaIngresoFarmaciaResource;

class NotaIngresoAlmacenController extends Controller
{
    use NotaIngresoAlmacenDataTable;

    public function index(): Response
    {
        return Inertia::render('Modulos/Farmacia/NotaIngresoAlmacenIndex');
    }

    public function create(): Response
    {
        return Inertia::render('Modulos/Farmacia/NotaIngresoAlmacenForm');
    }

    public function getRecord($id)
    {
        $sp = new StoredProcedureService();
        $params = [
            $id,
            'E',
        ];

        $records = $sp->executeSinPaginacion('dbo.farmMovimientoSeleccionarPorId', $params);
        $detalle = $sp->executeSinPaginacion('dbo.FarmMovimientosDetalleDevuelveTodosItems', $params);
        $cabecera = $records[0];
        $cabecera->detalle = collect($detalle)->toArray();

        $record = new NotaIngresoAlmacenResource($cabecera);
        return $record;
    }

    public function store(NotaIngresoFarmaciaRequest $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->input('id');

            $record = NotaIngresoFarmacia::query()->firstOrNew(['id' => $id]);
            $record->fill($request->all());
            $record->save();

            DB::commit();

            return [
                'success' => true,
                'message' => ($id) ? 'Farmacia actualizada' : 'Farmacia registrada'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function recordDestroy($id)
    {
        $record = new NotaIngresoFarmaciaResource(NotaIngresoFarmacia::query()
            ->findOrFail($id));

        return [
            'title' => 'Está seguro de eliminar la farmacia: ' . $record->name . '?',
            'verify_password' => true,
            'record' => $record,
        ];
    }

    public function destroy(Request $request)
    {
        $response = validacion_password($request);
        if (!$response['success']) {
            return $response;
        }

        try {
            $record = NotaIngresoFarmacia::query()->findOrFail($request->input('id'));
            $record->delete();

            return obtener_respuesta_exito('Farmacia eliminada con éxito');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new NotaIngresoFarmaciaResource(NotaIngresoFarmacia::query()
            ->findOrFail($id));

        return [
            'title' => 'Está seguro que desea cambiar el estado de la farmacia: ' . $record->nombre . '?',
            'verify_password' => true,
            'record' => $record,
        ];
    }
}
