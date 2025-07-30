<?php

namespace Modules\Farmacia\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Farmacia\DataTables\NotaIngresoFarmaciaDataTable;
use Modules\Farmacia\Http\Requests\NotaIngresoFarmaciaRequest;
use Modules\Farmacia\Models\NotaIngresoFarmacia;
use Modules\Impresora\Http\Resources\NotaIngresoFarmaciaResource;

class NotaIngresoFarmaciaController extends Controller
{
    use NotaIngresoFarmaciaDataTable;

    public function getRecord($id)
    {
        $record = new NotaIngresoFarmaciaResource(NotaIngresoFarmacia::query()->findOrFail($id));

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
        $response = validate_password($request);
        if (!$response['success']) {
            return $response;
        }

        try {
            $record = NotaIngresoFarmacia::query()->findOrFail($request->input('id'));
            $record->delete();

            return get_response_success('Farmacia eliminada con éxito');
        } catch (Exception $e) {
            return get_response_error($e);
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
