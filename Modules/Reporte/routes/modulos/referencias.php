<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Reporte\Http\Controllers\ReferenciaController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('reportes/referencias')
    ->middleware('auth')
    ->controller(ReferenciaController::class)
    ->as($as)
    ->group(function () use ($as) {
        Route::middleware("verificar.permisos:{$as}.acceder")->group(function () {
            Route::get('/', 'index'); 
            Route::post('/records', 'records');
        });
    });
