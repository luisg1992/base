<?php

namespace App\Providers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::version(fn() => md5_file(public_path('mix-manifest.json'))); // o vite-manifest.json si usas Vite

        Inertia::share([
            'auth' => function () {
                $user = Auth::user();
                $empleado = $user?->empleado;

                $nombreCompleto = $empleado
                    ? trim("{$empleado->ApellidoPaterno} {$empleado->Nombres}")
                    : $user?->name;

                // Determinar la imagen del avatar
                $avatar = null;

                // if ($empleado?->ImagenFoto) {
                //     $avatar = asset('storage/fotos/' . $empleado->ImagenFoto);
                // } else {
                $sexo = $empleado?->sexo;
                $avatar = asset($sexo == 1
                    ? '../../assets/img/sexo1.gif'
                    : '../../assets/img/sexo2.gif');
                // }

                return [
                    'user' => $user ? [
                        'name' => $nombreCompleto,
                        'email' => $user->email,
                        'avatar' => $avatar,
                        'IpTerminalLogin' => $user->IpTerminalLogin,
                        'TerminalLogin' => $user->TerminalLogin,
                        'anioActual' => date('Y'),
                    ] : null,
                    'esAdministrador' => $user?->esAdministrador() ?? false,
                ];
            },
        ]);
    }
}
