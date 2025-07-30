<?php

use App\Http\Controllers\AppController;
use Modules\Farmacia\Http\Controllers\FarmaciaAlmacenController;
use Modules\Farmacia\Http\Controllers\FarmaciaController;
use Modules\Farmacia\Http\Controllers\NotaIngresoAlmacenController;
use Modules\Farmacia\Http\Controllers\NotaIngresoFarmaciaController;
use Modules\Farmacia\Http\Controllers\NotaSalidaAlmacenController;
use Modules\Farmacia\Http\Controllers\NotaSalidaFarmaciaController;

// Route::prefix('farmacias')
//     ->controller(FarmaciaController::class)
//     ->group(function () {
//         Route::get('/init_data_table', 'initTable');
//         Route::post('/update_visible_columns', 'updateVisibleColumns');
//         Route::post('/records', 'getRecords');
//         Route::get('/record/{id}', 'getRecord');
//         Route::post('/', 'store');
//         Route::get('/record_destroy/{id}', 'recordDestroy');
//         Route::post('/destroy', 'destroy');
//         Route::get('/record_active/{id}', 'recordActive');
//         Route::post('/change_active', 'changeActive');
//     });

// Route::prefix('farmacia_almacenes')
//     ->controller(FarmaciaAlmacenController::class)
//     ->group(function () {
//         Route::get('/init_data_table', 'initTable');
//         Route::post('/update_visible_columns', 'updateVisibleColumns');
//         Route::post('/records', 'getRecords');
//         Route::get('/record/{id}', 'getRecord');
//         Route::post('/', 'store');
//         Route::get('/record_destroy/{id}', 'recordDestroy');
//         Route::post('/destroy', 'destroy');
//         Route::get('/record_active/{id}', 'recordActive');
//         Route::post('/change_active', 'changeActive');
//     });

// //Route::prefix('seguridad/usuarios')
// //    ->middleware('auth')
// //    ->controller(UserController::class)
// //    ->name('seguridad.usuarios.')
// //    ->group(function () {
// //
// //        // Página principal
// //        Route::get('/', 'index');
// //
// //        // Registros y detalles
// //        Route::post('/records', 'records');
// //        Route::get('/record/{id}', 'show');
// //
// //        // Almacenar
// //        Route::post('/', 'store')->name('store');
// //
// //        // Eliminar
// //        Route::get('/record_destroy/{id}', 'recordDestroy');
// //        Route::post('/destroy', 'destroy');
// //
// //        // Cambio de Estado
// //        Route::get('/record_active/{id}', 'recordActive');
// //        Route::post('/change_active', 'changeActive');
// //
// //        // Tablas y columnas
// //        Route::get('/lista-tablas', 'listarTablas');
// //        Route::get('/inicializar_tabla', 'inicializarTabla');
// //        Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas'); // Actualizar columnas visibles
// //    });


// Route::prefix('farmacia/notas_ingresos_almacen')
//     ->controller(NotaIngresoAlmacenController::class)
//     ->group(function () {
//         Route::get('/inicializar_tabla', 'inicializarTabla');
//         Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas');
//         Route::post('/records', 'getRecords');
//         Route::get('/record/{id}', 'getRecord');
//         Route::post('/', 'store');
//         Route::get('/record_destroy/{id}', 'recordDestroy');
//         Route::post('/destroy', 'destroy');
//         Route::get('/record_active/{id}', 'recordActive');
//         Route::post('/change_active', 'changeActive');
//     });

// Route::prefix('farmacia/notas_salidas_almacen')
//     ->controller(NotaSalidaAlmacenController::class)
//     ->group(function () {
//         Route::get('/inicializar_tabla', 'inicializarTabla');
//         Route::post('/actualizar_visibilidad_columnas', 'actualizarVisibilidadColumnas');
//         Route::post('/records', 'getRecords');
//         Route::get('/record/{id}', 'getRecord');
//         Route::post('/', 'store');
//         Route::get('/record_destroy/{id}', 'recordDestroy');
//         Route::post('/destroy', 'destroy');
//         Route::get('/record_active/{id}', 'recordActive');
//         Route::post('/change_active', 'changeActive');

//     });

// Route::prefix('farmacia/notas_ingresos_farmacia')
//     ->controller(NotaIngresoFarmaciaController::class)
//     ->group(function () {
//         Route::get('/init_data_table', 'initTable');
//         Route::post('/update_visible_columns', 'updateVisibleColumns');
//         Route::post('/records', 'getRecords');
//         Route::get('/record/{id}', 'getRecord');
//         Route::post('/', 'store');
//         Route::get('/record_destroy/{id}', 'recordDestroy');
//         Route::post('/destroy', 'destroy');
//         Route::get('/record_active/{id}', 'recordActive');
//         Route::post('/change_active', 'changeActive');
//     });

// Route::prefix('farmacia/notas_salidas_farmacia')
//     ->controller(NotaSalidaFarmaciaController::class)
//     ->group(function () {
//         Route::get('/init_data_table', 'initTable');
//         Route::post('/update_visible_columns', 'updateVisibleColumns');
//         Route::post('/records', 'getRecords');
//         Route::get('/record/{id}', 'getRecord');
//         Route::post('/', 'store');
//         Route::get('/record_destroy/{id}', 'recordDestroy');
//         Route::post('/destroy', 'destroy');
//         Route::get('/record_active/{id}', 'recordActive');
//         Route::post('/change_active', 'changeActive');
//     });

// Route::controller(AppController::class)
//     ->group(function () {
//         Route::post('/filtrar_tipo_de_conceptos', 'filtrarTipoConceptos');
//         Route::post('/filtrar_tipo_de_conceptos_detalle', 'filtrarTipoConceptosDetalle');
//         Route::post('/filtrar_proveedores', 'filtrarProveedores');
//         Route::post('/filtrar_catalogo_bienes_insumos', 'filtrarCatalogoBienesInsumos');
//     });
