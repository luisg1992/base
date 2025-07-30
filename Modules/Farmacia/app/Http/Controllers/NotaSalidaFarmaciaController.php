<?php

namespace Modules\Farmacia\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Farmacia\DataTables\NotaSalidaFarmaciaDataTable;
use Modules\Farmacia\Http\Requests\NotaSalidaFarmaciaRequest;
use Modules\Farmacia\Models\NotaSalidaFarmacia;
use Modules\Impresora\Http\Resources\NotaSalidaFarmaciaResource;

class NotaSalidaFarmaciaController extends Controller
{
    use NotaSalidaFarmaciaDataTable;

    public function getRecord($id)
    {
        $record = new NotaSalidaFarmaciaResource(NotaSalidaFarmacia::query()->findOrFail($id));

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
        $response = validate_password($request);
        if (!$response['success']) {
            return $response;
        }

        try {
            $record = NotaSalidaFarmacia::query()->findOrFail($request->input('id'));
            $record->delete();

            return get_response_success('Farmacia eliminada con éxito');
        } catch (Exception $e) {
            return get_response_error($e);
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
