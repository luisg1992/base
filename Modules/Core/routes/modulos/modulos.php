<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\ModuloAccionController;
use Modules\Core\Http\Controllers\ModuloController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('core/modulos')
    ->middleware('auth')
    ->controller(ModuloController::class)
    ->as($as)
    ->group(function () use ($as) {
        Route::middleware("verificar.permisos:{$as}.acceder")->group(function () use ($as) {
            Route::get('/', 'index')->name($as.'.index');
            Route::get('/inicializar_tabla', 'inicializarTabla');
            Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas');
            Route::post('/records', 'records');
            Route::post('/get_records', 'getRecords');
            Route::post('/actualizar_orden', 'actualizarOrden');
            Route::get('/lista-tablas', 'listarTablas');
        });
        Route::get('/record/{id}', 'show');
        Route::post('/', 'store');
        Route::get('/record_destroy/{id}', 'recordDestroy');
        Route::post('/destroy', 'destroy');
        Route::get('/record_active/{id}', 'recordActive');
        Route::post('/change_active', 'changeActive');

        Route::post('/obtener-modulo-actual', 'obtenerModuloActual');

    });

Route::post('core/modulos/action', [ModuloAccionController::class, 'store']);
Route::post('core/modulos/action/destroy', [ModuloAccionController::class, 'destroy']);
