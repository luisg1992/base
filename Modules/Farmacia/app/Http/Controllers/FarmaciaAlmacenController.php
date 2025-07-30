<?php

namespace Modules\Farmacia\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Farmacia\DataTables\FarmaciaAlmacenDataTable;
use Modules\Farmacia\Http\Requests\FarmaciaAlmacenRequest;
use Modules\Farmacia\Models\FarmaciaAlmacen;
use Modules\Farmacia\Http\Resources\FarmaciaAlmacenResource;

class FarmaciaAlmacenController extends Controller
{
    use FarmaciaAlmacenDataTable;

    public function getRecord($id)
    {
        $record = new FarmaciaAlmacenResource(FarmaciaAlmacen::query()->findOrFail($id));

        return $record;
    }

    public function store(FarmaciaAlmacenRequest $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->input('id');

            $record = FarmaciaAlmacen::query()->firstOrNew(['id' => $id]);
            $record->fill($request->all());
            $record->save();

            DB::commit();

            return [
                'success' => true,
                'message' => ($id) ? 'Almacén actualizado' : 'Almacén registrado'
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
        $record = new FarmaciaAlmacenResource(FarmaciaAlmacen::query()
            ->findOrFail($id));

        return [
            'title' => 'Está seguro de eliminar el almacén: ' . $record->name . '?',
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
            $record = FarmaciaAlmacen::query()->findOrFail($request->input('id'));
            $record->delete();

            return obtener_respuesta_exito('Almacén eliminado con éxito');
        } catch (Exception $e) {
            return obtener_respuesta_error($e);
        }
    }

    public function recordActive($id)
    {
        $record = new FarmaciaAlmacenResource(FarmaciaAlmacen::query()
            ->findOrFail($id));

        return [
            'title' => 'Está seguro que desea cambiar el estado de la almacén: ' . $record->nombre . '?',
            'verify_password' => true,
            'record' => $record,
        ];
    }
}
