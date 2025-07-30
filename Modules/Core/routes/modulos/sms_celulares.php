<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\SmsCelularController;

//$as = ModuloHelper::obtenerPermisoBaseDesdeRuta();

Route::prefix('core/sms-celulares')
    ->middleware('auth')
    ->controller(SmsCelularController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/inicializar_tabla', 'inicializarTabla');
        Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas');
        Route::post('/records', 'records');

        Route::get('/record/{id}', 'show');
        Route::post('/', 'store');
        Route::get('/record_destroy/{id}', 'recordDestroy');
        Route::post('/destroy', 'destroy');
        Route::get('/record_active/{id}', 'recordActive');
        Route::post('/change_active', 'changeActive');
        Route::get('/enviar-sms/{id}', 'enviarSms');
    });

