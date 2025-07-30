<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;
use Modules\Core\Models\Modulo;

class ModuloHelper
{
    public static function obtenerPermisoBaseDesdeRuta($path = null): ?string
    {
        // Obtener los primeros dos segmentos de la ruta
        if ($path) {
            $pathArray = explode('/', trim($path, '/')); // Quitar slashes iniciales/finales
            $rutaSegmentos = array_slice($pathArray, 0, 2);
        } else {
            $rutaSegmentos = array_filter([
                request()->segment(1),
                request()->segment(2)
            ]);
        }

        $rutaActual = implode('/', $rutaSegmentos);

        $modulo = Modulo::query()->where('Url', $rutaActual)->first();

        return $modulo?->Valor;
    }
}
