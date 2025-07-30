<?php

use Illuminate\Support\Facades\Route;
use Modules\Imagenologia\Http\Controllers\ListaEsperaController;

Route::prefix('imagenologia/lista-espera')
    ->middleware('auth')
    ->controller(ListaEsperaController::class)
    ->name('imagenologia.listaespera.')
    ->group(function () {

        Route::get('/', 'index')->name('index');

        // Registros y detalles
        Route::post('/records', 'records')->name('records');
        Route::get('/record/{id}', 'show')->name('show');

        // Almacenar
        Route::post('/', 'store')->name('store');

        // Eliminar
        Route::get('/record_destroy/{id}', 'recordDestroy')->name('record.destroy');
        Route::post('/destroy', 'destroy')->name('destroy');

        // Cambio de Estado
        Route::get('/record_active/{id}', 'recordActive')->name('record.active');
        Route::post('/change_active', 'changeActive')->name('change.active');

        // Configuración de tabla
        Route::get('/lista-tablas', 'listarTablas');
        Route::get('/inicializar_tabla', 'inicializarTabla')->name('inicializar_tabla');
        Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas')->name('actualizar_visibilidad_columnas'); // Actualizar columnas visibles

    });
