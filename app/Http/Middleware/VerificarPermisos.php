<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermisos
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permisos): Response
    {
        $user = auth()->user();
        //dd($user->getAllPermissions()->pluck('name')->toArray());
        if (!$user || !$user->hasAnyPermission($permisos)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para acceder a esta ruta.'
                ], 403);
            }

            abort(403, 'No tienes permiso para acceder.');
        }

        return $next($request);
    }
}
