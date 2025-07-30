<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\SetisisController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('core/setisis')
    ->middleware('auth')
    ->controller(SetisisController::class)
    ->as($as)
    ->group(function () use ($as) {
        Route::middleware("verificar.permisos:{$as}.acceder")->group(function () use ($as) {
            Route::get('/', 'index');
            Route::get('/inicializar_tabla', 'inicializarTabla');
            Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas');
            Route::post('/records', 'records');
        });

        Route::get('/record/{id}', 'show');
        Route::post('/', 'store');
        Route::get('/record_destroy/{id}', 'recordDestroy');
        Route::post('/destroy', 'destroy');
        Route::get('/record_active/{id}', 'recordActive');
        Route::post('/change_active', 'changeActive');
        Route::post('/generar-paquete', 'generarPaquete');
        Route::get('/enviar-paquete/{id}', 'enviarPaquete');
        Route::get('/consultar-paquete/{id}', 'consultarPaquete');
    });

