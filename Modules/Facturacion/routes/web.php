<?php

require __DIR__ . '/modulos/cartas_garantia.php';
require __DIR__ . '/modulos/expedientes_judiciales.php';
/* use Illuminate\Support\Facades\Route;
use Modules\Facturacion\Http\Controllers\FacturacionController;

Route::prefix('$MODULE_PREFIX$/facturacion')
    ->middleware('auth')
    ->controller(FacturacionController::class)
    ->as('$MODULE_PREFIX$.facturacion.')
    ->group(function () {

        // Página principal del módulo
        Route::get('/', 'index')->name('index');

        // Registros y detalles
        Route::post('/records', 'records')->name('records');
        Route::get('/record/{id}', 'show')->name('show');

        // Almacenar nuevo registro
        Route::post('/', 'store')->name('store');

        // Eliminar registro
        Route::get('/record_destroy/{id}', 'recordDestroy')->name('record.destroy');
        Route::post('/destroy', 'destroy')->name('destroy');

        // Cambiar estado
        Route::get('/record_active/{id}', 'recordActive')->name('record.active');
        Route::post('/change_active', 'changeActive')->name('change.active');

        // Configuración de tabla
        Route::get('/inicializar_tabla', 'inicializarTabla')->name('inicializar_tabla');
        Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas')->name('actualizar_visibilidad_columnas'); // Actualizar columnas visibles
    }); */
