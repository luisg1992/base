<?php

namespace Modules\AtencionMedica\Http\Controllers\AtencionMedicaSecciones;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Imagenologia\Models\RecetaCabecera;

class AtencionMedicaFarmaciaController extends Controller
{
    public function WebS_ListarDetalleRecetaAtencion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdCuentaAtencion' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'codigo' => 'ERROR',
                'mensaje' => $validator->errors()
            ]);
        }
        $AtencionMedicaReceta = new AtencionMedicaRecetaController();
        $productos_farmacia = $AtencionMedicaReceta->obtenerDetalleReceta($request->IdCuentaAtencion, 5); 
        return response()->json([
            'success' => true,
            'codigo' => 'OK',
            'mensaje' => 'Receta Detalles obtenidos correctamente.',
            'data' => $productos_farmacia
        ]);
    }
}
