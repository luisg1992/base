<?php

use App\Helpers\ModuloHelper;
use Illuminate\Support\Facades\Route;
use Modules\Configuracion\Http\Controllers\FarmTipoConceptoController;

$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('configuracion/farm-tipo-concepto')
    ->middleware('auth')
    ->controller(FarmTipoConceptoController::class)
    ->as($as)
    ->group(function () use ($as) {
        Route::middleware("verificar.permisos:{$as}.acceder")->group(function () {
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
    });
