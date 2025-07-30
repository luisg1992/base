<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    protected $timeout = 1800; // 1800 segundos = 30 minutos

    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('lastActivityTime');
            if ($lastActivity && (time() - $lastActivity) > $this->timeout) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    // Para peticiones AJAX/Fetch/Inertia
                    return response()->json([
                        'success' => false,
                        'message' => 'Sesión expirada por inactividad'
                    ], 401);
                } else {
                    // Petición normal HTTP
                    return redirect('/login')->withErrors(['message' => 'Sesión expirada por inactividad']);
                }
            }
            session(['lastActivityTime' => time()]);
        }

        return $next($request);
    }
}
