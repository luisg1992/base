<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Http\Requests\ModuloAccionRequest;
use Modules\Core\Http\Resources\ModuloResource;
use Modules\Core\Models\Modulo;
use Modules\Core\Models\ModuloAccion;
use Spatie\Permission\Models\Permission;

class ModuloAccionController extends Controller
{
    public function recordDestroy($id)
    {
        $record = new ModuloResource(Modulo::query()->findOrFail($id));
        return obtener_respuesta_eliminar_record(
            'ESTÁ SEGURO DE ELIMINAR EL ITEM SELECCIONADO: ' . $record->nombre . '?',
            true,
            $record
        );
    }

    public function store(ModuloAccionRequest $request)
    {
        $data = $request->all();
        $modulo = Modulo::query()->findOrFail($data['ModuloId']);
        $accionValor = Str::slug($data['Nombre'], '.');
        $data['Valor'] = $accionValor;
        $record = ModuloAccion::query()->create($data);

        Permission::findOrCreate("{$modulo->Valor}.{$accionValor}");

        return obtener_respuesta_exito('LOS DATOS ENVIADOS FUERON PROCESADOS DE FORMA EXITOSA', $record);
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $record = ModuloAccion::query()->findOrFail($request->input('id'));
            $modulo = Modulo::query()->findOrFail($record->ModuloId);
            Permission::query()->where('name', "{$modulo->Valor}.{$record->Valor}")->delete();
            $record->delete();
            DB::commit();
            return obtener_respuesta_exito('EL ITEM SELECCIONADO FUE ELIMINADO DE FORMA EXITOSA.');
        } catch (Exception $e) {
            DB::rollBack();
            return obtener_respuesta_error($e->getMessage());
        }
    }
}
