<?php

use Illuminate\Support\Facades\Route;
use Modules\Auditoria\Http\Controllers\Login\LoginSessionController;


Route::prefix('auditoria/login')
    ->middleware('auth')
    ->controller(LoginSessionController::class)
    ->as('auditoria.')
    ->group(function () {

        // Página principal del módulo
        Route::get('/', 'index');

        // Rutas para obtener registros y detalles
        Route::post('/records', 'records')->name('records'); // Obtener los registros
        Route::get('/record/{id}', 'show')->name('show'); // Ver un detalle específico

        // Ruta para almacenar nuevos registros
        Route::post('/', 'store')->name('store'); // Crear un nuevo registro

        // Ruta para eliminar un registro
        // Route::post('/{id}', 'destroy'); // Eliminar un registro específico

        // Ruta para obtener la lista de tablas
        Route::get('/lista-tablas', 'listarTablas')->name('listarTablas');  // Obtener lista de tablas

        // Rutas para inicializar la tabla de datos y actualizar las columnas visibles
        Route::get('/inicializar_tabla', 'inicializarTabla')->name('inicializarTabla'); // Inicializar la tabla
        Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas')->name('actualizarVisibilidadColumnas'); // Actualizar columnas visibles
    });
