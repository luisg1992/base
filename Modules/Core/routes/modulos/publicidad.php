<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\PubController;

//$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::get('core/pub/visualizar',[PubController::class,  'visualizar']);
Route::get('core/pub/configuracion',[PubController::class,  'getConfiguracion']);

Route::prefix('core/pub')
    ->middleware('auth')
    ->controller(PubController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/inicializar_tabla', 'inicializarTabla');
        Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas');
        Route::post('/records', 'records');

        Route::get('/init_tables', 'initTable');
        Route::get('/record/{id}', 'show');
        Route::post('/', 'store');
        Route::get('/record_destroy/{id}', 'recordDestroy');
        Route::post('/destroy', 'destroy');
        Route::get('/record_active/{id}', 'recordActive');
        Route::post('/change_active', 'changeActive');
    });

