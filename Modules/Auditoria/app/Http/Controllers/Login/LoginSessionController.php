<?php

namespace Modules\Auditoria\Http\Controllers\Login;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\Auditoria\DataTables\Login\LoginSessionDataTable;
use Modules\Auditoria\Http\Resources\Login\LoginSessionResource;
use Modules\Auditoria\Models\Login\LoginSession;

class LoginSessionController extends Controller
{
    use LoginSessionDataTable;

    public function index(Request $request): Response
    {
        return Inertia::render('Auditoria/Login/IndexLoginSesion');
    }

    public function show($id): JsonResponse
    {
        try {
            $sesion = LoginSession::with('user')->findOrFail($id);
            return response()->json([
                'success' => true,
                'mensaje' => 'Sesión obtenida correctamente.',
                'data' => new LoginSessionResource($sesion),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al obtener la sesión de login: ' . $e->getMessage(),
            ], 500);
        }
    }

}
