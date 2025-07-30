<?php

use Illuminate\Support\Facades\Route;
use Modules\Imagenologia\Http\Controllers\ProgramacionImagenologiaController;

Route::prefix('imagenologia/programacion-imagenologia')
    ->middleware('auth')
    ->controller(ProgramacionImagenologiaController::class)
    ->name('imagenologia.programacionimagenologia.')
    ->group(function () {

        Route::get('/', 'index')->name('index');

        Route::post('/FactPuntosCargaFiltrar', 'FactPuntosCargaFiltrar');
        Route::post('/WebS_ProgramacionImagenologia_Lista_BuscarFiltro', 'WebS_ProgramacionImagenologia_Lista_BuscarFiltro');
        Route::post('/WebS_InsertarProgramacionImagenologia', 'WebS_InsertarProgramacionImagenologia');

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
