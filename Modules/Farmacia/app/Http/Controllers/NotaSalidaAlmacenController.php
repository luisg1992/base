<?php

namespace Modules\Farmacia\Http\Controllers;

use App\Core\Services\StoredProcedureService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Farmacia\DataTables\NotaSalidaAlmacenDataTable;
use Modules\Farmacia\Http\Requests\NotaSalidaFarmaciaRequest;
use Modules\Farmacia\Http\Resources\NotaSalidaAlmacenResource;
use Modules\Farmacia\Models\NotaSalidaFarmacia;
use Modules\Farmacia\Http\Resources\NotaSalidaFarmaciaResource;

class NotaSalidaAlmacenController extends Controller
{
    use NotaSalidaAlmacenDataTable;

    public function index(): Response
    {
        return Inertia::render('Modulos/Farmacia/NotaSalidaAlmacenIndex');
    }

    public function create(): Response
    {
        return Inertia::render('Modulos/Farmacia/NotaSalidaAlmacenForm');
    }

    public function getRecord($id)
    {
        $sp = new StoredProcedureService();
        $params = [
            $id,
            'S',
        ];

        $records = $sp->executeSinPaginacion('dbo.farmMovimientoSeleccionarPorId', $params);
        $detalle = $sp->executeSinPaginacion('dbo.FarmMovimientosDetalleDevuelveTodosItems', $params);
        $cabecera = $records[0];
        $cabecera->detalle = collect($detalle)->toArray();

        $record = new NotaSalidaAlmacenResource($cabecera);
        return $record;
    }

    public function store(NotaSalidaFarmaciaRequest $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->input('id');

            $record = NotaSalidaFarmacia::query()->firstOrNew(['id' => $id]);
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
        $record = new NotaSalidaFarmaciaResource(NotaSalidaFarmacia::query()
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
            $record = NotaSalidaFarmacia::query()->findOrFail($request->input('id'));
            $record->delete();

            return obtener_respuesta_exito('Farmacia eliminada con éxito');
        } catch (Exception $e) {
            return obtener_respuesta_error($e->getMessage());
        }
    }

    public function recordActive($id)
    {
        $record = new NotaSalidaFarmaciaResource(NotaSalidaFarmacia::query()
            ->findOrFail($id));

        return [
            'title' => 'Está seguro que desea cambiar el estado de la farmacia: ' . $record->nombre . '?',
            'verify_password' => true,
            'record' => $record,
        ];
    }
}
