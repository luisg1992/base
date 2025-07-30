<?php

use Modules\Farmacia\Http\Controllers\NotaIngresoAlmacenController;
use Modules\Farmacia\Http\Controllers\NotaSalidaAlmacenController;

Route::middleware('auth')
    ->group(function () {

        Route::prefix('farmacia/notas_ingresos_almacen')
            ->controller(NotaIngresoAlmacenController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/registrar', 'create');
            });

        Route::prefix('farmacia/notas_salidas_almacen')
            ->controller(NotaSalidaAlmacenController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/registrar', 'create');
            });

        require __DIR__ . '/route.php';
    });
