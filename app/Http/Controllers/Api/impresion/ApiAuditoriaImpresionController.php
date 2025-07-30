<?php

namespace App\Http\Controllers\Api\impresion;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Auditoria\Models\AuditoriaImpresion;

class ApiAuditoriaImpresionController extends Controller
{
    public function store($dataJson)
    {
        $record = AuditoriaImpresion::on('sqlsrvAuditoria')->create([
            'IdExterna' => Str::uuid()->toString(),
            'DataJson' => $dataJson,
            'FechaRegistro' => now()->toDateString(),
            'IdEmpleadoRegistra' => Auth::user()->id,
            'Ipv4' => Auth::user()->IpTerminalLogin ?? '',
            'Ipv6' => Auth::user()->IpV6 ?? '',
            'Hostname' => Auth::user()->TerminalLogin ?? '',
            'Mac' => Auth::user()->MacLogin ?? '',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrado satisfactoriamente',
            'data' => [
                'external_id' => $record->IdExterna,
            ]
        ], 201);
    }

    public function auditoria_impresion(Request $request)
    {
        $record = AuditoriaImpresion::on('sqlsrvAuditoria')
            ->where('IdExterna', $request->external_id)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró resultado'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => ($record->DataJson)
        ]);
    }
}
