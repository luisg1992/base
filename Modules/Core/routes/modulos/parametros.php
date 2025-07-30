<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\ParametroController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('core/parametros')
    ->middleware('auth')
    ->controller(ParametroController::class)
    ->as($as)
    ->group(function () use ($as) {
        Route::middleware("verificar.permisos:{$as}.acceder")->group(function () use ($as) {
            Route::get('/', 'index')->name($as.'.index');
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
        Route::get('/obtener_servicios_consultar', 'obtenerServiciosConsultar');
        Route::post('/actualizar', 'actualizarParametro');
    });
